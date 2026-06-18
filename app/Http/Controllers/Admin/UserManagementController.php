<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Officer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class UserManagementController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('it_officer');
    }

    public function index()
    {
        $users = User::with('officer')
            ->whereIn('role', ['review_officer', 'finance_officer', 'it_officer'])
            ->orderBy('created_at', 'desc')
            ->get();
        
        return view('admin.users.index', compact('users'));
    }

    public function create()
    {
        return view('admin.users.create');
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Password::defaults()],
            'role' => ['required', 'in:review_officer,finance_officer,it_officer'],
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        DB::beginTransaction();

        try {
            $roleMap = [
                'review_officer' => ['department' => 'Review Department', 'position' => 'Review Officer'],
                'finance_officer' => ['department' => 'Finance Department', 'position' => 'Finance Officer'],
                'it_officer' => ['department' => 'IT Department', 'position' => 'IT Officer'],
            ];

            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role' => $request->role,
                'vendor_id' => null,
            ]);

            Officer::create([
                'user_id' => $user->id,
                'staff_name' => $request->name,
                'department' => $roleMap[$request->role]['department'],
                'position' => $roleMap[$request->role]['position'],
            ]);

            DB::commit();

            return redirect()->route('dashboard.admin.users.index')
                ->with('success', 'User created successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to create user: ' . $e->getMessage());
            
            return redirect()->back()
                ->with('error', 'Failed to create user: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function edit($id)
    {
        $user = User::with('officer')->findOrFail($id);
        
        if (!in_array($user->role, ['review_officer', 'finance_officer', 'it_officer'])) {
            return redirect()->route('dashboard.admin.users.index')
                ->with('error', 'Cannot edit this user.');
        }
        
        return view('admin.users.edit', compact('user'));
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);
        
        if (!in_array($user->role, ['review_officer', 'finance_officer', 'it_officer'])) {
            return redirect()->route('dashboard.admin.users.index')
                ->with('error', 'Cannot edit this user.');
        }

        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $id],
            'role' => ['required', 'in:review_officer,finance_officer,it_officer'],
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        DB::beginTransaction();

        try {
            $roleMap = [
                'review_officer' => ['department' => 'Review Department', 'position' => 'Review Officer'],
                'finance_officer' => ['department' => 'Finance Department', 'position' => 'Finance Officer'],
                'it_officer' => ['department' => 'IT Department', 'position' => 'IT Officer'],
            ];

            $user->update([
                'name' => $request->name,
                'email' => $request->email,
                'role' => $request->role,
            ]);

            if ($request->filled('password')) {
                $request->validate([
                    'password' => ['confirmed', Password::defaults()],
                ]);
                $user->update(['password' => Hash::make($request->password)]);
            }

            if ($user->officer) {
                $user->officer->update([
                    'staff_name' => $request->name,
                    'department' => $roleMap[$request->role]['department'],
                    'position' => $roleMap[$request->role]['position'],
                ]);
            }

            DB::commit();

            return redirect()->route('dashboard.admin.users.index')
                ->with('success', 'User updated successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to update user: ' . $e->getMessage());
            
            return redirect()->back()
                ->with('error', 'Failed to update user: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);
        
        if ($user->id == auth()->id()) {
            return redirect()->route('dashboard.admin.users.index')
                ->with('error', 'You cannot delete your own account.');
        }

        if (!in_array($user->role, ['review_officer', 'finance_officer', 'it_officer'])) {
            return redirect()->route('dashboard.admin.users.index')
                ->with('error', 'Cannot delete this user.');
        }

        DB::beginTransaction();

        try {
            if ($user->officer) {
                $user->officer->delete();
            }
            $user->delete();

            DB::commit();

            return redirect()->route('dashboard.admin.users.index')
                ->with('success', 'User deleted successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to delete user: ' . $e->getMessage());
            
            return redirect()->back()
                ->with('error', 'Failed to delete user: ' . $e->getMessage());
        }
    }

    public function import(Request $request)
    {
        $request->validate([
            'csv_file' => 'required|file|mimes:csv,txt|max:2048',
        ]);

        $file = $request->file('csv_file');
        $handle = fopen($file->getPathname(), 'r');
        
        $bom = fread($handle, 3);
        if ($bom !== "\xEF\xBB\xBF") {
            rewind($handle);
        }
        
        $headers = fgetcsv($handle);
        $headers = array_map('trim', $headers);
        
        Log::info('CSV Headers: ', $headers);
        
        $requiredHeaders = ['name', 'email', 'password', 'role'];
        
        $headersLower = array_map('strtolower', $headers);
        $requiredHeadersLower = array_map('strtolower', $requiredHeaders);
        
        $missingHeaders = array_diff($requiredHeadersLower, $headersLower);
        if (!empty($missingHeaders)) {
            fclose($handle);
            return redirect()->back()
                ->with('error', 'CSV missing required columns: ' . implode(', ', $missingHeaders) . '. Found headers: ' . implode(', ', $headers));
        }

        $roleMap = [
            'review_officer' => ['department' => 'Review Department', 'position' => 'Review Officer'],
            'finance_officer' => ['department' => 'Finance Department', 'position' => 'Finance Officer'],
            'it_officer' => ['department' => 'IT Department', 'position' => 'IT Officer'],
        ];

        $successCount = 0;
        $errorCount = 0;
        $errors = [];

        while (($row = fgetcsv($handle)) !== false) {
            if (empty(array_filter($row))) {
                continue;
            }
            
            $data = array_combine($headers, $row);
            $data = array_map('trim', $data);
            
            if (empty($data['name']) || empty($data['email'])) {
                $errorCount++;
                $errors[] = "Row with empty name or email. Skipped.";
                continue;
            }
            
            if (!in_array($data['role'], ['review_officer', 'finance_officer', 'it_officer'])) {
                $errorCount++;
                $errors[] = "Row with email {$data['email']} has invalid role. Skipped.";
                continue;
            }

            if (User::where('email', $data['email'])->exists()) {
                $errorCount++;
                $errors[] = "User with email {$data['email']} already exists. Skipped.";
                continue;
            }

            DB::beginTransaction();

            try {
                $user = User::create([
                    'name' => $data['name'],
                    'email' => $data['email'],
                    'password' => Hash::make($data['password']),
                    'role' => $data['role'],
                    'vendor_id' => null,
                ]);

                Officer::create([
                    'user_id' => $user->id,
                    'staff_name' => $data['name'],
                    'department' => $roleMap[$data['role']]['department'],
                    'position' => $roleMap[$data['role']]['position'],
                ]);

                DB::commit();
                $successCount++;

            } catch (\Exception $e) {
                DB::rollBack();
                $errorCount++;
                $errors[] = "Failed to create user {$data['email']}: " . $e->getMessage();
                Log::error('CSV import error: ' . $e->getMessage());
            }
        }

        fclose($handle);

        $message = "Import completed. Success: {$successCount}, Failed: {$errorCount}";
        
        if ($errorCount > 0) {
            $message .= "<br><br><strong>Errors:</strong><br>" . implode('<br>', $errors);
            return redirect()->route('dashboard.admin.users.index')
                ->with('warning', $message);
        }

        return redirect()->route('dashboard.admin.users.index')
            ->with('success', $message);
    }

    public function downloadTemplate()
    {
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="user_import_template.csv"',
        ];

        $callback = function() {
            $file = fopen('php://output', 'w');
            
            fputcsv($file, ['name', 'email', 'password', 'role']);
            
            fputcsv($file, [
                'John Doe',
                'john@example.com',
                'password123',
                'review_officer'
            ]);
            
            fputcsv($file, [
                'Jane Smith',
                'jane@example.com',
                'password123',
                'finance_officer'
            ]);
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}