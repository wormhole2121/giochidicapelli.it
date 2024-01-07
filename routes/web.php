<?php
use App\Http\Controllers\AdminBookingController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Route;



/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// rotta json
Route::get('/manifest.json', function () {
     return response()->file(public_path('manifest.json'));
});



Route::get('/', [HomeController::class, 'homepage'])->name('home');
// rotte per il calendario
Route::get('/calendario', [BookingController::class, 'index'])->name('calendario');
Route::post('/prenota', [BookingController::class, 'prenota'])->name('prenota');
Route::get('/le-mie-prenotazioni', [BookingController::class, 'leMiePrenotazioni'])->name('le-mie-prenotazioni');
Route::delete('/elimina/{id}', [BookingController::class, 'elimina'])->name('elimina');

Route::get('/terms', function () {
     return view('terms');
});
 

// rotte reset password

use App\Http\Controllers\PasswordResetController;

Route::get('/password/reset', [PasswordResetController::class, 'showForgotPasswordForm'])
     ->middleware('guest')->name('password.request');

Route::post('/password/email', [PasswordResetController::class, 'sendResetLinkEmail'])
     ->middleware('guest')->name('password.email');

Route::get('/password/reset/{token}', [PasswordResetController::class, 'showResetPasswordForm'])
     ->middleware('guest')->name('password.reset');

Route::post('/password/reset', [PasswordResetController::class, 'resetPassword'])
     ->middleware('guest')->name('password.update');
