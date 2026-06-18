<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\DeliveryOrder;
use App\Models\Payment;
use App\Models\AuditLog;
use App\Models\User;
use App\Models\VendorUser;
use App\Services\InvoiceSubmissionService;
use App\Services\NotificationService;
use App\Services\VendorNotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;

class ReviewController extends Controller
{
    protected $invoiceSubmission;
    protected $notificationService;
    protected $vendorNotificationService;

    public function __construct(
        InvoiceSubmissionService $invoiceSubmission, 
        NotificationService $notificationService,
        VendorNotificationService $vendorNotificationService
    ) {
        $this->middleware('auth');
        $this->invoiceSubmission = $invoiceSubmission;
        $this->notificationService = $notificationService;
        $this->vendorNotificationService = $vendorNotificationService;
    }

    // =============================================
    // DASHBOARD
    // =============================================

    public function index()
    {
        $user = Auth::user();
        
        if (!$user->isOfficer()) {
            return redirect()->route('dashboard')->with('error', 'Only officers can access this page.');
        }

        $pendingInvoices = Invoice::where('status', 'Submitted')
            ->with(['vendor', 'vendorExternal', 'deliveryOrder'])
            ->orderBy('created_at', 'asc')
            ->get();

        $reviewingInvoices = Invoice::where('status', 'Finance Review')
            ->with(['vendor', 'vendorExternal', 'deliveryOrder'])
            ->orderBy('created_at', 'asc')
            ->get();

        $processingInvoices = Invoice::where('status', 'Payment Processing')
            ->with(['vendor', 'vendorExternal', 'deliveryOrder'])
            ->orderBy('created_at', 'asc')
            ->get();

        $paidInvoices = Invoice::where('status', 'Paid')
            ->with(['vendor', 'vendorExternal', 'deliveryOrder'])
            ->orderBy('created_at', 'desc')
            ->limit(20)
            ->get();

        $pendingDOs = DeliveryOrder::where('status', 'Submitted')->count();
        $approvedDOs = DeliveryOrder::where('status', 'Approved')->count();
        $rejectedDOs = DeliveryOrder::where('status', 'Rejected')->count();

        $statusData = [
            'Submitted' => Invoice::where('status', 'Submitted')->count(),
            'Finance Review' => Invoice::where('status', 'Finance Review')->count(),
            'Payment Processing' => Invoice::where('status', 'Payment Processing')->count(),
            'Paid' => Invoice::where('status', 'Paid')->count(),
        ];

        $monthlyData = Invoice::select(
            DB::raw('DATE_FORMAT(created_at, "%Y-%m") as month'),
            DB::raw('COUNT(*) as total'),
            DB::raw('SUM(total) as amount'),
            DB::raw('SUM(CASE WHEN status = "Paid" THEN total ELSE 0 END) as paid_amount')
        )
        ->groupBy('month')
        ->orderBy('month', 'desc')
        ->limit(12)
        ->get();

        $topVendors = Invoice::with('vendor')
            ->select('vendor_id', DB::raw('COUNT(*) as total_invoices'), DB::raw('SUM(total) as total_amount'))
            ->where('status', 'Paid')
            ->groupBy('vendor_id')
            ->orderBy('total_amount', 'desc')
            ->limit(10)
            ->get();

        return view('review.index', compact(
            'pendingInvoices',
            'reviewingInvoices',
            'processingInvoices',
            'paidInvoices',
            'pendingDOs',
            'approvedDOs',
            'rejectedDOs',
            'statusData',
            'monthlyData',
            'topVendors'
        ));
    }

