<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\VendorUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
        $this->middleware('guest:vendor')->except('logout');
    }

    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
            'role' => 'required|in:vendor,officer,admin'
        ]);

        Log::info('Login attempt', [
            'email' => $request->email,
            'role' => $request->role,
            'ip' => $request->ip()
        ]);

        // VENDOR LOGIN - authenticate from external database
        if ($request->role === 'vendor') {
            $vendorUser = VendorUser::where('email', $request->email)->first();
            
            Log::info('Vendor user check', [
                'email' => $request->email,
                'exists' => $vendorUser ? 'Yes' : 'No',
                'status' => $vendorUser ? $vendorUser->status : 'N/A'
            ]);

            if (!$vendorUser) {
                throw ValidationException::withMessages([
                    'email' => 'No vendor account found with this email.',
                ]);
            }

            if ($vendorUser->status !== 'active') {
                throw ValidationException::withMessages([
                    'email' => 'Your vendor account is inactive. Please contact administrator.',
                ]);
            }

            $vendor = $vendorUser->vendor;
            if (!$vendor) {
                throw ValidationException::withMessages([
                    'email' => 'Vendor company not found. Please contact administrator.',
                ]);
            }

            Log::info('Vendor company check', [
                'SUPPLIERID' => $vendor->SUPPLIERID,
                'status' => $vendor->SUPPLIER_CTC_STATUS,
                'expired_date' => $vendor->SUPPLIER_EXPIRED_DATE
            ]);

            if ($vendor->SUPPLIER_CTC_STATUS !== 'active') {
                throw ValidationException::withMessages([
                    'email' => 'The vendor company is inactive. Please contact administrator.',
                ]);
            }

            if ($vendor->SUPPLIER_EXPIRED_DATE && $vendor->SUPPLIER_EXPIRED_DATE < now()) {
                throw ValidationException::withMessages([
                    'email' => 'The vendor company registration has expired. Please contact administrator.',
                ]);
            }

            $credentials = [
                'email' => $request->email,
                'password' => $request->password,
            ];

            Log::info('Attempting vendor authentication', ['email' => $request->email]);

            if (Auth::guard('vendor')->attempt($credentials, $request->remember)) {
                $authenticatedUser = Auth::guard('vendor')->user();
                
                Log::info('Vendor authentication successful', [
                    'id' => $authenticatedUser->id,
                    'email' => $authenticatedUser->email,
                    'name' => $authenticatedUser->name
                ]);

                $request->session()->regenerate();
                return redirect()->intended(route('vendor.dashboard'));
            }

            Log::warning('Vendor authentication failed - password mismatch', ['email' => $request->email]);
            
            throw ValidationException::withMessages([
                'password' => 'The password is incorrect.',
            ]);
        }

        // OFFICER/ADMIN LOGIN - authenticate from main database
        if (Auth::attempt([
            'email' => $request->email,
            'password' => $request->password
        ], $request->remember)) {
            
            $user = Auth::user();
            
            if ($request->role === 'admin' && !$user->isITOfficer()) {
                Auth::logout();
                throw ValidationException::withMessages([
                    'role' => 'This account does not have admin privileges.',
                ]);
            }
            
            if ($request->role === 'officer' && !$user->isOfficer()) {
                Auth::logout();
                throw ValidationException::withMessages([
                    'role' => 'This account does not have officer privileges.',
                ]);
            }
            
            $request->session()->regenerate();
            
            // Redirect based on role - UPDATED ROUTE NAMES
            if ($user->isITOfficer()) {
                return redirect()->intended(route('dashboard.admin')); // CHANGED from admin.dashboard
            } elseif ($user->isReviewOfficer() || $user->isFinanceOfficer()) {
                return redirect()->intended(route('review.index'));
            }
            
            return redirect()->intended(route('dashboard'));
        }

        throw ValidationException::withMessages([
            'email' => 'The provided credentials do not match our records.',
        ]);
    }

    public function logout(Request $request)
    {
        if (Auth::guard('vendor')->check()) {
            Auth::guard('vendor')->logout();
        }
        
        if (Auth::check()) {
            Auth::logout();
        }
        
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect('/login');
    }
}