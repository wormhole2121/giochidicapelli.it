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

Route::get('/', [HomeController::class, 'homepage'])->name('home');
// rotte per il calendario
Route::get('/calendario', [BookingController::class, 'index'])->name('calendario');
Route::post('/prenota', [BookingController::class, 'prenota'])->name('prenota');
Route::get('/le-mie-prenotazioni', [BookingController::class, 'leMiePrenotazioni'])->name('le-mie-prenotazioni');
Route::delete('/elimina/{id}', [BookingController::class, 'elimina'])->name('elimina');



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


// rotte admin
// Route::middleware(['auth', 'admin'])->group(function () {
//     Route::get('/admin/bookings', [AdminBookingController::class, 'adminIndex'])->name('admin.bookings.index');
//     Route::get('/admin/bookings/create', [AdminBookingController::class, 'adminCreate'])->name('admin.bookings.create');
//     Route::post('/admin/bookings', [AdminBookingController::class, 'adminStore'])->name('admin.bookings.store');
//     Route::get('/admin/bookings/{id}/edit', [AdminBookingController::class, 'adminEdit'])->name('admin.bookings.edit');
//     Route::put('/admin/bookings/{id}', [AdminBookingController::class, 'adminUpdate'])->name('admin.bookings.update');
//     Route::delete('/admin/bookings/{id}', [AdminBookingController::class, 'adminDestroy'])->name('admin.bookings.destroy');
// });



