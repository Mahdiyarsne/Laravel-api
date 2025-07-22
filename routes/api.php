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

    //دریافت تمامی دسته بندی ها

    Route::get('/categories', action: [ApiController::class, 'getAllCategories'])->name('getAllCategories');

    //ویرایش دسته بندی

    Route::post('/category/{id}', action: [ApiController::class, 'editCategory'])->name('editCategory');

    //حذف دسنه بندی
    Route::delete('/category/{id}', action: [ApiController::class, 'deleteCategory'])->name('deleteCategory');

    //ساخت محصولات

    Route::post('/create-product', action: [ApiController::class, 'createProduct'])->name('createProduct');

    //دریافت تمامی محصولات

    Route::get('/products', action: [ApiController::class, 'getAllProducts'])->name('getAllProducts');

    // ویرایش محصولات

    Route::post('/product/{id}', action: [ApiController::class, 'editProduct'])->name('editProduct');

    //حذف محصولات
    Route::delete('/product/{id}', action: [ApiController::class, 'deleteProduct'])->name('deleteProduct');

    //ساخت مسیر های حمل و نقل
    Route::post('/create-shipping-method', action: [ApiController::class, 'createShippingMethod'])->name('createShippingMethod');
    Route::get('/shipping-methods', action: [ApiController::class, 'getAllShippingMethods'])->name('getAllShippingMethods');
    Route::put('/shipping-method/{id}', action: [ApiController::class, 'editShippingMethod'])->name('editShippingMethod');
    Route::delete('/shipping-method/{id}', action: [ApiController::class, 'deleteShippingMethod'])->name('deleteShippingMethod');
    Route::put('/change-shipping-method-status/{id}', action: [ApiController::class, 'changeShippingMethodStatus'])->name('changeShippingMethodStatus');

    //ساخت متدد پرداخت
    Route::post('/create-payment-method', action: [ApiController::class, 'createPaymentMethod'])->name('createPaymentMethod');
    Route::get('/payment-method', action: [ApiController::class, 'getAllPaymentMethods'])->name('getAllPaymentMethods');
    Route::put('/payment-method/{id}', action: [ApiController::class, 'editPaymentMethod'])->name('editPaymentMethod');
    Route::delete('/payment-method/{id}', action: [ApiController::class, 'deletePaymentMethod'])->name('deletePaymentMethod');
    Route::put('/change-payment-method-status/{id}', action: [ApiController::class, 'changePaymentMethodStatus'])->name('changePaymentMethodStatus');

    //ساخت متتد های سفارش
    Route::post('/create-order', action: [ApiController::class, 'createOrder'])->name('createOrder');
    Route::get('/orders', action: [ApiController::class, 'getAllOrders'])->name('getAllOrders');
    Route::put('/change-order-status/{order_id}', action: [ApiController::class, 'changeOrderStatus'])->name('changeOrderStatus');
    Route::put('/change-payment-status/{order_id}', action: [ApiController::class, 'changePaymentStatus'])->name('changePaymentStatus');
    Route::get('/pending-orders', action: [ApiController::class, 'getPendingOrders'])->name('getPendingOrders');
    Route::get('/processing-orders', action: [ApiController::class, 'getProcessingOrders'])->name('getProcessingOrders');
    Route::get('/completed-orders', action: [ApiController::class, 'getCompletedOrders'])->name('getCompletedOrders');

    //دریافت سفارشات کاربر
    Route::get('/user/orders/{user_id}', action: [ApiController::class, 'getUserOrders'])->name('getUserOrders');

    //ادرس کاربر
    Route::post('/user/address', action: [ApiController::class, 'createUserAddress'])->name('createUserAddress');
    Route::get('/user/address/{user_id}', action: [ApiController::class, 'getUserAddresses'])->name('getUserAddresses');
    Route::put('/user/address/{address_id}', action: [ApiController::class, 'editUserAddress'])->name('editUserAddress');
    Route::delete('/user/address/{address_id}', action: [ApiController::class, 'deleteUserAddress'])->name('deleteUserAddress');

    //جزییات سفارشات
    Route::get('/order/{order_id}', action: [ApiController::class, 'getOrderDetails'])->name('getOrderDetails');

    //بخش نظرات محصولات 
    Route::post('/product/review', action: [ApiController::class, 'createProdutReview'])->name('createProdutReview');
});
