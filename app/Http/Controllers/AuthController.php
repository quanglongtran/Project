<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login', 'register']]);
    }

    public function register(Request $request)
    {
        $gender = $request->gender;
        $account_type = $request->account_type;

        $validator = Validator::make($request->all(), [
            'username' => 'required|string|min:5',
            'email' => 'required|email|string|unique:users',
            'password' => 'required|string|confirmed|min:6',
            'fullname' => 'string|min:2',
            'birthday' => 'date_format:Y-m-d|before_or_equal:today',
            'identity_number' => 'string|min:10|unique:users',
            'address' => 'string'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors()->toJson(), 400);
        }

        if (!is_null($gender)) {
            if ($gender == 'male' or $gender == 1) {
                $gender = 1;
            } else {
                $gender = 0;
            }
        }

        if (!is_null($account_type)) {
            if ($account_type == 'enterprise' or $account_type == 1) {
                $account_type = 1;
            } else {
                $account_type = 0;
            }
        }

        $inputInsert = array_merge(
            $validator->validated(),
            [
                'password' => bcrypt($request->password),
                'gender' => $gender,
                'account_type' => $account_type,
                'fullname' => $validator->validated()['fullname']
            ]
        );

        // return $inputInsert;

        $user = User::create($inputInsert);

        return response()->json([
            'message' => 'Đăng ký thành công',
            'user' => $user,
            'code' => 201
        ], 201);
    }

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'username' => 'required|string|min:5',
            'password' => 'required|string|min:6',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors()->toJson(), 422);
        }

        

        $token = Auth::attempt($validator->validated());
        
        if ($token == false) {
            return response()->json([
                'message' => 'Đăng nhập thất bại!',
                'code' => 401
            ], 401);
        }

        
        Log::info(['333333333333' => $token]);
        return $this->createNewToken($token);
    }

    public function createNewToken($token)
    {

        return response()->json([
            'access_token' => $token,
            'type' => 'Bearer',
            'expired_in' => auth()->factory()->getTTL() * 60,
            'user' => auth()->user(),
            'code' => 200,
            'message' => 'Đăng nhập thành công'
        ]);
    }

    public function profile()
    {
        return response()->json(auth()->user());
    }

    public function logout()
    {
        Auth::logout();
        return response()->json([
            'message' => 'Đăng xuất thành công!'
        ], 200);
    }
}