    public function invoices()
    {
        $user = Auth::user();
        
        if (!$user->isOfficer()) {
            return redirect()->route('dashboard')->with('error', 'Only officers can access this page.');
        }

        $pendingInvoices = Invoice::where('status', 'Submitted')
            ->with(['vendor', 'vendorExternal', 'deliveryOrder'])
            ->orderBy('created_at', 'asc')
            ->get();

        $reviewingInvoices = Invoice::where('status', 'Finance Review')
            ->with(['vendor', 'vendorExternal', 'deliveryOrder'])
            ->orderBy('created_at', 'asc')
            ->get();

        $processingInvoices = Invoice::where('status', 'Payment Processing')
            ->with(['vendor', 'vendorExternal', 'deliveryOrder'])
            ->orderBy('created_at', 'asc')
            ->get();

        $paidInvoices = Invoice::where('status', 'Paid')
            ->with(['vendor', 'vendorExternal', 'deliveryOrder'])
            ->orderBy('created_at', 'desc')
            ->limit(20)
            ->get();

        $rejectedInvoices = Invoice::where('status', 'Rejected')
            ->with(['vendor', 'vendorExternal', 'deliveryOrder'])
            ->orderBy('created_at', 'desc')
            ->limit(20)
            ->get();

        return view('review.invoices', compact(
            'pendingInvoices',
            'reviewingInvoices',
            'processingInvoices',
            'paidInvoices',
            'rejectedInvoices'
        ));
    }

    public function showInvoice($id)
    {
        $invoice = Invoice::with(['vendor', 'vendorExternal', 'deliveryOrder', 'items', 'payment'])
            ->findOrFail($id);
        
        return view('review.show', compact('invoice'));
    }

    // =============================================
    // DO REVIEW METHODS
    // =============================================

    public function dos()
    {
        $user = Auth::user();
        
        if (!$user->isOfficer()) {
            return redirect()->route('dashboard')->with('error', 'Only officers can access this page.');
        }

        $pendingDOs = DeliveryOrder::where('status', 'Submitted')
            ->with(['vendor', 'vendorExternal', 'items'])
            ->orderBy('created_at', 'asc')
            ->get();

        $approvedDOs = DeliveryOrder::where('status', 'Approved')
            ->with(['vendor', 'vendorExternal', 'items'])
            ->orderBy('created_at', 'desc')
            ->limit(20)
            ->get();

        $rejectedDOs = DeliveryOrder::where('status', 'Rejected')
            ->with(['vendor', 'vendorExternal', 'items'])
            ->orderBy('created_at', 'desc')
            ->limit(20)
            ->get();

        return view('review.dos', compact('pendingDOs', 'approvedDOs', 'rejectedDOs'));
    }

    public function showDO($id)
    {
        $do = DeliveryOrder::with(['vendor', 'vendorExternal', 'items'])->findOrFail($id);
        
        $user = Auth::user();
        if (!$user->isOfficer()) {
            return redirect()->route('dashboard')->with('error', 'Unauthorized access.');
        }
        
        return view('review.do-show', compact('do'));
    }

    public function approveDO($id)
    {
        $do = DeliveryOrder::findOrFail($id);
        
        $user = Auth::user();
        if (!$user->isOfficer()) {
            return redirect()->back()->with('error', 'Unauthorized access.');
        }

        if ($do->status !== 'Submitted') {
            return redirect()->back()->with('error', 'This DO cannot be approved.');
        }

        $do->status = 'Approved';
        $do->save();

        // Send notification to vendor using VendorNotificationService
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
            "DO {$do->do_number} approved by " . $user->name,
            'Success'
        );

