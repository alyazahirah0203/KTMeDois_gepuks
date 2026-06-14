<?php

namespace App\Http\Controllers;

use App\Models\DeliveryOrder;
use App\Models\Invoice;
use App\Models\User;
use App\Services\InvoiceSubmissionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class InvoiceController extends Controller
{
    protected $invoiceSubmission;

    public function __construct(InvoiceSubmissionService $invoiceSubmission)
    {
        $this->middleware('auth');
        $this->invoiceSubmission = $invoiceSubmission;
    }

    public function create()
    {
        $user = Auth::user();
        
        if (!$user->isVendor()) {
            return redirect()->route('dashboard')->with('error', 'Only vendors can submit invoices.');
        }

        $deliveryOrders = DeliveryOrder::where('vendor_id', $user->vendor_id)
            ->where('status', 'Approved')
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
        
        $invoice = $this->invoiceSubmission->submit(
            $request->all(),
            $do,
            $request->items,
            $request->file('proof_of_delivery')
        );

        return redirect()->route('invoices.show', $invoice->invoice_id)
            ->with('success', 'Invoice submitted successfully!');
    }

    public function show($id)
    {
        $invoice = Invoice::with(['items', 'payments', 'deliveryOrder', 'vendor'])->findOrFail($id);
        return view('invoices.show', compact('invoice'));
    }

    public function track(Request $request)
    {
        $invoice = null;
        if ($request->invoice_no) {
            $invoice = Invoice::with(['items', 'payments', 'deliveryOrder', 'vendor'])
                ->where('invoice_no', $request->invoice_no)
                ->first();
        }
        return view('invoices.track', compact('invoice'));
    }

    public function download($id)
    {
        $invoice = Invoice::findOrFail($id);
        
        if ($invoice->pdf_path) {
            $fullPath = storage_path("app/public/" . $invoice->pdf_path);
            if (file_exists($fullPath)) {
                $safeFilename = str_replace('/', '_', $invoice->invoice_no);
                return response()->download($fullPath, $safeFilename . '.pdf');
            }
        }
        
        $safeFilename = str_replace('/', '_', $invoice->invoice_no);
        $path = storage_path("app/public/invoices/invoice_{$safeFilename}.pdf");
        
        if (!file_exists($path)) {
            abort(404, 'PDF file not found at: ' . $path);
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