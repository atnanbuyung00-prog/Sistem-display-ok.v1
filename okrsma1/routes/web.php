<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ScheduleController;
use App\Http\Controllers\MasterDataController;
use App\Http\Controllers\ShiftController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\DisplayController;
use App\Http\Controllers\GreetingController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ActivityLogController;
use App\Http\Controllers\BackupController;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::get('/display', [DisplayController::class, 'index'])->name('display');
Route::get('/display/events', [DisplayController::class, 'events'])->name('display.events');

Route::middleware('auth')->prefix('admin')->name('admin.')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::middleware('permission:user.manage')->group(function () {
        Route::get('/greetings', [GreetingController::class, 'index'])->name('greetings.index');
        Route::post('/greetings', [GreetingController::class, 'store'])->name('greetings.store');
        Route::put('/greetings/{greeting}', [GreetingController::class, 'update'])->name('greetings.update');
        Route::delete('/greetings/{greeting}', [GreetingController::class, 'destroy'])->name('greetings.destroy');
    });
    Route::get('/', [DashboardController::class, 'index'])->middleware('permission:dashboard.view')->name('dashboard');

    Route::get('/display', [DisplayController::class, 'index'])->name('display');
    Route::get('/display/events', [DisplayController::class, 'events'])->name('display.events');

    Route::middleware('permission:schedule.view')->group(function () {
        Route::get('/schedules', [ScheduleController::class, 'index'])->name('schedules.index');
        Route::get('/schedules/create', [ScheduleController::class, 'create'])->middleware('permission:schedule.manage')->name('schedules.create');
        Route::post('/schedules', [ScheduleController::class, 'store'])->middleware('permission:schedule.manage')->name('schedules.store');
        Route::get('/schedules/manage', [ScheduleController::class, 'manage'])->middleware('permission:schedule.manage')->name('schedules.manage');
        Route::get('/schedules/{schedule}/edit', [ScheduleController::class, 'edit'])->middleware('permission:schedule.manage')->name('schedules.edit');
        Route::put('/schedules/{schedule}', [ScheduleController::class, 'update'])->middleware('permission:schedule.manage')->name('schedules.update');
        Route::delete('/schedules/{schedule}', [ScheduleController::class, 'destroy'])->middleware('permission:schedule.manage')->name('schedules.destroy');
        Route::get('/schedules/calendar/events', [ScheduleController::class, 'calendarEvents'])->name('schedules.calendar.events');
        Route::post('/schedules/{schedule}/move', [ScheduleController::class, 'move'])->middleware('permission:schedule.manage')->name('schedules.move');
        Route::get('/schedules/print', [ScheduleController::class, 'print'])->name('schedules.print');
        Route::get('/schedules/export', [ScheduleController::class, 'export'])->middleware('permission:schedule.export')->name('schedules.export');
        Route::post('/schedules/copy', [ScheduleController::class, 'copy'])->middleware('permission:schedule.manage')->name('schedules.copy');
    });

    Route::middleware('permission:master.manage')->group(function () {
        Route::get('/masters/{type}', [MasterDataController::class, 'index'])->name('masters.index');
        Route::post('/masters/{type}', [MasterDataController::class, 'store'])->name('masters.store');
        Route::put('/masters/{type}/{item}', [MasterDataController::class, 'update'])->name('masters.update');
        Route::delete('/masters/{type}/{item}', [MasterDataController::class, 'destroy'])->name('masters.destroy');
    });

    Route::middleware('permission:shift.manage')->group(function () {
        Route::get('/shifts', [ShiftController::class, 'index'])->name('shifts.index');
        Route::post('/shifts', [ShiftController::class, 'store'])->name('shifts.store');
        Route::get('/shifts/manage', [ShiftController::class, 'manage'])->name('shifts.manage');
        Route::post('/shifts/assignments/bulk', [ShiftController::class, 'storeAssignments'])->name('shifts.assignments.bulk');
        Route::post('/shifts/{shift}/assignments', [ShiftController::class, 'storeAssignment'])->name('shifts.assignments.store');
        Route::delete('/shifts/{shift}/assignments/{assignment}', [ShiftController::class, 'destroyAssignment'])->name('shifts.assignments.destroy');
        Route::put('/shifts/{shift}', [ShiftController::class, 'update'])->name('shifts.update');
        Route::delete('/shifts/{shift}', [ShiftController::class, 'destroy'])->name('shifts.destroy');
        Route::get('/shifts/export', [ShiftController::class, 'export'])->middleware('permission:shift.export')->name('shifts.export');
        Route::post('/shifts/copy', [ShiftController::class, 'copy'])->name('shifts.copy');
    });

    Route::middleware('permission:user.manage')->group(function () {
        Route::get('/users', [UserController::class, 'index'])->name('users.index');
        Route::post('/users', [UserController::class, 'store'])->name('users.store');
        Route::put('/users/{user}', [UserController::class, 'update'])->name('users.update');
        Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');
    });

    Route::middleware('permission:audit.view')->group(function () {
        Route::get('/activity-logs', [ActivityLogController::class, 'index'])->name('activity_logs.index');
        Route::get('/activity-logs/{activity_log}', [ActivityLogController::class, 'show'])->name('activity_logs.show');
    });

    Route::middleware('permission:backup.manage')->group(function () {
        Route::get('/backups', [BackupController::class, 'index'])->name('backups.index');
        Route::post('/backups', [BackupController::class, 'store'])->name('backups.store');
        Route::get('/backups/{filename}/download', [BackupController::class, 'download'])->name('backups.download');
        Route::post('/backups/restore', [BackupController::class, 'restore'])->name('backups.restore');
        Route::delete('/backups/{filename}', [BackupController::class, 'destroy'])->name('backups.destroy');
    });
});
