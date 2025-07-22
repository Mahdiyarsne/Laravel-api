<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\OrderItem;
use App\Models\PaymentMethod;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Product;
use App\Models\Order;
use App\Models\ProductCategory;
use App\Models\ShippingMethod;
use App\Models\UserAddress;
use App\Trait\FileUploadTrait;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;


class ApiController extends Controller
{
    use FileUploadTrait;

    //ثبت کاربر
    public function register(Request $request)
    {
        $validator = Validator::make(data: $request->all(), rules: [
            'name' => 'required|min:4',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:8'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'fail',
                'message' => $validator->errors()
            ], 400);
        }

        $data = $request->all();
        $data['password'] = Hash::make($data['password']);


        $user = User::create($data);

        $response['name'] = $user->name;
        $response['email'] = $user->email;
        $response['token'] = $user->createToken("MyApp")->plainTextToken;

        return response()->json([
            'status' => 'success',
            'message' => 'New User Registered Successfully',
            'data' => $response
        ], 200);
    }

    //ورود کاربر

    public function login(Request $request)
    {        //اعتبار سنجی
        $validator = Validator::make(data: $request->all(), rules: [
            'email' => 'required',
            'password' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'fali',
                'message' => $validator->errors(),
            ], 400);
        }

        if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {

            $user = Auth::user();

            $response['token'] = $user->createToken('MyApp')->plainTextToken;
            $response['name'] = $user->name;
            $response['email'] = $user->email;
            $response['role'] = $user->role;
            return response()->json([
                'status' => 'success',
                'message' => 'Logged in Successfully',
                'data' => $response
            ], 200);
        } else {
            return response()->json([
                'status' => 'fali',
                'message' => 'Invalid Credentiales'

            ], 400);
        }
    }

    //دریافت تمامی کاربران
    public function getAllUsers()
    {
        $users = User::get();
        if (!$users) {
            return response()->json([
                'status' => 'fail',
                'message' => 'No User Found',
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'count' => count($users),
            'data' => $users
        ], 200);
    }


    public function editUser(int $userId, Request $request)
    {

        //صحت وجود داشتن کاربر

        $user = User::find($userId);
        if (!$user) {
            return response()->json([
                'status' => 'fail',
                'message' => 'No User Found'
            ], 404);
        }

        //درخواست اعتبار سنجی فیلد ها

        $validator = Validator::make($request->all(), rules: [
            'name' => 'required|min:4',
            'email' => 'required|email'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'fail',
                'message' => $validator->errors()
            ], 400);
        }

        $user->name = $request->name;
        $user->email = $request->email;
        $user->save();

        return response()->json([
            'status' => 'success',
            'message' => 'User Updated Successfully',
            'data' => $user
        ], 200);
    }

    //حدف کاربران
    public function deleteUser(int $userId)
    {

        //صحت وجود داشتن کاربر براساس ای دی

        $user = User::find($userId);
        if (!$user) {
            return response()->json([
                'status' => 'fail',
                'message' => 'No User Found'
            ], 404);
        }

        $user->delete(); //حذف کاربر

        //پیام صحت حدف کاربر
        return response()->json([
            'status' => 'sucess',
            'message' => 'User Deleted Successfully!',
        ], 200);
    }


    //دسته بندی محصولات

    public function createCategory(Request $request)
    {

        $validator = Validator::make(data: $request->all(), rules: [

            'name' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json([], 400);
        }

        $data['name'] = $request->name;
        $data['slug'] = Str::slug($request->name);
        $imagePath = $this->uploadImage($request, 'image');
        $data['image'] = isset($imagePath) ? $imagePath : '';

        ProductCategory::create($data);

        return response()->json([

            'status' => 'success',
            'message' => 'Category Created Successfully'

        ], 200);
    }

    //دریافت تمامی دسته بندی
    public function getAllCategories()
    {
        $categories = ProductCategory::get();

        if (!$categories) {
            return response()->json([
                'status' => 'fail',
                'message' => 'No Categories Found'
            ], 404);
        }
        return response()->json([
            'status' => 'success',
            'count' => count($categories),
            'data' => $categories
        ], 200);
    }

    //ویرایش دسته بندی ها

    public function editCategory(Request $request, int $categoryId)
    {

        $category = ProductCategory::find($categoryId);

        if (!$category) {
            return response()->json([
                'status' => 'fail',
                'message' => 'No Category Found'
            ], 404);
        }

        $validator = Validator::make(data: $request->all(), rules: [
            'name' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'fail',
                'message' => $validator->errors()
            ], 400);
        }

        $category->name = $request->name;
        $imagePath = $this->uploadImage($request, 'image');
        $category->image = isset($imagePath) ? $imagePath : $imagePath;
        $category->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Category Edited Successfully'
        ], 200);
    }

    //حذف دسته بندی  براساس ایدی
    public function deleteCategory(int $categoryId)
    {
        $category = ProductCategory::find($categoryId);

        if (!$category) {
            return response()->json([
                'status' => 'fail',
                'message' => 'No Category Found'
            ], 404);
        }

        $this->removeImage($category->image);

        $category->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Category Deleted Successfully'
        ], 200);
    }

    //ساخت محصولات

    public function createProduct(Request $request)
    {

        $validator = Validator::make(data: $request->all(), rules: [
            'name' => 'required',
            'category_id' => 'required',
            'price' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'fail',
                'message' => $validator->errors()
            ], 400);
        }

        $data['name'] = $request->name;
        $data['slug'] = Str::slug($request->name);
        $data['category_id'] = $request->category_id;
        $imagePath = $this->uploadImage($request, 'image');
        $data['image'] = isset($imagePath) ? $imagePath : '';
        $data['price'] = $request->price;
        $data['description'] = $request->description;

        Product::create($data);

        return response()->json([
            'status' => 'success',
            'message' => 'Product Created Successfully'
        ], 200);
    }

    //دریافت تمامی محصولات

    public function getAllProducts()
    {

        $products = Product::get();

        if (!$products) {
            return response()->json([
                'status' => 'fail',
                'message' => 'No Product Founded'
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'count' => count($products),
            'data' => $products
        ], 200);
    }

    //ویرایش محصولات

    public function editProduct(int $productId, Request $request)
    {

        $product = Product::find($productId);
        if (!$product) {
            return response()->json([
                'status' => 'fail',
                'message' => 'No Product Found'
            ], 404);
        }

        $validator = Validator::make(data: $request->all(), rules: [
            'name' => 'required',
            'category_id' => 'required',
            'price' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'fail',
                'message' => $validator->errors()
            ], 400);
        }

        $product->name = $request->name;
        $product->category_id = $request->category_id;
        $imagePath = $this->uploadImage($request, 'image');
        $product->image = isset($imagePath) ? $imagePath : $imagePath;
        $product->price = $request->price;
        $product->description = $request->description;
        $product->status = $request->status;

        $product->save();

        return response()->json(
            [
                'status' => 'success',
                'message' => 'Product Edited Successfully'
            ],
            200
        );
    }

    //حذف محصولات براساس ایدی

    public function deleteProduct(int $productId)
    {
        $product = Product::find($productId);
        if (!$product) {
            return response()->json([
                'status' => 'fail',
                'message' => 'No Product Found'
            ], 404);
        }

        $this->removeImage($product->image);
        $product->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Product Deleted Successfully'
        ], 200);
    }

    //ساخت متدد حمل و نقل

    public function createShippingMethod(Request $request)
    {
        $validator = Validator::make($request->all(), rules: [
            'name' => 'required',
            'method_code' => 'required',
            'shipping_price' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'fail',
                'message' => $validator->errors()
            ], 400);
        }

        $data['name'] = $request->name;
        $data['method_code'] = $request->method_code;
        $data['shipping_price'] = $request->shipping_price;

        ShippingMethod::create($data);

        return response()->json([
            'status' => 'success',
            'message' => 'Shipping Method Created Successfully'
        ], 200);
    }

    //دریافت تمامی حمل و نقل

    public function getAllShippingMethods()
    {
        $ShippingMethods = ShippingMethod::get();

        if (!$ShippingMethods) {
            return response()->json(
                [
                    'status' => 'fail',
                    'message' => 'No Product Founded'
                ],
                404
            );
        }

        return response()->json([
            'status' => 'success',
            'count' => count($ShippingMethods),
            'data' => $ShippingMethods
        ], 200);
    }


    //ویرایش حمل و نقل
    public function editShippingMethod(int $shippingMethodId, Request $request)
    {
        $shippingMethod = ShippingMethod::find($shippingMethodId);
        if (!$shippingMethod) {
            return response()->json([
                'status' => 'fail',
                'message' => 'No Product Found'
            ], 404);
        }

        $validator = Validator::make(data: $request->all(), rules: [
            'name' => 'required',
            'method_code' => 'required',
            'shipping_price' => 'required'
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status' => 'fail',
                'message' => $validator->errors()

            ], 400);
        }

        $shippingMethod->name = $request->name;
        $shippingMethod->method_code = $request->method_code;
        $shippingMethod->shipping_price = $request->shipping_price;

        $shippingMethod->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Shipping Method Edited Successfully'
        ], 200);
    }

    public  function  deleteShippingMethod(int $shippingMethodId)
    {
        $shippingMethod = ShippingMethod::find($shippingMethodId);

        if (!$shippingMethod) {
            return response()->json([
                'status' => 'fail',
                'message' => 'No Product Found'
            ], 404);
        }
        $shippingMethod->delete();
        return response()->json([
            'status' => 'success',
            'message' => 'Shipping Method Deleted Successfully'
        ], 200);
    }

    public function changeShippingMethodStatus(int $shippingMethodId)
    {

        $shippingMethod = ShippingMethod::find($shippingMethodId);
        if (!$shippingMethod) {
            return response()->json([
                'status' => 'fail',
                'message' => 'No Product Found'
            ], 404);
        }
        $shippingMethod->status = $shippingMethod->status == 1 ? 0 : 1;
        $shippingMethod->save();
        return response()->json([
            'status' => 'success',
            'message' => 'Shipping Method Status Changed Successfully'
        ], 200);
    }


    //ایجاد متدد پرداخت

    public  function createPaymentMethod(Request $request)
    {
        $validator = Validator::make(data: $request->all(), rules: [
            'name' => 'required',
            'method_code' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'fail',
                'message' => $validator->errors()
            ]);
        }

        $data['name'] = $request->name;
        $data['method_code'] = $request->method_code;

        PaymentMethod::create($data);

        return response()->json([
            'status' => 'success',
            'message' => 'Payment Method Created Successfully'
        ], 200);
    }

    public  function  getAllPaymentMethods()
    {
        $paymentMethods = PaymentMethod::get();
        if (!$paymentMethods) {
            return response()->json(
                [
                    'status' => 'fail',
                    'message' => 'No PaymentMethod Founded'
                ],
                404
            );
        }
        return response()->json([
            'status' => 'success',
            'count' => count($paymentMethods),
            'data' => $paymentMethods
        ], 200);
    }

    public  function  editPaymentMethod(int $paymentMethodId, Request $request)
    {

        $paymentMethod = PaymentMethod::find($paymentMethodId);

        if (!$paymentMethod) {
            return response()->json(

                ['status' => 'fail', 'message' => 'No Product Found'],
                404
            );
        }

        $validator = Validator::make($request->all(), rules: [
            'name' => 'required',
            'method_code' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status' => 'fail',
                'message' => $validator->errors()
            ], 400);
        }

        $paymentMethod->name = $request->name;
        $paymentMethod->method_code = $request->method_code;

        $paymentMethod->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Payment Method Edited Successfully'
        ], 200);
    }

    public function deletePaymentMethod(int $paymentMethodId)
    {
        $paymentMethod = PaymentMethod::find($paymentMethodId);
        if (!$paymentMethod) {
            return response()->json([
                'status' => 'fail',
                'message' => 'No Product Found'
            ], 404);
        }

        $paymentMethod->delete();
        return response()->json([
            'status' => 'success',
            'message' => 'Payment Method Deleted Successfully'
        ], 200);
    }

    public  function  changePaymentMethodStatus(int $paymentMethodId)
    {
        $paymentMethod = PaymentMethod::find($paymentMethodId);
        if (!$paymentMethod) {
            return response()->json([
                'status' => 'fail',
                'message' => 'No Product Found'
            ], 404);
        }
        $paymentMethod->status = $paymentMethod->status == 1 ? 0 : 1;
        $paymentMethod->save();
        return response()->json([
            'status' => 'success',
            'message' => 'Payment Method Status Changed Successfully'
        ], 200);
    }

    //ساخت متدد های پرداخت
    public function createOrder(Request $request)
    {
        $validator = Validator::make(data: $request->all(), rules: [
            'user_id' => 'required',
            'email' => 'required|email',
            'user_address_id' => 'required',
            'shipping_price' => 'required',
            'tax' => 'required',
            'grand_total' => 'required',
            'qty' => 'required',
            'shipping_method_id' => 'required',
            'payment_method_id' => 'required',
            'cart_items' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'fail',
                'message' => $validator->errors()
            ], 400);
        }

        $data['user_id'] = $request->user_id;
        $data['email'] = $request->email;
        $data['user_address_id'] = $request->user_address_id;
        $data['shipping_price'] = $request->shipping_price;
        $data['tax'] = $request->tax;
        $data['grand_total'] = $request->grand_total;
        $data['qty'] = $request->qty;
        $data['shipping_method_id'] = $request->shipping_method_id;
        $data['payment_method_id'] = $request->payment_method_id;

        $order =  Order::create($data);

        $orderId = $order->id;
        $cartItems = $request->cart_items;

        foreach ($cartItems as $cartItem) {
            $orderData['order_id'] = $orderId;
            $orderData['price'] = $cartItem['price'];
            $orderData['product_id'] = $cartItem['product_id'];
            $orderData['qty'] = $cartItem['qty'];

            OrderItem::create($orderData);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Order Created Successfully'
        ], 200);
    }

    public function getAllOrders()
    {
        $orders = Order::get();

        if (!$orders) {
            return response()->json(
                [
                    'status' => 'fail',
                    'message' => 'No Order Founded'
                ],
                404
            );
        }

        return response()->json([
            'status' => "success",
            'count' => count($orders),
            'data' => $orders
        ], 200);
    }

    public function changeOrderStatus(int $orderId, Request $request)
    {
        $order = Order::find($orderId);

        if (!$order) {
            return response()->json([
                'status' => 'fail',
                'message' => 'No Order Founded'
            ], 404);
        }

        $validator = Validator::make(data: $request->all(), rules: [
            'order_status' => 'required'

        ]);

        if ($validator->fails()) {

            return response()->json([

                'status' => 'fail',
                'message' => $validator->errors()

            ], 400);
        }

        $order->order_status = $request->order_status;
        $order->save();

        return response()->json([
            'status' => 'success',
            'message' => 'order status changed Successfully'

        ], 200);
    }

    public function changePaymentStatus(int $orderId, Request $request)
    {
        $order = Order::find($orderId);

        if (!$order) {
            return response()->json([
                'status' => 'fail',
                'message' => 'No Order Founded'
            ], 404);
        }

        $validator = Validator::make(data: $request->all(), rules: [
            'payment_status' => 'required'

        ]);

        if ($validator->fails()) {

            return response()->json([

                'status' => 'fail',
                'message' => $validator->errors()

            ], 400);
        }

        $order->payment_status = $request->payment_status;
        $order->save();

        return response()->json([
            'status' => 'success',
            'message' => 'payment status changed Successfully'

        ], 200);
    }

    public function getPendingOrders()
    {
        $orders = Order::where('order_status', 'pending')->get();

        if (!$orders) {
            return response()->json([
                'status' => 'fail',
                'message' => 'No Order Founded'
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'count' => count($orders),
            'data' => $orders
        ]);
    }

    public function getProcessingOrders()
    {
        $orders = Order::where('order_status', 'processing')->get();
        if (!$orders) {
            return response()->json([
                'status' => 'fail',
                'message' => 'No Order Founded'
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'count' => count($orders),
            'data' => $orders
        ], 200);
    }

    public function getCompletedOrders()
    {
        $orders = Order::where('order_status', 'completed')->get();
        if (!$orders) {
            return response()->json([
                'status' => 'fail',
                'message' => 'No Order Founded'
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'count' => count($orders),
            'data' => $orders
        ], 200);
    }

    //دریافت سفارشات کاربر

    public function getUserOrders(int $userId)
    {
        $user = User::find($userId);

        if (!$user) {
            return response()->json([
                'status' => 'fail',
                'message' => 'No user found'
            ], 404);
        }
        $orders = Order::where('user_id', $userId)->get();
        if (!$orders) {
            return response()->json([
                'status' => 'fail',
                'message' => 'No order found'
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'count' => count($orders),
            'data' => $orders
        ], 200);
    }

    //ایجاد آدرس کاربر

    public function createUserAddress(Request $request)
    {
        $validator = Validator::make(data: $request->all(), rules: [

            'user_id' => 'required',
            'address_line_one' => 'required',
            'city' => 'required',
            'state' => 'required',
            'zip' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'fail',
                'message' => $validator->errors()

            ], 400);
        }

        $user = User::find($request->user_id);
        if (!$user) {
            return response()->json([
                'status' => 'fail',
                'message' => 'No user found'
            ], 404);
        }

        $data = $request->all();
        UserAddress::create($data);
        return response()->json([
            'status' => 'success',
            'message' => 'Address create successfully'
        ], 200);
    }


    //دریافت تمامی ادرس کاربران
    public function getUserAddresses(int $userId)
    {

        $user = User::find($userId);
        if (!$user) {
            return response()->json([
                'status' => 'fail',
                'message' => 'no user Founded'
            ], 400);
        }

        $addresses = UserAddress::where('user_id', $userId)->get();

        if (!$addresses) {
            return response()->json([
                'status' => 'fail',
                'message' => 'No userAddress found'
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'count' => count($addresses),
            'data' => $addresses
        ], 200);
    }

    //ویرایش ادرس کاربران
    public function editUserAddress(int $addressId, Request $request)
    {
        $address = UserAddress::find($addressId);

        if (!$address) {
            return response()->json([
                'status' => 'fail',
                'message' => 'No user address found'
            ], 404);
        }

        $validator = Validator::make(data: $request->all(), rules: [
            'user_id' => 'required',
            'address_line_one' => 'required',
            'city' => 'required',
            'state' => 'required',
            'zip' => 'required'
        ]);
        if ($validator->fails()) {
            return response()->json([
                'state' => 'fail',
                'message' => $validator->errors()
            ], 400);
        }

        $address->user_id = $request->user_id;
        $address->address_line_one = $request->address_line_one;
        $address->address_line_two = $request->address_line_two;
        $address->city = $request->city;
        $address->state = $request->state;
        $address->zip = $request->zip;
        $address->country = $request->country;

        $address->save();

        return response()->json([
            'status' => 'success',
            'message' => 'user address updated sucessfully'
        ], 200);
    }


    //حذف ادرس کاربر
    public function deleteUserAddress(int $addressId)
    {
        $address = UserAddress::find($addressId);

        if (!$address) {
            return response()->json([
                'status' => 'fail',
                'message' => 'No user address founded'
            ], 404);
        }

        $address->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Deleted successfully'
        ], 200);
    }


    //جزییات سفارشات 
    public function getOrderDetails(int $orderId)
    {
        $order = Order::with(['orderItems'])->where('id', $orderId)->first();

        if (!$order) {
            return response()->json([
                'status' => 'fail',
                'message' => 'Faild to load order'
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data' => $order
        ], 200);
    }
}
