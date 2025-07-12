<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;



class ApiController extends Controller
{
    //
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
    }
}
