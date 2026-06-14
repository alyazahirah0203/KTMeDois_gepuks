<?php

namespace App\Http\Controllers;

use App\Models\DeliveryOrder;
use App\Models\Invoice;
use App\Models\Vendor;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $user = Auth::user();

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
                ->limit(5)
                ->get();
            
            return view('dashboard.vendor', compact('deliveryOrders', 'invoices', 'pendingInvoices', 'recentInvoices'));
        }

        if ($user->isOfficer()) {
            $pendingReviews = Invoice::where('status', 'Submitted')->count();
            $totalInvoices = Invoice::count();
            $totalPaid = Invoice::where('status', 'Paid')->sum('total');
            $pendingInvoicesList = Invoice::where('status', 'Submitted')
                ->with('vendor')
                ->orderBy('created_at', 'desc')
                ->get();
            
            return view('dashboard.officer', compact('pendingReviews', 'totalInvoices', 'totalPaid', 'pendingInvoicesList'));
        }

        if ($user->isAdmin()) {
            $totalVendors = Vendor::count();
            $totalInvoices = Invoice::count();
            $totalAmount = Invoice::sum('total');
            $totalUsers = User::count();
            
            return view('dashboard.admin', compact('totalVendors', 'totalInvoices', 'totalAmount', 'totalUsers'));
        }

        return view('home');
    }
}