        return redirect()->route('review.dos')
            ->with('success', "DO {$do->do_number} approved successfully.");
    }

    public function rejectDO(Request $request, $id)
    {
        $request->validate([
            'reason' => 'required|string|min:5',
        ]);

        $do = DeliveryOrder::findOrFail($id);
        
        $user = Auth::user();
        if (!$user->isOfficer()) {
            return redirect()->back()->with('error', 'Unauthorized access.');
        }

        if ($do->status !== 'Submitted') {
            return redirect()->back()->with('error', 'This DO cannot be rejected.');
        }

        $do->status = 'Rejected';
        $do->reason = $request->reason;
        $do->save();

        // Send notification to vendor using VendorNotificationService
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
            "DO {$do->do_number} rejected by " . $user->name . ". Reason: {$request->reason}",
            'Success'
        );

        return redirect()->route('review.dos')
            ->with('info', "DO {$do->do_number} has been rejected.");
    }

    // =============================================
    // REVIEW OFFICER INVOICE ACTIONS
    // =============================================

    public function approve(Request $request, $id)
    {
        $invoice = Invoice::findOrFail($id);
        
        if ($invoice->status !== 'Submitted') {
            return redirect()->back()->with('error', 'This invoice cannot be approved.');
        }

        $this->invoiceSubmission->updateStatus($invoice, 'Finance Review');
        
        AuditLog::log(
            'Invoice',
            'REVIEW_APPROVE',
            "Invoice {$invoice->invoice_no} approved by Review Officer " . auth()->user()->name,
            'Success'
        );

        // Send notification to vendor using VendorNotificationService
        $this->vendorNotificationService->sendToVendorBySupplierId(
            $invoice->vendor_id,
            'Invoice Approved for Finance Review',
            'Your invoice ' . $invoice->invoice_no . ' has been approved by Review Officer and forwarded to Finance. Please wait for payment processing.',
            'info',
            route('invoices.show', $invoice->invoice_id)
        );

        // Send notification to Finance Officers
        $financeOfficers = User::where('role', 'finance_officer')->get();
        foreach ($financeOfficers as $officer) {
            $this->notificationService->send(
                $officer->id,
                'New Invoice Ready for Finance Review',
                'Invoice ' . $invoice->invoice_no . ' from vendor ' . ($invoice->vendor_name ?? 'Unknown') . ' is ready for finance review.',
                'warning',
                route('review.invoices')
            );
        }

        return redirect()->route('review.invoices')
            ->with('success', "Invoice {$invoice->invoice_no} approved and forwarded to Finance.");
    }

    public function reject(Request $request, $id)
    {
        $request->validate([
            'reason' => 'required|string|min:5',
        ]);

        $invoice = Invoice::findOrFail($id);
        
        if ($invoice->status !== 'Submitted') {
            return redirect()->back()->with('error', 'Only pending invoices can be rejected.');
        }

        $invoice->status = 'Rejected';
        $invoice->reason = $request->reason;
        $invoice->rejected_at = now();
        $invoice->rejected_by = auth()->id();
        $invoice->save();

        AuditLog::log(
            'Invoice',
            'REVIEW_REJECT',
            "Invoice {$invoice->invoice_no} rejected by Review Officer " . auth()->user()->name . ". Reason: {$request->reason}",
            'Success'
        );

        // Send notification to vendor using VendorNotificationService
        $this->vendorNotificationService->sendToVendorBySupplierId(
            $invoice->vendor_id,
            'Invoice Rejected',
            'Your invoice ' . $invoice->invoice_no . ' has been rejected. Reason: ' . $request->reason,
            'danger',
            route('invoices.show', $invoice->invoice_id)
        );

        return redirect()->route('review.invoices')
            ->with('info', "Invoice {$invoice->invoice_no} has been rejected.");
    }

    // =============================================
    // FINANCE OFFICER PAYMENT METHODS
    // =============================================

    public function paymentForm($id)
    {
        $invoice = Invoice::with(['vendor', 'vendorExternal', 'items'])->findOrFail($id);
        
        if ($invoice->status !== 'Finance Review') {
            return redirect()->back()->with('error', 'This invoice is not ready for payment.');
        }

        return view('review.payment', compact('invoice'));
    }

    public function processPayment(Request $request, $id)
    {
        $request->validate([
            'payment_date' => 'required|date',
            'payment_amount' => 'required|numeric|min:0.01',
            'payment_method' => 'required|in:Bank Transfer,Credit Card,Cheque,Cash',
            'transaction_ref' => 'nullable|string|max:100',
            'remarks' => 'nullable|string',
            'proof_of_payment' => 'nullable|file|mimes:pdf,jpg,png,jpeg|max:5120',
        ]);

        $invoice = Invoice::findOrFail($id);
        
        if ($invoice->status !== 'Finance Review') {
            return redirect()->back()->with('error', 'This invoice cannot be processed.');
        }

        DB::beginTransaction();

        try {
            $proofPath = null;
            if ($request->hasFile('proof_of_payment')) {
                $proofPath = $request->file('proof_of_payment')->store('payment_proofs', 'public');
            }

            $payment = Payment::create([
                'invoice_id' => $invoice->invoice_id,
                'payment_date' => $request->payment_date,
                'payment_amount' => $request->payment_amount,
                'payment_status' => 'Completed',
                'payment_method' => $request->payment_method,
                'transaction_ref' => $request->transaction_ref,
                'proof_of_payment' => $proofPath,
                'remarks' => $request->remarks,
                'processed_by' => auth()->id(),
            ]);

            $this->invoiceSubmission->updateStatus($invoice, 'Payment Processing');

            AuditLog::log(
                'Invoice',
                'FINANCE_PROCESS',
                "Payment of RM {$payment->payment_amount} processed for invoice {$invoice->invoice_no} by Finance Officer " . auth()->user()->name,
                'Success'
            );

            // Send notification to vendor using VendorNotificationService
            $this->vendorNotificationService->sendToVendorBySupplierId(
                $invoice->vendor_id,
                'Payment Processing Started',
                'Payment for your invoice ' . $invoice->invoice_no . ' is now being processed. Amount: RM ' . number_format($payment->payment_amount, 2),
                'info',
                route('invoices.show', $invoice->invoice_id)
            );

            DB::commit();

            return redirect()->route('review.payment.details', $invoice->invoice_id)
                ->with('success', "Payment of RM {$payment->payment_amount} processed. Please confirm and mark as paid.");

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Failed to process payment: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function confirmPayment($id)
    {
        $invoice = Invoice::with(['payment', 'vendor', 'vendorExternal'])->findOrFail($id);
        
        if ($invoice->status !== 'Payment Processing') {
            return redirect()->back()->with('error', 'This invoice is not in payment processing status.');
        }

        if (!$invoice->payment) {
            return redirect()->back()->with('error', 'No payment record found for this invoice.');
        }

        return view('review.confirm-payment', compact('invoice'));
    }

    public function markAsPaid(Request $request, $id)
    {
        $invoice = Invoice::findOrFail($id);
        
        if ($invoice->status !== 'Payment Processing') {
            return redirect()->back()->with('error', 'This invoice cannot be marked as paid.');
        }

        DB::beginTransaction();

        try {
            $invoice->status = 'Paid';
            $invoice->payments = $invoice->total;
            $invoice->balance_due = 0;
            $invoice->save();

            if ($invoice->payment) {
                $invoice->payment->update(['payment_status' => 'Completed']);
            }

            AuditLog::log(
                'Invoice',
                'FINANCE_PAID',
                "Invoice {$invoice->invoice_no} marked as paid by Finance Officer " . auth()->user()->name,
                'Success'
            );

            // Send notification to vendor using VendorNotificationService
            $this->vendorNotificationService->sendToVendorBySupplierId(
                $invoice->vendor_id,
                'Payment Completed',
                'Payment for your invoice ' . $invoice->invoice_no . ' has been completed. Amount: RM ' . number_format($invoice->total, 2),
                'success',
                route('invoices.show', $invoice->invoice_id)
            );

            DB::commit();

            return redirect()->route('review.invoices')
                ->with('success', "Invoice {$invoice->invoice_no} has been marked as paid.");

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Failed to mark as paid: ' . $e->getMessage());
        }
    }

    public function viewPayment($id)
    {
        $invoice = Invoice::with(['payment', 'vendor', 'vendorExternal', 'payment.processor'])->findOrFail($id);
        
        if (!$invoice->payment) {
            return redirect()->back()->with('error', 'No payment record found for this invoice.');
        }

        return view('review.payment-details', compact('invoice'));
    }

    public function editPayment($id)
    {
        $invoice = Invoice::with(['payment', 'vendor', 'vendorExternal'])->findOrFail($id);
        
        if (!$invoice->payment) {
            return redirect()->back()->with('error', 'No payment record found for this invoice.');
        }

        if ($invoice->status === 'Paid') {
            return redirect()->back()->with('error', 'Cannot edit payment for a paid invoice.');
        }

        return view('review.payment-edit', compact('invoice'));
    }

    public function updatePayment(Request $request, $id)
    {
        $request->validate([
            'payment_date' => 'required|date',
            'payment_amount' => 'required|numeric|min:0.01',
            'payment_method' => 'required|in:Bank Transfer,Credit Card,Cheque,Cash',
            'transaction_ref' => 'nullable|string|max:100',
            'remarks' => 'nullable|string',
            'proof_of_payment' => 'nullable|file|mimes:pdf,jpg,png,jpeg|max:5120',
        ]);

        $invoice = Invoice::with('payment')->findOrFail($id);
        
        if (!$invoice->payment) {
            return redirect()->back()->with('error', 'No payment record found.');
        }

        if ($invoice->status === 'Paid') {
            return redirect()->back()->with('error', 'Cannot update payment for a paid invoice.');
        }

        DB::beginTransaction();

        try {
            $payment = $invoice->payment;

            if ($request->hasFile('proof_of_payment')) {
                if ($payment->proof_of_payment && Storage::disk('public')->exists($payment->proof_of_payment)) {
                    Storage::disk('public')->delete($payment->proof_of_payment);
                }
                $proofPath = $request->file('proof_of_payment')->store('payment_proofs', 'public');
                $payment->proof_of_payment = $proofPath;
            }

            $payment->payment_date = $request->payment_date;
            $payment->payment_amount = $request->payment_amount;
            $payment->payment_method = $request->payment_method;
            $payment->transaction_ref = $request->transaction_ref;
            $payment->remarks = $request->remarks;
            $payment->save();

            AuditLog::log(
                'Invoice',
                'FINANCE_UPDATE',
                "Payment for invoice {$invoice->invoice_no} updated by Finance Officer " . auth()->user()->name,
                'Success'
            );

            DB::commit();

            return redirect()->route('review.payment.details', $invoice->invoice_id)
                ->with('success', "Payment for invoice {$invoice->invoice_no} has been updated.");

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Failed to update payment: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function deletePayment($id)
    {
        $invoice = Invoice::with('payment')->findOrFail($id);
        
        if (!$invoice->payment) {
            return redirect()->back()->with('error', 'No payment record found.');
        }

        if ($invoice->status === 'Paid') {
            return redirect()->back()->with('error', 'Cannot delete payment for a paid invoice.');
        }

        DB::beginTransaction();

        try {
            $payment = $invoice->payment;

            if ($payment->proof_of_payment && Storage::disk('public')->exists($payment->proof_of_payment)) {
                Storage::disk('public')->delete($payment->proof_of_payment);
            }

            $payment->delete();

            $invoice->status = 'Finance Review';
            $invoice->save();

            AuditLog::log(
                'Invoice',
                'FINANCE_DELETE',
                "Payment for invoice {$invoice->invoice_no} deleted by Finance Officer " . auth()->user()->name,
                'Success'
            );

            DB::commit();

            return redirect()->route('review.invoices')
                ->with('info', "Payment for invoice {$invoice->invoice_no} has been deleted. Invoice moved back to Finance Review.");

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Failed to delete payment: ' . $e->getMessage());
        }
    }

    // =============================================
    // EXPORT FUNCTIONS
    // =============================================

    public function export(Request $request)
    {
        $user = Auth::user();
        
        if (!$user->isOfficer()) {
            return redirect()->route('dashboard')->with('error', 'Only officers can access this page.');
        }

        $format = $request->get('format', 'pdf');
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');
        $status = $request->get('status');

        $query = Invoice::with(['vendor', 'vendorExternal', 'deliveryOrder']);

        if ($startDate && $endDate) {
            $query->whereBetween('created_at', [$startDate, $endDate]);
        }

        if ($status && $status != 'all') {
            $query->where('status', $status);
        }

        $invoices = $query->orderBy('created_at', 'desc')->get();

        if ($format == 'excel') {
            return $this->exportExcel($invoices);
        }

        if ($format == 'pdf') {
            $data = [
                'invoices' => $invoices,
                'title' => 'Invoice Report',
                'date_generated' => now()->format('d/m/Y H:i:s'),
                'total_amount' => $invoices->sum('total'),
                'total_invoices' => $invoices->count()
            ];
            
            $pdf = Pdf::loadView('review.export-pdf', $data);
            return $pdf->download('invoice_report_' . date('Ymd') . '.pdf');
        }

        return $this->exportPrint($invoices);
    }

    private function exportExcel($invoices)
    {
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="invoice_report_' . date('Ymd') . '.csv"',
        ];

        $callback = function() use ($invoices) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['Invoice No', 'Vendor', 'Date', 'Total (RM)', 'Status', 'Created At']);
            
            foreach ($invoices as $invoice) {
                fputcsv($file, [
                    $invoice->invoice_no,
                    $invoice->vendor_name,
                    $invoice->invoice_date->format('d-m-Y'),
                    number_format($invoice->total, 2),
                    $invoice->status,
                    $invoice->created_at->format('d-m-Y H:i')
                ]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    private function exportPrint($invoices)
    {
        return view('review.export-print', compact('invoices'));
    }
}