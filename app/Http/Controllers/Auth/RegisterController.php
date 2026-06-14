<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Vendor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;

class RegisterController extends Controller
{
    public function __construct()
    {
        $this->middleware('guest');
    }

    public function showRegistrationForm()
    {
        return view('auth.register');
    }

    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Password::defaults()],
            'supplierid' => ['required', 'string', 'exists:vendors,supplierid'],
            'role' => ['required', 'in:vendor,officer'], 
        ]);
    }

    protected function create(array $data)
    {
        $vendor = Vendor::where('supplierid', $data['supplierid'])->first();

        if (!$vendor) {
            throw new \Exception('Vendor ID not found');
        }

        if ($vendor->supplier_ctc_status !== 'active') {
            throw new \Exception('Your vendor account is inactive');
        }

        if ($vendor->supplier_expired_date && $vendor->supplier_expired_date < now()) {
            throw new \Exception('Your vendor registration has expired');
        }

        return User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'role' => $data['role'],
            'vendor_id' => $data['supplierid'],
        ]);
    }

    public function register(Request $request)
    {
        $this->validator($request->all())->validate();

        try {
            $user = $this->create($request->all());
            Auth::login($user);
            return redirect(route('dashboard'));
        } catch (\Exception $e) {
            return back()->withErrors(['supplierid' => $e->getMessage()])->withInput();
        }
    }
}