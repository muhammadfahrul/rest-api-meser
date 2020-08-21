<?php

namespace App\Http\Controllers;

use App\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class CategoryController extends Controller
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
        $data = Category::all();
        if(!$data) {
            return response()->json([
                "message" => "Data Not Found"
            ]);
        }else {
            Log::info('Showing all category');

            return response()->json([
                "message" => "Success retrieve data",
                "status" => true,
                "data" => $data
            ]);
        }
    }

    public function showId($id)
    {
        $data = Category::find($id);
        if(!$data) {
            return response()->json([
                "message" => "Parameter Not Found"
            ]);
        }else {
            Log::info('Showing category by id');

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
            'name' => 'required'
        ]);
        
        $data = new Category();
        $data->name = $request->input('name');
        $data->save();

        Log::info('Adding category');

        return response()->json([
            "message" => "Success Added",
            "status" => true,
            "data" => $data
        ]);
    }

    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'name' => 'required'
        ]);
        
        $data = Category::find($id);
        if ($data) {
            $data->name = $request->input('name');
            $data->save();

            Log::info('Updating category by id');

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
        $data = Category::find($id);
        if($data) {
            $data->delete();

            Log::info('Deleting category by id');

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