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

    Route::get('/all-users', action: [ApiController::class, 'getAllUsers'])->name('getAllUsers')->middleware(['auth:sanctum', 'admin']);

    //ویرایش کاربران

    Route::put('/user/{userId}', action: [ApiController::class, 'editUser'])->name('editUser')->middleware('auth:sanctum');

    //حدف کاربران

    Route::delete('/user/{userId}', action: [ApiController::class, 'deleteUser'])->name('deleteUser')->middleware(['auth:sanctum', 'admin']);

    //دسته بندی محصولات

    Route::post('/create-category', action: [ApiController::class, 'createCategory'])->name('createCategory')->middleware(['auth:sanctum', 'admin']);

    //دریافت تمامی دسته بندی ها

    Route::get('/categories', action: [ApiController::class, 'getAllCategories'])->name('getAllCategories');

    //ویرایش دسته بندی

    Route::post('/category/{id}', action: [ApiController::class, 'editCategory'])->name('editCategory')->middleware(['auth:sanctum', 'admin']);

    //حذف دسنه بندی
    Route::delete('/category/{id}', action: [ApiController::class, 'deleteCategory'])->name('deleteCategory')->middleware(['auth:sanctum', 'admin']);

    //ساخت محصولات

    Route::post('/create-product', action: [ApiController::class, 'createProduct'])->name('createProduct')->middleware(['auth:sanctum', 'admin']);

    //دریافت تمامی محصولات

    Route::get('/products', action: [ApiController::class, 'getAllProducts'])->name('getAllProducts');

    // ویرایش محصولات

    Route::post('/product/{id}', action: [ApiController::class, 'editProduct'])->name('editProduct')->middleware(['auth:sanctum', 'admin']);

    //حذف محصولات
    Route::delete('/product/{id}', action: [ApiController::class, 'deleteProduct'])->name('deleteProduct')->middleware(['auth:sanctum', 'admin']);

    //ساخت مسیر های حمل و نقل
    Route::post('/create-shipping-method', action: [ApiController::class, 'createShippingMethod'])->name('createShippingMethod')->middleware(['auth:sanctum', 'admin']);;
    Route::get('/shipping-methods', action: [ApiController::class, 'getAllShippingMethods'])->name('getAllShippingMethods');
    Route::put('/shipping-method/{id}', action: [ApiController::class, 'editShippingMethod'])->name('editShippingMethod')->middleware(['auth:sanctum', 'admin']);;
    Route::delete('/shipping-method/{id}', action: [ApiController::class, 'deleteShippingMethod'])->name('deleteShippingMethod')->middleware(['auth:sanctum', 'admin']);
    Route::put('/change-shipping-method-status/{id}', action: [ApiController::class, 'changeShippingMethodStatus'])->name('changeShippingMethodStatus')->middleware(['auth:sanctum', 'admin']);

    //ساخت متدد پرداخت
    Route::post('/create-payment-method', action: [ApiController::class, 'createPaymentMethod'])->name('createPaymentMethod')->middleware(['auth:sanctum', 'admin']);
    Route::get('/payment-method', action: [ApiController::class, 'getAllPaymentMethods'])->name('getAllPaymentMethods');
    Route::put('/payment-method/{id}', action: [ApiController::class, 'editPaymentMethod'])->name('editPaymentMethod')->middleware(['auth:sanctum', 'admin']);
    Route::delete('/payment-method/{id}', action: [ApiController::class, 'deletePaymentMethod'])->name('deletePaymentMethod')->middleware(['auth:sanctum', 'admin']);
    Route::put('/change-payment-method-status/{id}', action: [ApiController::class, 'changePaymentMethodStatus'])->name('changePaymentMethodStatus')->middleware(['auth:sanctum', 'admin']);

    //ساخت متتد های سفارش
    Route::post('/create-order', action: [ApiController::class, 'createOrder'])->name('createOrder')->middleware('auth:sanctum');
    Route::get('/orders', action: [ApiController::class, 'getAllOrders'])->name('getAllOrders')->middleware(['auth:sanctum', 'admin']);;
    Route::put('/change-order-status/{order_id}', action: [ApiController::class, 'changeOrderStatus'])->name('changeOrderStatus')->middleware(['auth:sanctum', 'admin']);
    Route::put('/change-payment-status/{order_id}', action: [ApiController::class, 'changePaymentStatus'])->name('changePaymentStatus')->middleware(['auth:sanctum', 'admin']);
    Route::get('/pending-orders', action: [ApiController::class, 'getPendingOrders'])->name('getPendingOrders')->middleware(['auth:sanctum', 'admin']);
    Route::get('/processing-orders', action: [ApiController::class, 'getProcessingOrders'])->name('getProcessingOrders')->middleware(['auth:sanctum', 'admin']);
    Route::get('/completed-orders', action: [ApiController::class, 'getCompletedOrders'])->name('getCompletedOrders')->middleware(['auth:sanctum', 'admin']);

    //دریافت سفارشات کاربر
    Route::get('/user/orders/{user_id}', action: [ApiController::class, 'getUserOrders'])->name('getUserOrders')->middleware('auth:sanctum');

    //ادرس کاربر
    Route::post('/user/address', action: [ApiController::class, 'createUserAddress'])->name('createUserAddress')->middleware('auth:sanctum');
    Route::get('/user/address/{user_id}', action: [ApiController::class, 'getUserAddresses'])->name('getUserAddresses');
    Route::put('/user/address/{address_id}', action: [ApiController::class, 'editUserAddress'])->name('editUserAddress')->middleware('auth:sanctum');
    Route::delete('/user/address/{address_id}', action: [ApiController::class, 'deleteUserAddress'])->name('deleteUserAddress')->middleware('auth:sanctum');

    //جزییات سفارشات
    Route::get('/order/{order_id}', action: [ApiController::class, 'getOrderDetails'])->name('getOrderDetails')->middleware('auth:sanctum');

    //بخش نظرات محصولات 
    Route::post('/product-review', action: [ApiController::class, 'createProdutReview'])->name('createProdutReview')->middleware('auth:sanctum');
    Route::get('/product-reviews', action: [ApiController::class, 'getProductReviews'])->name('getProductReviews')->middleware('auth:sanctum');
    Route::get('/reviews/{product_id}', action: [ApiController::class, 'getReviewsByProduct'])->name('getReviewsByProduct')->middleware('auth:sanctum');
    Route::get('/reviews/{user_id}', action: [ApiController::class, 'getReviewsByUser'])->name('getReviewsByUser')->middleware('auth:sanctum');
    Route::put('/product-review/{review_id}', action: [ApiController::class, 'updateReviewStatus'])->name('updateReviewStatus')->middleware('auth:sanctum');
    Route::delete('/product-review/{review_id}', action: [ApiController::class, 'deleteReview'])->name('deleteReview')->middleware('auth:sanctum');
});
