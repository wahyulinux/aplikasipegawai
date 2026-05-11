<?php

use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\PayrollController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('payrolls.index');
});

Route::resource('employees', EmployeeController::class);
Route::resource('payrolls', PayrollController::class)->only(['index', 'create', 'store', 'show', 'edit', 'update', 'destroy']);
Route::patch('payrolls/{payroll}/approve', [PayrollController::class, 'approve'])->name('payrolls.approve');
