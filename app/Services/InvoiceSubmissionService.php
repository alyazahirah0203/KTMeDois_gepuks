<?php

namespace App\Services;

use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\AuditLog;
use App\Models\DeliveryOrder;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Notifications\InvoiceSubmittedNotification;
use App\Services\NotificationService;

class InvoiceSubmissionService
{
    protected $calculationService;
    protected $notificationService;

    public function __construct(InvoiceCalculationService $calculationService, NotificationService $notificationService) 
    {
        $this->calculationService = $calculationService;
        $this->notificationService = $notificationService;
    }

    public function submit($data, $do, $items, $proofFile)
    {
        DB::beginTransaction();

        try {
            $lineTotal = 0;
            foreach ($items as $item) {
                $lineTotal += $item['quantity'] * $item['unit_price'];
            }

            $creditNote = $data['credit_note'] ?? 0;
            $isLate = $this->calculationService->isLateDelivery($do->delivery_date);
            $calculation = $this->calculationService->calculate($lineTotal, $creditNote, $isLate);

            $invoiceNo = $this->generateInvoiceNumber();
            $uuid = (string) \Str::uuid();
            $proofPath = $proofFile->store('proofs', 'public');

            $invoice = Invoice::create([
                'invoice_no' => $invoiceNo,
                'uuid' => $uuid,
                'do_id' => $do->do_id,
                'vendor_id' => $do->vendor_id,
                'invoice_date' => $data['invoice_date'],
                'customer_no' => $do->vendor->supplierid ?? null,
                'line_total' => $calculation['line_total'],
                'service_tax' => $calculation['tax'],
                'shipping' => 0,
                'discount' => $calculation['discount'],
                'penalty' => $calculation['penalty'],
                'total' => $calculation['total'],
                'payments' => 0,
                'credits' => $creditNote,
                'financial_charges' => 0,
                'balance_due' => $calculation['balance_due'],
                'payment_terms' => '30 DAYS',
                'due_date' => now()->addDays(30),
                'proof_of_delivery' => $proofPath,
                'status' => 'Submitted'
            ]);

            foreach ($items as $item) {
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

            $pdfPath = $this->generateInvoicePDF($invoice);
            $invoice->pdf_path = $pdfPath;
            $invoice->save();

            // ==============================================
            // SEND NOTIFICATIONS - ADD THIS CODE HERE
            // ==============================================
            
            // Send notification to vendor
            $vendorUser = User::where('vendor_id', $do->vendor_id)->first();
            if ($vendorUser) {
                $this->notificationService->send(
                    $vendorUser->id,
                    'Invoice Submitted Successfully',
                    'Your invoice ' . $invoiceNo . ' has been submitted and is pending finance review. Total: RM ' . number_format($calculation['total'], 2),
                    'success',
                    route('invoices.show', $invoice->invoice_id)
                );
            }

            // Send notification to finance officers
            $officers = User::where('role', 'officer')->get();
            foreach ($officers as $officer) {
                $this->notificationService->send(
                    $officer->id,
                    'New Invoice Pending Review',
                    'Vendor ' . ($do->vendor->supplier_comp_name ?? 'Unknown') . ' has submitted invoice ' . $invoiceNo . '. Total: RM ' . number_format($calculation['total'], 2),
                    'warning',
                    route('invoices.show', $invoice->invoice_id)
                );
            }

            AuditLog::log(
                'Invoice',
                'SUBMIT',
                "Invoice {$invoiceNo} submitted for DO {$do->do_number}. Total: RM {$calculation['total']}",
                'Success'
            );

            DB::commit();
            return $invoice;

        } catch (\Exception $e) {
            DB::rollBack();
            AuditLog::log('Invoice', 'SUBMIT', "Failed: {$e->getMessage()}", 'Failed');
            throw $e;
        }
    }

    public function updateStatus($invoice, $newStatus, $reason = null)
    {
        DB::beginTransaction();

        try {
            $oldStatus = $invoice->status;
            $invoice->status = $newStatus;
            $invoice->reason = $reason;
            $invoice->save();

            // Send notification when status changes
            $vendorUser = User::where('vendor_id', $invoice->vendor_id)->first();
            
            if ($vendorUser) {
                $statusMessages = [
                    'Finance Review' => 'Your invoice is now under finance review.',
                    'Payment Processing' => 'Your invoice has been approved! Payment is being processed.',
                    'Paid' => 'Payment has been completed for your invoice. Total: RM ' . number_format($invoice->total, 2)
                ];
                
                $statusTitles = [
                    'Finance Review' => 'Invoice Under Review',
                    'Payment Processing' => 'Invoice Approved',
                    'Paid' => 'Payment Completed'
                ];
                
                if (isset($statusMessages[$newStatus])) {
                    $this->notificationService->send(
                        $vendorUser->id,
                        $statusTitles[$newStatus],
                        $statusMessages[$newStatus],
                        $newStatus == 'Paid' ? 'success' : 'info',
                        route('invoices.show', $invoice->invoice_id)
                    );
                }
            }

            AuditLog::log(
                'Invoice',
                'STATUS_CHANGE',
                "Invoice {$invoice->invoice_no} from {$oldStatus} to {$newStatus}",
                'Success'
            );

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            AuditLog::log('Invoice', 'STATUS_CHANGE', "Failed: {$e->getMessage()}", 'Failed');
            throw $e;
        }
    }

    private function generateInvoiceNumber()
    {
        $prefix = 'KTMB/INV/' . date('Ymd') . '/';
        $last = Invoice::where('invoice_no', 'like', $prefix . '%')
                       ->orderBy('invoice_id', 'desc')
                       ->first();
        
        if (!$last) {
            return $prefix . '0001';
        }
        
        $lastNumber = (int) substr($last->invoice_no, -4);
        return $prefix . str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
    }

    private function generateInvoicePDF($invoice)
    {
        $data = [
            'invoice' => $invoice,
            'vendor' => $invoice->vendor,
            'do' => $invoice->deliveryOrder,
            'items' => $invoice->items,
            'company' => [
                'name' => 'Keretapi Tanah Melayu Berhad',
                'address' => 'KTMB Headquarters, Jalan Sultan Hishamuddin, 50621 Kuala Lumpur',
                'reg_no' => '199101015631',
                'sst_no' => 'W10-1808-31002103',
                'tel' => '03-2263 1111',
                'website' => 'www.ktmb.com.my'
            ],
            'payment_details' => [
                'account_name' => 'KERETAPI TANAH MELAYU BERHAD',
                'account_no' => '514011336586',
                'bank_name' => 'Malayan Banking Bhd'
            ]
        ];

        $pdf = Pdf::loadView('pdfs.invoice', $data);
        
        $safeFilename = str_replace('/', '_', $invoice->invoice_no);
        $filename = "invoice_{$safeFilename}.pdf";
        $path = "invoices/{$filename}";
        
        Storage::disk('public')->put($path, $pdf->output());
        
        return $path;
    }
}