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
            'username' => 'required|unique:users',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:8',
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

    public function sendEmail(Request $request)
    {
        $config = array(
            'driver' => 'smtp',
            'host' => 'smtp.gmail.com',
            'port' => 587,
            'from' => array('address' => 'from_email_id', 'name' => 'From_name'),
            'encryption' => 'tls',
            'username' => 'messerapp2020@gmail.com',
            'password' => 'mamangsekayu97',
            'sendmail' => '/usr/sbin/sendmail -bs',
            'pretend' => false
        );

        Config::set('mail',$config);

        try{
            Mail::raw('This is Email Body', function($msg) { 
                $msg->to(['harerdnyaneshwar@gmail.com']); $msg->from(['From_email']); 
            });

            echo "Mail Sent Successfully";

        }catch(Exception $e){
            echo "error Sending Mail <pre>";
            // print_r($e);
            echo "</pre>";
        }
    }

}
