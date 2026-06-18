<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Invoice;
use App\Models\Vendor;
use App\Models\User;
use App\Models\VendorDB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class AdminController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function dashboard()
    {
        $user = Auth::user();
        
        if (!$user->isITOfficer()) {
            return redirect()->route('dashboard')->with('error', 'Unauthorized access.');
        }

        $totalVendors = Vendor::count() + VendorDB::count();
        $totalInvoices = Invoice::count();
        $totalUsers = User::count();
        $totalPaid = Invoice::where('status', 'Paid')->sum('total');
        $pendingInvoices = Invoice::where('status', 'Submitted')->count();
        $recentLogs = AuditLog::orderBy('timestamp', 'desc')->limit(10)->get();

        // View path: resources/views/dashboard/admin.blade.php
        return view('dashboard.admin', compact(
            'totalVendors',
            'totalInvoices',
            'totalUsers',
            'totalPaid',
            'pendingInvoices',
            'recentLogs'
        ));
    }

    public function auditLogs()
    {
        $user = Auth::user();
        
        if (!$user->isITOfficer()) {
            return redirect()->route('dashboard')->with('error', 'Unauthorized access.');
        }

        $auditLogs = AuditLog::orderBy('timestamp', 'desc')->get();

        // View path: resources/views/admin/audit-logs.blade.php
        return view('admin.audit-logs', compact('auditLogs'));
    }

    public function exportAuditLogsPDF(Request $request)
    {
        $user = Auth::user();
        
        if (!$user->isITOfficer()) {
            return redirect()->route('dashboard')->with('error', 'Unauthorized access.');
        }

        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');
        $module = $request->get('module');
        $status = $request->get('status');

        $query = AuditLog::orderBy('timestamp', 'desc');

        if ($startDate && $endDate) {
            $query->whereBetween('timestamp', [$startDate . ' 00:00:00', $endDate . ' 23:59:59']);
        }

        if ($module && $module != 'all') {
            $query->where('module', $module);
        }

        if ($status && $status != 'all') {
            $query->where('status', $status);
        }

        $auditLogs = $query->get();

        $data = [
            'auditLogs' => $auditLogs,
            'title' => 'Audit Log Report',
            'date_generated' => now()->format('d/m/Y H:i:s'),
            'total_logs' => $auditLogs->count(),
            'filters' => [
                'start_date' => $startDate,
                'end_date' => $endDate,
                'module' => $module,
                'status' => $status
            ]
        ];

        $pdf = Pdf::loadView('admin.audit-pdf', $data);
        return $pdf->download('audit_log_report_' . date('Ymd') . '.pdf');
    }

    public function reports()
    {
        $user = Auth::user();
        
        if (!$user->isITOfficer()) {
            return redirect()->route('dashboard')->with('error', 'Unauthorized access.');
        }

        $totalInvoices = Invoice::count();
        $totalAmount = Invoice::sum('total');
        $totalPaid = Invoice::where('status', 'Paid')->sum('total');
        $totalPending = Invoice::where('status', 'Submitted')->sum('total');
        $totalReviewing = Invoice::where('status', 'Finance Review')->count();
        $totalProcessing = Invoice::where('status', 'Payment Processing')->count();

        $statusData = [
            'Submitted' => Invoice::where('status', 'Submitted')->count(),
            'Finance Review' => Invoice::where('status', 'Finance Review')->count(),
            'Payment Processing' => Invoice::where('status', 'Payment Processing')->count(),
            'Paid' => Invoice::where('status', 'Paid')->count(),
        ];

        $topVendors = Invoice::with(['vendor', 'vendorExternal'])
            ->select('vendor_id', DB::raw('COUNT(*) as total_invoices'), DB::raw('SUM(total) as total_amount'))
            ->where('status', 'Paid')
            ->groupBy('vendor_id')
            ->orderBy('total_amount', 'desc')
            ->limit(10)
            ->get();

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

        // View path: resources/views/admin/reports.blade.php
        return view('admin.reports', compact(
            'totalInvoices',
            'totalAmount',
            'totalPaid',
            'totalPending',
            'totalReviewing',
            'totalProcessing',
            'statusData',
            'topVendors',
            'monthlyData'
        ));
    }
    
    public function exportAuditLogsCSV(Request $request)
    {
        $user = Auth::user();
        
        if (!$user->isITOfficer()) {
            return redirect()->route('dashboard')->with('error', 'Unauthorized access.');
        }

        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');
        $module = $request->get('module');
        $status = $request->get('status');

        $query = AuditLog::orderBy('timestamp', 'desc');

        if ($startDate && $endDate) {
            $query->whereBetween('timestamp', [$startDate . ' 00:00:00', $endDate . ' 23:59:59']);
        }

        if ($module && $module != 'all') {
            $query->where('module', $module);
        }

        if ($status && $status != 'all') {
            $query->where('status', $status);
        }

        $auditLogs = $query->get();

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="audit_log_report_' . date('Ymd') . '.csv"',
        ];

        $callback = function() use ($auditLogs) {
            $file = fopen('php://output', 'w');
            
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
            
            fputcsv($file, ['Timestamp', 'Username', 'Module', 'Action', 'Description', 'Status']);
            
            foreach ($auditLogs as $log) {
                $timestamp = $log->timestamp instanceof \Carbon\Carbon 
                    ? $log->timestamp 
                    : \Carbon\Carbon::parse($log->timestamp);
                
                fputcsv($file, [
                    $timestamp->format('Y-m-d H:i:s'),
                    $log->username,
                    $log->module,
                    $log->action,
                    $log->description,
                    $log->status
                ]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}