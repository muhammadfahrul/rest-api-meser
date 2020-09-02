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
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Config;

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

        $token = User::where('status', 'true')->first();

        if ($user && $token && Hash::check($password, $user->password)) {
            return response()->json([
                "message" => "Login success",
                "status" => true,
                "data" => $user
            ]);
        }elseif (!$user) {
            return response()->json([
                "message" => "Invalid username or Password",
                "status" => false
            ]);
        }else {
            return response()->json([
                "message" => "Please activate your email first",
                "status" => false
            ]);
        }
    }


    public function register(Request $request)
    {
        $this->validate($request, [
            'username' => 'required|unique:users',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:8',
        ]);

        $data = new User();
        $data->username = $request->input('username');
        $data->email = $request->input('email');
        $data->password = Hash::make($request->input('password'));
        $data->status = "false";
        $data->token = Str::random(10);

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

            Mail::send('email-activation', ['token' => $data->token], function ($req)
            {
                $req->subject('Email Activation');
                $req->from('messerapp2020@gmail.com', 'messer app');
                $req->to($data->email);
                // $email->setBody('<h1>Hi, welcome user!</h1>', 'text/html');
            });

            return response()->json([
                "message" => "Register Success",
                "status" => true,
                "data" => $data
            ]);
        }
    }

    public function emailActivation(Request $request, $token)
    {
        $data = User::where('token', $token)->first();

        if ($data) {
            $data->status = "true";
            $data->save();

            return response()->json([
                "message" => "Successful activation of email",
                "status" => true
            ]);
        }else {
            return response()->json([
                "message" => "Token Not Found",
                "status" => false
            ]);
        }
    }

    public function sendEmail(Request $request)
    {
        $this->validate($request, [
            'email' => 'required|email',
            'name' => 'required',
            'message' => 'required',
            'subject' => 'required'
        ]);

        try{
            $data = [
                'names' => $request->name, 
                'messages' => $request->message
            ];

            Mail::send('reset-password', $data, function ($message) use ($request)
            {
                $message->subject($request->subject);
                $message->from('messerapp2020@gmail.com', 'messer app');
                $message->to($request->email);
                // $message->setBody('<h1>Hi, welcome user!</h1>', 'text/html');
            });
            // return back()->with('alert-success','Berhasil Kirim Email');
            return response()->json([
                "message" => "Successfully sending email",
                "status" => true,
                // "data" => $data['name']
            ]);
        }
        catch (Exception $e){
            // return response (['status' => false,'errors' => $e->getMessage()]);
            return response()->json([
                "message" => $e->getMessage(),
                "status" => false
            ]);
        }
    }

    public function resetRequest(Request $request)
    {
        $this->validate($request, [
            'email' => 'required'
        ]);

        $email = $request->input('email');

        $user = User::where('email', $email)->first();

        if ($user) {
            return response()->json([
                "message" => "Email found",
                "status" => true
            ]);
        }else {
            return response()->json([
                "message" => "Email not found",
                "status" => false
            ]);
        }
    }

    public function reset(Request $request)
    {
        $this->validate($request, [
            'email' => 'required|email',
            'password' => 'required|min:8'
        ]);
        
        $email = $request->input('email');
        
        $data = User::where('email', $email)->first();
        if ($data) {
            $data->password = Hash::make($request->input('password'));
            $data->save();

            Log::info('Updating user by email');

            return response()->json([
                "message" => "Success updated",
                "status" => true,
                "data" => $data
            ]);        
        }else {
            return response()->json([
                "message" => "Parameter not found",
                "status" => false
            ]);
        }
    }

}
