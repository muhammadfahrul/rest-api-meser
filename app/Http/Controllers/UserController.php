<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
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

    public function showAll()
    {
        $data = User::all();
        if($data) {
            Log::info('Showing all user');

            return response()->json([
                "message" => "Success retrieve data",
                "status" => true,
                "data" => $data
            ]);
        }else {
            return response()->json([
                "message" => "Data not found",
                "status" => false
            ]);
        }
    }

    public function showId($id)
    {
        $data = User::find($id);
        if($data) {
            Log::info('Showing user by id');

            return response()->json([
                "message" => "Success retrieve data",
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

    public function add(Request $request)
    {
        $this->validate($request, [
            'username' => 'required|unique:users',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:8'
        ]);
        
        $data = new User();
        $data->username = $request->input('username');
        $data->email = $request->input('email');
        $data->password = Hash::make($request->input('password'));
        $data->status = "false";
        $data->token = "";
        $data->save();

        Log::info('Adding user');

        return response()->json([
            "message" => "Success added",
            "status" => true,
            "data" => $data
        ]);
    }

    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'username' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:8'
        ]);
        
        $data = User::find($id);
        if ($data) {
            $data->username = $request->input('username');
            $data->email = $request->input('email');
            $data->password = Hash::make($request->input('password'));
            $data->status = "false";
            $data->token = "";
            $data->save();

            Log::info('Updating user by id');

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

    public function delete($id)
    {
        $data = User::find($id);
        if($data) {
            $data->delete();

            Log::info('Deleting user by id');

            return response()->json([
                "message" => "Success deleted",
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

    public function deleteUsers()
    {
        $data = DB::table('users')->truncate();
        if($data) {
            Log::info('Deleting users');

            return response()->json([
                "message" => "Success deleted",
                "status" => true
            ]);   
        }else {
            return response()->json([
                "message" => "Data not found",
                "status" => false
            ]);
        }
    }
}