<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\AssessmentController;
use App\Http\Controllers\ImportController;

Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login'])->name('login.submit');
    Route::get('/otp-verify', [LoginController::class, 'showOtpForm'])->name('otp.verify');
    Route::post('/otp-verify', [LoginController::class, 'verifyOtp'])->name('otp.verify.submit');
    Route::post('/otp-resend', [LoginController::class, 'resendOtp'])->name('otp.resend');
});

Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

Route::middleware(['auth:lembur'])->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/employee', [EmployeeController::class, 'index'])->name('employee');
    Route::post('/employee', [EmployeeController::class, 'store'])->name('employee.store');
    Route::put('/employee/{id}', [EmployeeController::class, 'update'])->name('employee.update');
    Route::delete('/employee/{id}', [EmployeeController::class, 'destroy'])->name('employee.destroy');

    Route::get('/assessment', [AssessmentController::class, 'index'])->name('assessment');
    Route::get('/assessment/create', [AssessmentController::class, 'create'])->name('assessment.create');
    Route::post('/assessment', [AssessmentController::class, 'store'])->name('assessment.store');
    // Route::get('/assessment/{id}', [AssessmentController::class, 'show'])->name('assessment.show');
    Route::get('/assessment/{id}/edit', [AssessmentController::class, 'edit'])->name('assessment.edit');
    Route::put('/assessment/{id}', [AssessmentController::class, 'update'])->name('assessment.update');
    Route::delete('/assessment/{id}', [AssessmentController::class, 'destroy'])->name('assessment.destroy');

    Route::get('/import', [ImportController::class, 'index'])->name('import');
    Route::post('/import', [ImportController::class, 'store'])->name('import.store');
    Route::get('/import/template', [ImportController::class, 'template'])->name('import.template');
});
