<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Officer;
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
        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Password::defaults()],
            'role' => ['required', 'in:review_officer,finance_officer'], // Only officers can register
        ];
        
        return Validator::make($data, $rules);
    }

    protected function create(array $data)
    {
        // Only allow officer registration
        // Vendor registration is handled externally in the vendor database
        
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'role' => $data['role'],
            'vendor_id' => null,
        ]);

        if ($data['role'] === 'review_officer') {
            $department = 'Review Department';
            $position = 'Review Officer';
        } else {
            $department = 'Finance Department';
            $position = 'Finance Officer';
        }

        Officer::create([
            'user_id' => $user->id,
            'staff_name' => $data['name'],
            'department' => $department,
            'position' => $position
        ]);

        return $user;
    }

    public function register(Request $request)
    {
        $this->validator($request->all())->validate();

        try {
            $user = $this->create($request->all());
            Auth::login($user);
            return redirect(route('dashboard'));
        } catch (\Exception $e) {
            return back()->withErrors(['email' => 'Registration failed: ' . $e->getMessage()])->withInput();
        }
    }
}