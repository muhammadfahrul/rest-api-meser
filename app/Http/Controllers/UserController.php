<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

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
        if(!$data) {
            return response()->json([
                "message" => "Data Not Found"
            ]);
        }else {
            Log::info('Showing all user');

            return response()->json([
                "message" => "Success retrieve data",
                "status" => true,
                "data" => $data
            ]);
        }
    }

    public function showId($id)
    {
        $data = User::find($id);
        if(!$data) {
            return response()->json([
                "message" => "Parameter Not Found"
            ]);
        }else {
            Log::info('Showing user by id');

            return response()->json([
                "message" => "Success retrieve data",
                "status" => true,
                "data" => $data
            ]);
        }
    }

    public function add(Request $request)
    {
        $this->validate($request, [
            'username' => 'required',
            'password' => 'required|min:8'
        ]);
        
        $data = new User();
        $data->username = $request->input('username');
        $data->password = Hash::make($request->input('password'));
        $data->save();

        Log::info('Adding user');

        return response()->json([
            "message" => "Success Added",
            "status" => true,
            "data" => $data
        ]);
    }

    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'username' => 'required',
            'password' => 'required|min:8'
        ]);
        
        $data = User::find($id);
        if ($data) {
            $data->username = $request->input('username');
            $data->password = Hash::make($request->input('password'));
            $data->save();

            Log::info('Updating user by id');

            return response()->json([
                "message" => "Success Updated",
                "status" => true,
                "data" => $data
            ]);        
        }else {
            return response()->json([
                "message" => "Parameter Not Found"
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
                "message" => "Success Deleted",
                "status" => true,
                "data" => $data
            ]);   
        }else {
            return response()->json([
                "message" => "Parameter Not Found"
            ]);
        }
    }
}