<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ShortUrlController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\InvitationController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    if (Auth::check()) {
        return redirect('/dashboard');
    }
    return redirect('/login');
});

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [HomeController::class, 'index'])->name('dashboard');
    Route::get('/short-urls', [ShortUrlController::class, 'index'])->name('shorturls.index');
    Route::post('/short-urls', [ShortUrlController::class, 'store'])->name('shorturls.store');
    Route::get('/short-urls/{code}', [ShortUrlController::class, 'redirect'])->name('shorturls.redirect');
    // SuperAdmin only
    Route::middleware('role:SuperAdmin')->group(function () {
        Route::resource('companies', CompanyController::class)->only(['index','store']);
    });

    // SuperAdmin + Admin can send invitations (with restrictions)
    Route::middleware('role:SuperAdmin,Admin')->group(function () {
        Route::get('/invitations', [InvitationController::class, 'invitation']);
        Route::get('/api/invitations', [InvitationController::class, 'index']);
        Route::post('/api/invitations', [InvitationController::class, 'store']);
    });
});

require __DIR__.'/auth.php';
