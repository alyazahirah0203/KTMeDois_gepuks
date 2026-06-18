<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ForgotPasswordController extends Controller
{
    public function showLinkRequestForm()
    {
        return view('auth.passwords.forgot');
    }

    public function sendResetLinkEmail(Request $request)
    {
        return redirect()->route('password.request')
            ->with('info', 'Please contact IT Support at it@ktmb.gov.my to reset your password.');
    }
}