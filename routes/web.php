<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

Route::get('/', function () {
    return view('welcome');
});


Route::post('login',[AuthController::class,'login'])->name('login');

Route::post('logout',[AuthController::class,'logout'])->name('logout');  
Route::get('users_data',[AuthController::class,'users_data'])->name('users_data');  
Route::get('get_users',[AuthController::class,'get_users'])->name('get_users');
Route::get('past_users',[AuthController::class,'past_users'])->name('past_users');   



Route::get('dashboard',[AuthController::class,'dashboard'])->name('dashboard');  






