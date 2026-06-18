<?php

namespace App\Http\Controllers;

use App\Models\DeliveryOrder;
use App\Models\Invoice;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class HomeController extends Controller
{
    public function __construct()
    {
        // Only apply auth middleware to index
        $this->middleware('auth')->only('index');
    }

    public function index()
    {
        $user = Auth::user();

        // If user is not logged in, redirect to login
        if (!$user) {
            return redirect()->route('login');
        }

        // IT Officer → Admin Dashboard
        if ($user->isITOfficer()) {
            return redirect()->route('dashboard.admin');
        }

        // Vendor → Vendor Dashboard (from main DB - legacy)
        if ($user->isVendor()) {
            $deliveryOrders = DeliveryOrder::where('vendor_id', $user->vendor_id)
                ->where('status', 'Approved')
                ->count();
            
            $invoices = Invoice::where('vendor_id', $user->vendor_id)->count();
            
            $pendingInvoices = Invoice::where('vendor_id', $user->vendor_id)
                ->where('status', '!=', 'Paid')
                ->count();
            
            $recentInvoices = Invoice::where('vendor_id', $user->vendor_id)
                ->orderBy('created_at', 'desc')
                ->limit(10)
                ->get();
            
            return view('dashboard.vendor', compact('deliveryOrders', 'invoices', 'pendingInvoices', 'recentInvoices'));
        }

        // Officer (Review or Finance) → Review Dashboard
        if ($user->isReviewOfficer() || $user->isFinanceOfficer()) {
            return redirect()->route('review.index');
        }

        // Fallback
        return view('home');
    }

    // Vendor Dashboard for external database vendors
    public function vendorDashboard()
    {
        Log::info('Vendor Dashboard accessed', [
            'vendor_guard_check' => Auth::guard('vendor')->check(),
            'session_id' => session()->getId(),
        ]);

        // Check if vendor is authenticated from external DB
        if (!Auth::guard('vendor')->check()) {
            Log::warning('Vendor not authenticated, redirecting to login');
            return redirect()->route('login')->with('error', 'Please login as vendor.');
        }

        $vendorUser = Auth::guard('vendor')->user();
        Log::info('Vendor user found', [
            'id' => $vendorUser->id,
            'email' => $vendorUser->email,
            'name' => $vendorUser->name,
        ]);

        $vendor = $vendorUser->vendor;
        
        if (!$vendor) {
            Log::error('Vendor company not found for vendor user', ['vendor_user_id' => $vendorUser->id]);
            return redirect()->route('login')->with('error', 'Vendor company not found. Please contact administrator.');
        }
        
        // Get SUPPLIERID from the vendor object
        $supplierId = $vendor->SUPPLIERID ?? null;
        
        // If SUPPLIERID is null, try to get from attributes
        if (!$supplierId) {
            $attributes = $vendor->getAttributes();
            $supplierId = $attributes['SUPPLIERID'] ?? null;
        }
        
        // If still null, show error
        if (!$supplierId) {
            Log::error('SUPPLIERID not found for vendor', ['vendor_id' => $vendor->id]);
            return redirect()->route('login')->with('error', 'Vendor ID not found. Please contact administrator.');
        }

        Log::info('Vendor dashboard data', [
            'supplierId' => $supplierId,
            'vendor_name' => $vendor->SUPPLIER_COMP_NAME ?? 'Unknown',
        ]);
        
        $deliveryOrders = DeliveryOrder::where('vendor_id', $supplierId)
            ->where('status', 'Approved')
            ->count();
        
        $invoices = Invoice::where('vendor_id', $supplierId)->count();
        
        $pendingInvoices = Invoice::where('vendor_id', $supplierId)
            ->where('status', '!=', 'Paid')
            ->count();
        
        $recentInvoices = Invoice::where('vendor_id', $supplierId)
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();
        
        return view('dashboard.vendor', compact(
            'deliveryOrders', 
            'invoices', 
            'pendingInvoices', 
            'recentInvoices'
        ));
    }
}