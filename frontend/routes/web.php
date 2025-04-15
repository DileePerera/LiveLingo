<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\MeetingController;
use App\Http\Controllers\UserController;
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

Route::get('/', function () {
    return view('welcome');
});

Route::controller(AuthController::class)->group(function () {
    //admin login and reg
    Route::get('/login', 'log_index')->name('log_index');
    Route::post('/login', 'log')->name('log');
    Route::get('/register', 'reg_index')->name('reg_index');
    Route::post('/register', 'reg')->name('reg');


    Route::get('/logOut', 'logout')->name('logout');
});

Route::controller(UserController::class)->middleware('UserIsAuth')->group(function () {
    Route::get('/dashboard', 'dashboard')->name('dashboard');
});

Route::controller(MeetingController::class)->middleware('UserIsAuth')->group(function () {
    Route::get('/meeting/create', 'create')->name('meeting.create');
    Route::post('/meeting/store', 'store')->name('meeting.store');
    Route::get('/meeting/{id}', 'start')->name('meeting.start');
});