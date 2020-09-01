<?php

namespace App\Http\Controllers;

use App\User;

use Validator;
use Firebase\JWT\JWT;
use Firebase\JWT\ExpiredException;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

use Image;

class AuthenticationController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    public function login(Request $request)
    {
        $this->validate($request, [
            'username' => 'required',
            'password' => 'required',
        ]);

        $username = $request->input('username');
        $password = $request->input('password');

        $user = User::where('username', $username)->first();

        if ($user && Hash::check($password, $user->password)) {
            return response()->json([
                "message" => "Login success",
                "status" => true,
                "data" => $user
            ]);
        }else {
            return response()->json([
                "message" => "Invalid username or Password",
                "status" => false
            ]);
        }
    }


    public function register(Request $request)
    {
        $this->validate($request, [
            'username' => 'required|unique:t_users',
            'email' => 'required|unique:t_users',
            'password' => 'required',
        ]);

        $data = new User();
        $data->username = $request->input('username');
        $data->email = $request->input('email');
        $data->password = Hash::make($request->input('password'));

        $checkUsername = User::where('username', $data->username)->first();
        $checkEmail = User::where('email', $data->email)->first();

        if ($checkUsername) {
            return response()->json([
                "message" => "Username already exists",
                "status" => false
            ]);
        }elseif ($checkEmail) {
            return response()->json([
                "message" => "Email already exists",
                "status" => false
            ]);
        }else {
            $data->save();

            return response()->json([
                "message" => "Register Success",
                "status" => true,
                "data" => $data
            ]);
        }
    }

}
