<?php

use App\Http\Controllers\API\ApiController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


//گروه بندی مسیر ها

Route::group(['prefix' => 'v1', 'as' => 'api.'], function () {

    //ثبت کاربر جدید 
    Route::post('/register', action: [ApiController::class, 'register'])->name('register');

    //ورود کاربر

    Route::post('/login', [ApiController::class, 'login'])->name('login');

    //دربافت تمام کاربران 

    Route::get('/all-users', action: [ApiController::class, 'getAllUsers'])->name('getAllUsers');

    //ویرایش کاربران 

    Route::put('/user/{userId}', action: [ApiController::class, 'editUser'])->name('editUser');

    //حدف کاربران

    Route::delete('/user/{userId}', action: [ApiController::class, 'deleteUser'])->name('deleteUser');

    //دسته بندی محصولات
    Route::post('/create-category', action: [ApiController::class, 'createCategory'])->name('createCategory');
});
