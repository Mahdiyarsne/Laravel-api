<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\ShippingMethod;
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

    public function getAllShippingMethods(){
        $ShippingMethods= ShippingMethod::get();

        if (!$ShippingMethods) {
            return response()->json(
                ['status' => 'fail',
                 'message' => 'No Product Founded'
                ],404
            );
        }

        return response()->json([
            'status' => 'success',
            'count' => count($ShippingMethods),
            'data' => $ShippingMethods
        ],200);
    }


    //ویرایش حمل و نقل
    public function editShippingMethod(int $shippingMethodId, Request $request){
        $shippingMethod = ShippingMethod::find($shippingMethodId);
        if (!$shippingMethod) {
            return response()->json([
                'status' => 'fail',
                'message' => 'No Product Found'
            ],404);
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

            ],400);
        }

        $shippingMethod->name = $request->name;
        $shippingMethod->method_code = $request->method_code;
        $shippingMethod->shipping_price = $request->shipping_price;

        $shippingMethod->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Shipping Method Edited Successfully'
        ],200);
    }

    public  function  deleteShippingMethod(int $shippingMethodId){
        $shippingMethod=ShippingMethod::find($shippingMethodId);

        if (!$shippingMethod) {
        return response()->json([
            'status' => 'fail',
            'message' => 'No Product Found'
        ],404);
        }
        $shippingMethod->delete();
        return response()->json([
            'status' => 'success',
            'message' => 'Shipping Method Deleted Successfully'
        ],200);
    }
}
