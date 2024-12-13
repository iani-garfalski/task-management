<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;

// These routes are for non-API purposes (typically for views or web forms)
Route::get('/', function () {
    return view('home');
});


//use App\Http\Controllers\FrontendController;

// Route::get('/', [FrontendController::class, 'showLogin'])->name('login');
// Route::get('/app', [FrontendController::class, 'showApp'])->middleware('guest')->name('app');
// Web authentication routes

// Web authentication routes
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post'); // For web login
Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
Route::post('/register', [AuthController::class, 'register'])->name('register.post'); // For web registration
Route::post('/logout', [AuthController::class, 'logout'])->name('logout'); // For web logout

Route::get('/categories', [CategoryController::class, 'create'])->name('categories.index');
