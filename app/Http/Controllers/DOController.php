<?php

namespace App\Http\Controllers;

use App\Models\DeliveryOrder;
use App\Models\DOItem;
use App\Models\Vendor;
use App\Models\VendorDB;
use App\Models\AuditLog;
use App\Models\User;
use App\Services\NotificationService;
use App\Services\VendorNotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DOController extends Controller
{
    protected $notificationService;
    protected $vendorNotificationService;

    public function __construct(NotificationService $notificationService, VendorNotificationService $vendorNotificationService)
    {
        $this->notificationService = $notificationService;
        $this->vendorNotificationService = $vendorNotificationService;
    }

    // Helper to get vendor ID from either guard
    private function getVendorId()
    {
        if (Auth::guard('vendor')->check()) {
            $vendorUser = Auth::guard('vendor')->user();
            $vendor = $vendorUser->vendor;
            return $vendor->SUPPLIERID ?? null;
        }
        
        if (Auth::check() && Auth::user()->isVendor()) {
            return Auth::user()->vendor_id;
        }
        
        return null;
    }

    // Helper to get vendor name from either guard
    private function getVendorName()
    {
        if (Auth::guard('vendor')->check()) {
            $vendorUser = Auth::guard('vendor')->user();
            return $vendorUser->vendor->SUPPLIER_COMP_NAME ?? $vendorUser->name;
        }
        
        if (Auth::check() && Auth::user()->isVendor()) {
            $vendor = Vendor::where('supplierid', Auth::user()->vendor_id)->first();
            return $vendor->supplier_comp_name ?? Auth::user()->name;
        }
        
        return 'Unknown Vendor';
    }

    public function index()
    {
        $vendorId = $this->getVendorId();
        
        if ($vendorId) {
            // Vendor view - only show their DOs
            $dos = DeliveryOrder::where('vendor_id', $vendorId)
                ->orderBy('created_at', 'desc')
                ->get();
        } else {
            // Officer/Admin view - show all DOs
            $dos = DeliveryOrder::orderBy('created_at', 'desc')->get();
        }
        
        return view('do.index', compact('dos'));
    }

    public function create()
    {
        // Check if user is a vendor (either guard)
        $vendorId = $this->getVendorId();
        
        if (!$vendorId) {
            return redirect()->route('dashboard')->with('error', 'Only vendors can create DO.');
        }
        
        // Get vendor data for display
        $vendor = null;
        if (Auth::guard('vendor')->check()) {
            $vendor = Auth::guard('vendor')->user()->vendor;
        } else {
            $vendor = Vendor::where('supplierid', $vendorId)->first();
        }
        
        return view('do.create', compact('vendor'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'do_number' => 'required|string|unique:delivery_orders,do_number',
            'po_number' => 'required|string',
            'order_date' => 'required|date',
            'shipping_address' => 'required|string',
            'invoice_address' => 'required|string',
            'delivery_date' => 'required|date',
            'delivery_time' => 'required',
            'remarks' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.item_no' => 'required|string',
            'items.*.description' => 'required|string',
            'items.*.quantity' => 'required|integer|min:1',
        ]);

        DB::beginTransaction();

        try {
            $vendorId = $this->getVendorId();
            $vendorName = $this->getVendorName();
            
            if (!$vendorId) {
                return redirect()->back()->with('error', 'Vendor not found.');
            }

            Log::info('Creating DO with vendor_id: ' . $vendorId);

            $do = DeliveryOrder::create([
                'do_number' => $request->do_number,
                'po_number' => $request->po_number,
                'vendor_id' => $vendorId,
                'order_date' => $request->order_date,
                'shipping_address' => $request->shipping_address,
                'invoice_address' => $request->invoice_address,
                'delivery_date' => $request->delivery_date,
                'delivery_time' => $request->delivery_time,
                'remarks' => $request->remarks,
                'status' => 'Submitted'
            ]);

            foreach ($request->items as $item) {
                DOItem::create([
                    'do_id' => $do->do_id,
                    'item_no' => $item['item_no'],
                    'description' => $item['description'],
                    'quantity' => $item['quantity']
                ]);
            }

            // Send notification to officers
            $officers = User::where('role', 'review_officer')->get();
            foreach ($officers as $officer) {
                $this->notificationService->send(
                    $officer->id,
                    'New DO Pending Review',
                    'Vendor ' . $vendorName . ' has submitted DO ' . $do->do_number . ' for review.',
                    'warning',
                    route('review.do.show', $do->do_id)
                );
            }

            // Send notification to vendor
            $this->vendorNotificationService->sendToVendorBySupplierId(
                $vendorId,
                'DO Submitted Successfully',
                'Your Delivery Order ' . $do->do_number . ' has been submitted for review. You will be notified when it is approved.',
                'success',
                route('do.show', $do->do_id)
            );

            AuditLog::log(
                'DO',
                'CREATE',
                "DO {$do->do_number} created and submitted by vendor {$vendorName}",
                'Success'
            );

            DB::commit();

            return redirect()->route('do.index')
                ->with('success', 'Delivery Order created and submitted for review successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to create DO: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Failed to create DO: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function show($id)
    {
        $do = DeliveryOrder::with(['vendor', 'vendorExternal', 'items'])->findOrFail($id);
        
        // Check authorization - only vendor who owns it or officer/admin can view
        $vendorId = $this->getVendorId();
        if ($vendorId && $do->vendor_id != $vendorId) {
            return redirect()->route('do.index')->with('error', 'Unauthorized access.');
        }
        
        return view('do.show', compact('do'));
    }

    public function edit($id)
    {
        $do = DeliveryOrder::with(['vendor', 'items'])->findOrFail($id);
        
        $vendorId = $this->getVendorId();
        if (!$vendorId || $do->vendor_id != $vendorId) {
            return redirect()->route('dashboard')->with('error', 'Unauthorized access.');
        }
        
        if (!in_array($do->status, ['Draft', 'Submitted', 'Rejected'])) {
            return redirect()->route('do.show', $do->do_id)
                ->with('error', 'This DO cannot be edited.');
        }
        
        return view('do.edit', compact('do'));
    }

    public function update(Request $request, $id)
    {
        $do = DeliveryOrder::findOrFail($id);
        
        $vendorId = $this->getVendorId();
        if (!$vendorId || $do->vendor_id != $vendorId) {
            return redirect()->route('dashboard')->with('error', 'Unauthorized access.');
        }
        
        if (!in_array($do->status, ['Draft', 'Submitted', 'Rejected'])) {
            return redirect()->route('do.show', $do->do_id)
                ->with('error', 'This DO cannot be edited.');
        }

        $request->validate([
            'do_number' => 'required|string|unique:delivery_orders,do_number,' . $id . ',do_id',
            'po_number' => 'required|string',
            'order_date' => 'required|date',
            'shipping_address' => 'required|string',
            'invoice_address' => 'required|string',
            'delivery_date' => 'required|date',
            'delivery_time' => 'required',
            'remarks' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.item_no' => 'required|string',
            'items.*.description' => 'required|string',
            'items.*.quantity' => 'required|integer|min:1',
        ]);

        DB::beginTransaction();

        try {
            $vendorName = $this->getVendorName();
            
            $do->update([
                'do_number' => $request->do_number,
                'po_number' => $request->po_number,
                'order_date' => $request->order_date,
                'shipping_address' => $request->shipping_address,
                'invoice_address' => $request->invoice_address,
                'delivery_date' => $request->delivery_date,
                'delivery_time' => $request->delivery_time,
                'remarks' => $request->remarks,
                'status' => 'Submitted'
            ]);

            $do->items()->delete();
            foreach ($request->items as $item) {
                DOItem::create([
                    'do_id' => $do->do_id,
                    'item_no' => $item['item_no'],
                    'description' => $item['description'],
                    'quantity' => $item['quantity']
                ]);
            }

            // Send notification to officers
            $officers = User::where('role', 'review_officer')->get();
            foreach ($officers as $officer) {
                $this->notificationService->send(
                    $officer->id,
                    'DO Updated - Pending Review',
                    'Vendor ' . $vendorName . ' has updated DO ' . $do->do_number . ' and resubmitted for review.',
                    'warning',
                    route('review.do.show', $do->do_id)
                );
            }

            AuditLog::log(
                'DO',
                'UPDATE',
                "DO {$do->do_number} updated and resubmitted by vendor {$vendorName}",
                'Success'
            );

            DB::commit();

            return redirect()->route('do.show', $do->do_id)
                ->with('success', 'Delivery Order updated and submitted for review!');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Failed to update DO: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function submit($id)
    {
        $do = DeliveryOrder::findOrFail($id);
        
        $vendorId = $this->getVendorId();
        if (!$vendorId || $do->vendor_id != $vendorId) {
            return redirect()->back()->with('error', 'Unauthorized access.');
        }

        if (!in_array($do->status, ['Draft', 'Rejected'])) {
            return redirect()->back()->with('error', 'This DO cannot be submitted.');
        }

        $do->status = 'Submitted';
        $do->save();

        // Send notification to officers
        $officers = User::where('role', 'review_officer')->get();
        foreach ($officers as $officer) {
            $this->notificationService->send(
                $officer->id,
                'New DO Pending Review',
                'Vendor has submitted DO ' . $do->do_number . ' for review.',
                'warning',
                route('review.do.show', $do->do_id)
            );
        }

        AuditLog::log(
            'DO',
            'SUBMIT',
            "DO {$do->do_number} submitted by vendor",
            'Success'
        );

        return redirect()->route('do.show', $do->do_id)
            ->with('success', "DO {$do->do_number} submitted for review.");
    }

    public function destroy($id)
    {
        $do = DeliveryOrder::findOrFail($id);
        
        $vendorId = $this->getVendorId();
        $isVendor = $vendorId && $do->vendor_id == $vendorId;
        $isAdmin = Auth::check() && Auth::user()->isITOfficer();
        
        if ($isVendor) {
            if (!in_array($do->status, ['Draft', 'Submitted', 'Rejected'])) {
                return redirect()->back()->with('error', 'Cannot delete this DO.');
            }
        } else if (!$isAdmin) {
            return redirect()->route('dashboard')->with('error', 'Unauthorized access.');
        }

        DB::beginTransaction();

        try {
            $do->items()->delete();
            
            if ($do->invoice) {
                return redirect()->back()->with('error', 'Cannot delete DO that has an invoice.');
            }
            
            $do->delete();

            AuditLog::log(
                'DO',
                'DELETE',
                "DO {$do->do_number} deleted",
                'Success'
            );

            DB::commit();

            return redirect()->route('do.index')
                ->with('success', 'Delivery Order deleted successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Failed to delete DO: ' . $e->getMessage());
        }
    }

    public function approve($id)
    {
        $do = DeliveryOrder::findOrFail($id);
        
        if (!Auth::check() || !Auth::user()->isOfficer()) {
            return redirect()->back()->with('error', 'Unauthorized access.');
        }

        if ($do->status !== 'Submitted') {
            return redirect()->back()->with('error', 'This DO cannot be approved.');
        }

        $do->status = 'Approved';
        $do->save();

        // Send notification to vendor
        $this->vendorNotificationService->sendToVendorBySupplierId(
            $do->vendor_id,
            'DO Approved',
            'Your Delivery Order ' . $do->do_number . ' has been approved. You can now submit an invoice.',
            'success',
            route('do.show', $do->do_id)
        );

        AuditLog::log(
            'DO',
            'APPROVE',
            "DO {$do->do_number} approved by " . Auth::user()->name,
            'Success'
        );

        return redirect()->route('do.index')
            ->with('success', "DO {$do->do_number} approved successfully.");
    }

    public function reject(Request $request, $id)
    {
        $request->validate([
            'reason' => 'required|string|min:5',
        ]);

        $do = DeliveryOrder::findOrFail($id);
        
        if (!Auth::check() || !Auth::user()->isOfficer()) {
            return redirect()->back()->with('error', 'Unauthorized access.');
        }

        if ($do->status !== 'Submitted') {
            return redirect()->back()->with('error', 'This DO cannot be rejected.');
        }

        $do->status = 'Rejected';
        $do->reason = $request->reason;
        $do->save();

        // Send notification to vendor
        $this->vendorNotificationService->sendToVendorBySupplierId(
            $do->vendor_id,
            'DO Rejected',
            'Your Delivery Order ' . $do->do_number . ' has been rejected. Reason: ' . $request->reason,
            'danger',
            route('do.show', $do->do_id)
        );

        AuditLog::log(
            'DO',
            'REJECT',
            "DO {$do->do_number} rejected by " . Auth::user()->name . ". Reason: {$request->reason}",
            'Success'
        );

        return redirect()->route('do.index')
            ->with('info', "DO {$do->do_number} has been rejected.");
    }

    public function getItems($id)
    {
        $items = DOItem::where('do_id', $id)->get();
        return response()->json($items);
    }
}