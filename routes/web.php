<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DropdownsController;
use App\Http\Controllers\RulesController;
use App\Http\Controllers\ShipmentCalendarsController;
use App\Http\Controllers\UsersController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return Auth::check()
        ? redirect()->route('home')
        : redirect()->route('login');
});

Route::get('/login', [AuthController::class, 'login'])->name('login');
Route::post('/auth/login', [AuthController::class, 'postLogin'])->name('auth.login');
Route::get('/logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware('auth')->group(function () {
    Route::get('/home', function () {
        return view('home.index');
    })->name('home');

    Route::post('/change-password', [AuthController::class, 'changePassword'])->name('changePassword');

    //Calendar Controller
    Route::get('/calendar', [ShipmentCalendarsController::class, 'index'])->name('calendar.index');
    Route::get('/calendar/events', [ShipmentCalendarsController::class, 'events'])->name('calendar.events');
    Route::post('/calendar', [ShipmentCalendarsController::class, 'store'])->name('calendar.store');
    Route::put('/calendar/{calendar}', [ShipmentCalendarsController::class, 'update'])->name('calendar.update');
    Route::delete('/calendar/{calendar}', [ShipmentCalendarsController::class, 'destroy'])->name('calendar.destroy');

    //Dropdown Controller
    Route::get('/dropdown', [DropdownsController::class, 'index'])->name('dropdown.index');
    Route::put('/dropdown/update/{id}', [DropdownsController::class, 'update'])->name('dropdown.update');
    Route::delete('/dropdown/delete/{id}', [DropdownsController::class, 'destroy'])->name('dropdown.destroy');
    Route::post('/dropdown/store', [DropdownsController::class, 'store'])->name('dropdown.store');

    //Rules Controller
    Route::get('/rule', [RulesController::class, 'index'])->name('rules.index');
    Route::post('/rule', [RulesController::class, 'store'])->name('rules.store');
    Route::put('/rule/{id}', [RulesController::class, 'update'])->name('rules.update');
    Route::delete('/rule/{id}', [RulesController::class, 'destroy'])->name('rules.destroy');

    //User Controller
    Route::get('/user', [UsersController::class, 'index'])->name('user.index');
    Route::post('/user/store', [UsersController::class, 'store'])->name('user.store');
    Route::post('/user/store-partner', [UsersController::class, 'storePartner']);
    Route::patch('/user/update/{user}', [UsersController::class, 'update'])->name('user.update');
    Route::post('/user/revoke/{id}', [UsersController::class, 'revoke'])->name('user.revoke');
    Route::post('/user/activate/{id}', [UsersController::class, 'activate'])->name('user.activate');
    Route::get('/user/{id}', [UsersController::class, 'get'])->name('user.get');
});
