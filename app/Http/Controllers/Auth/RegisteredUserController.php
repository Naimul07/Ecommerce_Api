<?php

namespace App\Http\Controllers\Auth;

use Carbon\Carbon;
use App\Models\User;
use App\Mail\OtpMail;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\Rules;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Auth\Events\Registered;
use Illuminate\Validation\ValidationException;

class RegisteredUserController extends Controller
{
    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request):JsonResponse
    {
        // dd($request);
        // try{
            $otp = rand(100000,999999);
            $request->validate([
                'name' => ['required', 'string', 'max:255'],
                'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
                'password' => ['required', 'confirmed', Rules\Password::defaults()],
            ]);
    
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->string('password')),
                'otp' => $otp,
                'otp_expires_at'=>Carbon::now()->addMinutes(10),
            ]);
    
            // event(new Registered($user));
            Mail::to($user->email)->queue(new OtpMail($otp));
    
            Auth::login($user);
    
            return response()->json([
                'user'=>$user,
            ],200);
        // }catch(ValidationException $e){
        //     return response()->json([
        //         'errors'=>$e->errors(),
        //     ],422);
        // }catch (\Throwable $e) { // Generic exception handling
        //     return response()->json([
        //         'message' => 'An unexpected error occurred.',
        //         'error' => $e->getMessage(), // Optional: Remove this in production for security reasons
        //     ], 500);
        // }
       
    }
}
