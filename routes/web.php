<?php
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\PayrollController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\LoanController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\PasswordController;
use App\Http\Controllers\SocialAuthController;
use Illuminate\Support\Facades\Route;

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Google SSO Routes
Route::get('auth/google', [SocialAuthController::class, 'redirectToGoogle'])->name('auth.google');
Route::get('auth/google/callback', [SocialAuthController::class, 'handleGoogleCallback']);

Route::middleware('auth')->group(function () {
    // Menu Ganti Password
    Route::get('password/change', [PasswordController::class, 'edit'])->name('password.change');
    Route::put('password/change', [PasswordController::class, 'update'])->name('password.update');

    // Menu Superadmin: Manajemen User Operasional
    Route::middleware('role:superadmin')->group(function () {
        Route::get('users', [UserController::class, 'index'])->name('users.index');
        Route::get('users/create', [UserController::class, 'create'])->name('users.create');
        Route::post('users', [UserController::class, 'store'])->name('users.store');
        Route::get('users/{user}/edit', [UserController::class, 'edit'])->name('users.edit');
        Route::put('users/{user}', [UserController::class, 'update'])->name('users.update');
        Route::patch('users/{user}/toggle', [UserController::class, 'toggleStatus'])->name('users.toggle');
    });

    Route::get('/', function () {
        return redirect()->route('payrolls.index');
    });

    // Menu Pegawai: Hanya Staff dan Finance yang bisa mengelola, HRD hanya bisa lihat
    Route::middleware('role:staff,hrd,finance')->group(function () {
        Route::get('employees', [EmployeeController::class, 'index'])->name('employees.index');
    });

    Route::middleware('role:staff')->group(function () {
        Route::get('employees/create', [EmployeeController::class, 'create'])->name('employees.create');
        Route::post('employees', [EmployeeController::class, 'store'])->name('employees.store');
        Route::get('employees/{employee}/edit', [EmployeeController::class, 'edit'])->name('employees.edit');
        Route::put('employees/{employee}', [EmployeeController::class, 'update'])->name('employees.update');
        Route::delete('employees/{employee}', [EmployeeController::class, 'destroy'])->name('employees.destroy');
    });

    // Menu Pinjaman (Loans)
    Route::get('loans', [LoanController::class, 'index'])->name('loans.index');
    Route::middleware('role:pegawai')->group(function () {
        Route::get('loans/create', [LoanController::class, 'create'])->name('loans.create');
        Route::post('loans', [LoanController::class, 'store'])->name('loans.store');
    });
    Route::middleware('role:finance')->group(function () {
        Route::patch('loans/{loan}/approve', [LoanController::class, 'approve'])->name('loans.approve');
        Route::patch('loans/{loan}/reject', [LoanController::class, 'reject'])->name('loans.reject');
    });

    // Menu Payroll (Khusus Staff - Maker)
    Route::middleware('role:staff')->group(function () {
        Route::get('payrolls/create', [PayrollController::class, 'create'])->name('payrolls.create');
        Route::post('payrolls', [PayrollController::class, 'store'])->name('payrolls.store');
        Route::get('payrolls/{payroll}/edit', [PayrollController::class, 'edit'])->name('payrolls.edit');
        Route::put('payrolls/{payroll}', [PayrollController::class, 'update'])->name('payrolls.update');
        Route::delete('payrolls/{payroll}', [PayrollController::class, 'destroy'])->name('payrolls.destroy');
    });

    // Menu Payroll (Umum)
    Route::get('payrolls/loan-deduction/{employee}', [PayrollController::class, 'getLoanDeduction'])->name('payrolls.loan_deduction');
    Route::get('payrolls', [PayrollController::class, 'index'])->name('payrolls.index');
    Route::get('payrolls/export-excel', [PayrollController::class, 'exportExcel'])->name('payrolls.export_excel');
    Route::get('payrolls/print-all', [PayrollController::class, 'printAll'])->name('payrolls.print_all');

    // Menu Payroll (Khusus HRD - Checker)
    Route::patch('payrolls/{payroll}/approve', [PayrollController::class, 'approve'])
        ->middleware('role:hrd')
        ->name('payrolls.approve');

    // Menu Konfirmasi Gaji (Khusus Pegawai)
    Route::patch('payrolls/{payroll}/acknowledge', [PayrollController::class, 'acknowledge'])
        ->middleware('role:pegawai')
        ->name('payrolls.acknowledge');
});

// Route Publik (Verifikasi QR Code) - HARUS DI PALING BAWAH
Route::get('payrolls/{payroll}', [PayrollController::class, 'show'])->name('payrolls.show');
