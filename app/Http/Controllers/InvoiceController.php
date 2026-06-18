<?php

namespace App\Http\Controllers;

use App\Models\DeliveryOrder;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Vendor;
use App\Models\VendorDB;
use App\Models\User;
use App\Services\InvoiceSubmissionService;
use App\Services\NotificationService;
use App\Services\VendorNotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\AuditLog;

class InvoiceController extends Controller
{
    protected $invoiceSubmission;
    protected $notificationService;
    protected $vendorNotificationService;

    public function __construct(
        InvoiceSubmissionService $invoiceSubmission,
        NotificationService $notificationService,
        VendorNotificationService $vendorNotificationService
    ) {
        $this->invoiceSubmission = $invoiceSubmission;
        $this->notificationService = $notificationService;
        $this->vendorNotificationService = $vendorNotificationService;
    }

    // Helper to get vendor ID from either guard
    private function getVendorId()
    {
        if (Auth::guard('vendor')->check()) {
            $vendorUser = Auth::guard('vendor')->user();
            return $vendorUser->vendor->SUPPLIERID ?? null;
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

    public function create()
    {
        $vendorId = $this->getVendorId();
        
        if (!$vendorId) {
            return redirect()->route('dashboard')->with('error', 'Only vendors can submit invoices.');
        }

        $deliveryOrders = DeliveryOrder::where('vendor_id', $vendorId)
            ->where('status', 'Approved')
            ->whereDoesntHave('invoice')
            ->get();
        
        return view('invoices.create', compact('deliveryOrders'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'do_id' => 'required|exists:delivery_orders,do_id',
            'invoice_date' => 'required|date',
            'credit_note' => 'nullable|numeric|min:0',
            'proof_of_delivery' => 'required|file|mimes:pdf,jpg,png|max:5120',
            'items' => 'required|array|min:1',
            'items.*.product_code' => 'required|string',
            'items.*.description' => 'required|string',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
        ]);

        $do = DeliveryOrder::findOrFail($request->do_id);
        
        // Verify vendor owns this DO
        $vendorId = $this->getVendorId();
        if (!$vendorId || $do->vendor_id != $vendorId) {
            return redirect()->back()->with('error', 'Unauthorized access.');
        }
        
        $invoice = $this->invoiceSubmission->submit(
            $request->all(),
            $do,
            $request->items,
            $request->file('proof_of_delivery')
        );

        // Send notification to Review Officers
        $reviewOfficers = User::where('role', 'review_officer')->get();
        foreach ($reviewOfficers as $officer) {
            $this->notificationService->send(
                $officer->id,
                'New Invoice Pending Review',
                'Invoice ' . $invoice->invoice_no . ' from vendor ' . ($this->getVendorName()) . ' is pending review.',
                'warning',
                route('review.invoices')
            );
        }

        // Send notification to vendor (confirmation)
        $this->vendorNotificationService->sendToVendorBySupplierId(
            $vendorId,
            'Invoice Submitted Successfully',
            'Your invoice ' . $invoice->invoice_no . ' has been submitted successfully. Please wait for review.',
            'success',
            route('invoices.show', $invoice->invoice_id)
        );

        return redirect()->route('invoices.show', $invoice->invoice_id)
            ->with('success', 'Invoice submitted successfully!');
    }

    public function show($id)
    {
        $invoice = Invoice::with(['items', 'payments', 'deliveryOrder', 'vendor', 'vendorExternal'])->findOrFail($id);
        
        // Check authorization
        $vendorId = $this->getVendorId();
        if ($vendorId && $invoice->vendor_id != $vendorId) {
            return redirect()->route('dashboard')->with('error', 'Unauthorized access.');
        }
        
        return view('invoices.show', compact('invoice'));
    }

    public function track(Request $request)
    {
        $invoice = null;
        if ($request->invoice_no) {
            $invoice = Invoice::with(['items', 'payments', 'deliveryOrder', 'vendor', 'vendorExternal'])
                ->where('invoice_no', $request->invoice_no)
                ->first();
        }
        return view('invoices.track', compact('invoice'));
    }

    public function edit($id)
    {
        $invoice = Invoice::with(['items', 'deliveryOrder', 'vendor'])->findOrFail($id);
        
        $vendorId = $this->getVendorId();
        if (!$vendorId || $invoice->vendor_id != $vendorId) {
            return redirect()->route('dashboard')->with('error', 'Unauthorized access.');
        }
        
        if (!in_array($invoice->status, ['Submitted', 'Rejected'])) {
            return redirect()->route('invoices.show', $invoice->invoice_id)
                ->with('error', 'This invoice cannot be edited.');
        }
        
        $deliveryOrders = DeliveryOrder::where('vendor_id', $vendorId)
            ->where('status', 'Approved')
            ->get();
        
        return view('invoices.edit', compact('invoice', 'deliveryOrders'));
    }

    public function update(Request $request, $id)
    {
        $invoice = Invoice::findOrFail($id);
        
        $vendorId = $this->getVendorId();
        if (!$vendorId || $invoice->vendor_id != $vendorId) {
            return redirect()->route('dashboard')->with('error', 'Unauthorized access.');
        }
        
        if (!in_array($invoice->status, ['Submitted', 'Rejected'])) {
            return redirect()->route('invoices.show', $invoice->invoice_id)
                ->with('error', 'This invoice cannot be edited.');
        }

        $request->validate([
            'do_id' => 'required|exists:delivery_orders,do_id',
            'invoice_date' => 'required|date',
            'credit_note' => 'nullable|numeric|min:0',
            'items' => 'required|array|min:1',
            'items.*.product_code' => 'required|string',
            'items.*.description' => 'required|string',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
        ]);

        DB::beginTransaction();

        try {
            $do = DeliveryOrder::findOrFail($request->do_id);
            
            $lineTotal = 0;
            foreach ($request->items as $item) {
                $lineTotal += $item['quantity'] * $item['unit_price'];
            }

            $creditNote = $request->credit_note ?? 0;
            $isLate = $this->invoiceSubmission->calculationService->isLateDelivery($do->delivery_date);
            $calculation = $this->invoiceSubmission->calculationService->calculate($lineTotal, $creditNote, $isLate);

            $invoice->update([
                'do_id' => $request->do_id,
                'invoice_date' => $request->invoice_date,
                'line_total' => $calculation['line_total'],
                'service_tax' => $calculation['tax'],
                'discount' => $calculation['discount'],
                'penalty' => $calculation['penalty'],
                'total' => $calculation['total'],
                'credits' => $creditNote,
                'balance_due' => $calculation['balance_due'],
                'status' => 'Submitted'
            ]);

            $invoice->items()->delete();
            foreach ($request->items as $item) {
                InvoiceItem::create([
                    'invoice_id' => $invoice->invoice_id,
                    'product_code' => $item['product_code'],
                    'description' => $item['description'],
                    'uom' => $item['uom'] ?? 'EA',
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'amount' => $item['quantity'] * $item['unit_price']
                ]);
            }

            $this->invoiceSubmission->generateInvoicePDF($invoice);

            AuditLog::log(
                'Invoice',
                'UPDATE',
                "Invoice {$invoice->invoice_no} updated by vendor",
                'Success'
            );

            // Send notification to Review Officers about update
            $reviewOfficers = User::where('role', 'review_officer')->get();
            foreach ($reviewOfficers as $officer) {
                $this->notificationService->send(
                    $officer->id,
                    'Invoice Updated - Pending Review',
                    'Invoice ' . $invoice->invoice_no . ' from vendor ' . ($this->getVendorName()) . ' has been updated and is pending review.',
                    'warning',
                    route('review.invoices')
                );
            }

            DB::commit();

            return redirect()->route('invoices.show', $invoice->invoice_id)
                ->with('success', 'Invoice updated successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Failed to update invoice: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function destroy($id)
    {
        $invoice = Invoice::findOrFail($id);
        
        $vendorId = $this->getVendorId();
        $isVendor = $vendorId && $invoice->vendor_id == $vendorId;
        $isAdmin = Auth::check() && Auth::user()->isITOfficer();
        
        if ($isVendor) {
            if (!in_array($invoice->status, ['Submitted', 'Rejected'])) {
                return redirect()->back()->with('error', 'Cannot delete this invoice.');
            }
        } else if (!$isAdmin) {
            return redirect()->route('dashboard')->with('error', 'Unauthorized access.');
        }

        DB::beginTransaction();

        try {
            $invoice->items()->delete();
            
            if ($invoice->payment) {
                $invoice->payment->delete();
            }
            
            $invoice->delete();

            AuditLog::log(
                'Invoice',
                'DELETE',
                "Invoice {$invoice->invoice_no} deleted",
                'Success'
            );

            DB::commit();

            return redirect()->route('dashboard')
                ->with('success', 'Invoice deleted successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Failed to delete invoice: ' . $e->getMessage());
        }
    }

    public function download($id)
    {
        $invoice = Invoice::findOrFail($id);
        
        $safeFilename = str_replace('/', '_', $invoice->invoice_no);
        $path = storage_path("app/public/invoices/invoice_{$invoice->invoice_no}.pdf");
        
        if (!file_exists($path)) {
            $path = storage_path("app/public/invoices/invoice_" . str_replace('/', '_', $invoice->invoice_no) . ".pdf");
        }
        
        if (!file_exists($path)) {
            abort(404, 'PDF file not found.');
        }
        
        return response()->download($path, $safeFilename . '.pdf');
    }

    public function getStatus($id)
    {
        $invoice = Invoice::findOrFail($id);
        return response()->json([
            'status' => $invoice->status,
            'total' => $invoice->total,
            'balance_due' => $invoice->balance_due
        ]);
    }
}