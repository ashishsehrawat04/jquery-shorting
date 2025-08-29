<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\RequestController;

Route::get('/', function () {
    return view('welcome');
});
Route::get('/sign_up', function () {
    return view('signUp');
})->name('sign_up'); 


Route::post('login',[AuthController::class,'login'])->name(name: 'login');

Route::post('logout',[AuthController::class,'logout'])->name('logout');  
Route::get('users_data',[AuthController::class,'users_data'])->name('users_data');  
Route::get('get_users',[AuthController::class,'get_users'])->name('get_users');
Route::post('signupSubmit',[AuthController::class,'signupSubmit'])->name('signupSubmit');    
Route::get('past_users',[AuthController::class,'past_users'])->name('past_users');   

Route::Delete('delete_user/{id}',[Authcontroller::class,"destroy"]);
Route::Post('update_user/{id}',[Authcontroller::class,"update_user"]);  

Route::get('dashboard',[AuthController::class,'dashboard'])->name('dashboard');  





Route::get('chat_list',[AuthController::class,'chat'])->name('chat_list'); 
Route::get('messages/{id}',[AuthController::class,'getMessages'])->name('messages.send'); 
Route::post('/messages/send',[AuthController::class,'send_messages'])->name('send');   
