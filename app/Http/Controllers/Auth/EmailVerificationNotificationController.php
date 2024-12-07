<?php

namespace App\Http\Controllers\Auth;

use Carbon\Carbon;
use App\Mail\OtpMail;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Mail;
use Illuminate\Http\RedirectResponse;

class EmailVerificationNotificationController extends Controller
{
    /**
     * Send a new email verification notification.
     */
    /* public function store(Request $request): JsonResponse|RedirectResponse
    {
        if ($request->user()->hasVerifiedEmail()) {
            return redirect()->intended('/dashboard');
        }

        $request->user()->sendEmailVerificationNotification();

        return response()->json(['status' => 'verification-link-sent']);
    } */
    
    public function store(Request $request)
    {
        if ($request->user()->hasVerifiedEmail()) {
            return response()->json(['message' => 'Email already verified.'], 409);
        }
        if($request->email !== $request->user()->email)
        {
            return response()->json('Unauthorized',403);
        }
        $otp = rand(100000, 999999); // email verification otp random generator
        $user = $request->user();
        $otp_expires = Carbon::now()->addMinutes(10);
        $user->otp = $otp;
        $user->otp_expires_at = $otp_expires;
        $user->save();
        Mail::to($user->email)->queue(new OtpMail($otp));
        return response()->json([
            'message' => 'email sent',
        ]);
    }
}
