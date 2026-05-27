<?php
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\PayrollController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\LoanController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\PasswordController;
use App\Http\Controllers\SocialAuthController;
use App\Http\Controllers\PsbController;
use App\Http\Controllers\ItjController;
use App\Http\Controllers\OvertimeController;
use App\Http\Controllers\PicketController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\DashboardController;
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

    // Menu Pengaturan (Khusus HRD & Superadmin)
    Route::middleware('role:hrd,superadmin')->group(function () {
        Route::get('settings', [SettingController::class, 'index'])->name('settings.index');
        Route::put('settings', [SettingController::class, 'update'])->name('settings.update');
    });

    // Menu Superadmin: Manajemen User Operasional
    Route::middleware('role:superadmin')->group(function () {
        Route::get('users', [UserController::class, 'index'])->name('users.index');
        Route::get('users/create', [UserController::class, 'create'])->name('users.create');
        Route::post('users', [UserController::class, 'store'])->name('users.store');
        Route::get('users/{user}/edit', [UserController::class, 'edit'])->name('users.edit');
        Route::put('users/{user}', [UserController::class, 'update'])->name('users.update');
        Route::patch('users/{user}/toggle', [UserController::class, 'toggleStatus'])->name('users.toggle');
    });

    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

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

    // Menu PSB (Work Orders)
    Route::get('psb/report', [PsbController::class, 'printReport'])->name('psb.report');
    Route::get('psb/report-detail', [PsbController::class, 'printDetailReport'])->name('psb.report_detail');
    Route::get('psb', [PsbController::class, 'index'])->name('psb.index');
    Route::middleware('role:staff')->group(function () {
        Route::get('psb/create', [PsbController::class, 'create'])->name('psb.create');
        Route::post('psb', [PsbController::class, 'store'])->name('psb.store');
        Route::delete('psb/{psb}', [PsbController::class, 'destroy'])->name('psb.destroy');
    });

    // Menu ITJ (Tarik Jalur)
    Route::get('itj/report', [ItjController::class, 'printReport'])->name('itj.report');
    Route::get('itj/report-detail', [ItjController::class, 'printDetailReport'])->name('itj.report_detail');
    Route::get('itj', [ItjController::class, 'index'])->name('itj.index');
    Route::middleware('role:staff')->group(function () {
        Route::get('itj/create', [ItjController::class, 'create'])->name('itj.create');
        Route::post('itj', [ItjController::class, 'store'])->name('itj.store');
        Route::delete('itj/{itj}', [ItjController::class, 'destroy'])->name('itj.destroy');
    });

    // Menu Lembur (Overtime)
    Route::get('overtime/report', [OvertimeController::class, 'printReport'])->name('overtime.report');
    Route::get('overtime/report-detail', [OvertimeController::class, 'printDetailReport'])->name('overtime.report_detail');
    Route::get('overtime', [OvertimeController::class, 'index'])->name('overtime.index');
    Route::middleware('role:staff')->group(function () {
        Route::get('overtime/create', [OvertimeController::class, 'create'])->name('overtime.create');
        Route::post('overtime', [OvertimeController::class, 'store'])->name('overtime.store');
        Route::delete('overtime/{overtime}', [OvertimeController::class, 'destroy'])->name('overtime.destroy');
    });

    // Menu Piket (Standby)
    Route::get('picket/report', [PicketController::class, 'printReport'])->name('picket.report');
    Route::get('picket/report-detail', [PicketController::class, 'printDetailReport'])->name('picket.report_detail');
    Route::get('picket', [PicketController::class, 'index'])->name('picket.index');
    Route::middleware('role:staff')->group(function () {
        Route::get('picket/create', [PicketController::class, 'create'])->name('picket.create');
        Route::post('picket', [PicketController::class, 'store'])->name('picket.store');
        Route::delete('picket/{picket}', [PicketController::class, 'destroy'])->name('picket.destroy');
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
    Route::get('payrolls/components/{employee}', [PayrollController::class, 'getEmployeeComponents'])->name('payrolls.components');
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
