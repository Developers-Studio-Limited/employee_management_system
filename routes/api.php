<?php

use App\Http\Controllers\Api\Admin\AdminController;
use App\Http\Controllers\Api\Admin\AuthController as AdminAuthController;
use App\Http\Controllers\Api\Admin\DepartmentController as AdminDepartmentController;
use App\Http\Controllers\Api\Admin\LeaveController as AdminLeaveController;
use App\Http\Controllers\Api\Admin\ResetPasswordController as AdminResetPasswordController;
use App\Http\Controllers\Api\Admin\SalaryController as AdminSalaryController;
use App\Http\Controllers\Api\Admin\VerifyEmailController;
use App\Http\Controllers\Api\Employee\AuthController as EmployeeAuthController;
use App\Http\Controllers\Api\Employee\EmployeeController as EmployeeEmployeeController;
use App\Http\Controllers\Api\Employee\ResetPasswordController;
use GuzzleHttp\Psr7\Request;
use Illuminate\Support\Facades\Route;

// Admin Authentication Routes
Route::prefix('admin')->group(function () {
    Route::post('login', [AdminAuthController::class, 'login']);
    Route::post('register', [AdminAuthController::class, 'register']);
    Route::post('forget-password', [AdminAuthController::class, 'forgetPassword']);
    Route::post('reset-password', [AdminResetPasswordController::class, 'resetPassword']);
});

Route::middleware("auth:api")->prefix('admin')->group(function()
{
    Route::get('profile', [AdminAuthController::class, 'profile']);
    Route::post('update-profile/{id}', [AdminAuthController::class, 'updateProfile']);
    Route::get('refresh', [AdminAuthController::class, 'RefreshToken']);
    Route::get('logout', [AdminAuthController::class, 'logout']);

    Route::middleware('role:admin')->group( function() {
        Route::get('employees', [AdminController::class, 'index']); 
        Route::post('create-employee', [AdminController::class, 'create']); 
        Route::get('employee/{id}', [AdminController::class, 'show']); 
        Route::put('update-employee/{id}', [AdminController::class, 'update']);  
        Route::get('delete-employee/{id}', [AdminController::class, 'delete']); 
        Route::get('restore-employee/{id}', [AdminController::class, 'restore']); 
        Route::post('create-salary/{id}', [AdminSalaryController::class, 'create']);
        Route::post('update-salary/{id}', [AdminSalaryController::class, 'update']);
        Route::post('create-department', [AdminDepartmentController::class, 'create']);
        Route::post('update-department/{id}', [AdminDepartmentController::class, 'update']);
        Route::post('leave-approved/{id}', [AdminLeaveController::class, 'leaveApproved']);
        Route::get('leave', [AdminLeaveController::class, 'index']);
    });
});

// Employee Authentication Routes
Route::prefix('employee')->group(function () {
    Route::post('login', [EmployeeAuthController::class, 'login']);
    Route::post('register', [EmployeeAuthController::class, 'register']);
    Route::post('forget-password', [EmployeeAuthController::class, 'forgetPassword']);
    Route::post('reset-password', [ResetPasswordController::class, 'resetPassword']);
});

Route::middleware("auth:employee-api")->prefix('employee')->group(function()  
{
    Route::post('apply-leave/{id}', [EmployeeEmployeeController::class, 'applyLeave']);
    Route::get('refresh', [EmployeeAuthController::class, 'RefreshToken']);
    Route::get('logout', [EmployeeAuthController::class, 'logout']);
    Route::get('profile', [EmployeeAuthController::class, 'profile']);
    Route::post('update-profile/{id}', [EmployeeAuthController::class, 'updateProfile']);
    Route::get('salary', [EmployeeEmployeeController::class, 'viewSalary']);
});

// Verify email
Route::get('/email/verify/{id}/{hash}', [VerifyEmailController::class, '__invoke'])
    ->middleware(['signed', 'throttle:6,1'])
    ->name('verification.verify');

Route::get('/email/verify/success', function () {
    return view('verification-success');
})->name('verification.success');

Route::get('/email/verify/already-success', function () {
    return view('verification-success');
})->name('verification.already-success');

// Resend link to verify email
Route::post('/email/verify/resend', function (Request $request) {
    $request->user()->sendEmailVerificationNotification();
    return back()->with('message', 'Verification link sent!');
})->middleware(['auth:api', 'throttle:6,1'])->name('verification.send');
