<?php

use App\Http\Controllers\GoogleAuthController;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Laravel\Socialite\Facades\Socialite;

Route::get('/', function () {
    return ['Laravel' => app()->version()];
});
Route::get('/auth/google/redirect', [GoogleAuthController::class,'redirect']);
Route::get('/auth/google/callback',[GoogleAuthController::class,'callback']);

/* 
Route::get('/auth/google/redirect', function (Request $request) {
    return Socialite::driver("google")->redirect();
});
Route::get('/auth/google/callback', function (Request $request) {
    $googleUser = Socialite::driver("google")->user();
    $user = User::updateOrCreate(
        ["google_id" => $googleUser->id],
        [
            'name' => $googleUser->name,
            'email' => $googleUser->email,
            'password' => Str::password(12),
            'email_verified_at'=>now(),
        ]
    );
    Auth::login($user);
    return redirect(config("app.frontend_url")."/");
    // dd($user);
}); */


require __DIR__ . '/auth.php';
