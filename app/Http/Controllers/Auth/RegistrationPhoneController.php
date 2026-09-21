<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\Recaptcha;
use App\Services\RegistrationPhoneVerification;
use App\Services\RegistrationSms;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RegistrationPhoneController extends Controller
{
    public function send(Request $request, RegistrationPhoneVerification $verification, RegistrationSms $sms, Recaptcha $recaptcha): JsonResponse
    {
        $data = $request->validate(['phone' => ['required', 'regex:/^09[0-9]{9}$/']]);
        $recaptcha->validate($request);
        $verification->send($request, $data['phone'], $sms);

        return response()->json(['message' => 'A six-digit SMS code has been requested. It expires in 5 minutes.']);
    }

    public function verify(Request $request, RegistrationPhoneVerification $verification): JsonResponse
    {
        $data = $request->validate([
            'phone' => ['required', 'regex:/^09[0-9]{9}$/'],
            'code' => ['required', 'digits:6'],
        ]);
        $verification->verify($request, $data['phone'], $data['code']);

        return response()->json(['message' => 'Phone number verified. You can now create your account.']);
    }
}
