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
        if($data) {
            Log::info('Showing all category');

            return response()->json([
                "message" => "Success retrieve data",
                "status" => true,
                "data" => $data
            ]);
        }else {
            return response()->json([
                "message" => "Data not found"
            ]);
        }
    }

    public function showAllCategoryProduct()
    {
        $data = Category::with(array('product'=>function($query){
            $query->select();
        }))->get();
        if($data) {
            Log::info('Showing all category with product');

            return response()->json([
                "message" => "Success retrieve data",
                "status" => true,
                "data" => $data
            ]);
        }else {
            return response()->json([
                "message" => "Data not found"
            ]);
        }
    }

    public function showId($id)
    {
        $data = Category::find($id);
        if($data) {
            Log::info('Showing category by id');

            return response()->json([
                "message" => "Success retrieve data",
                "status" => true,
                "data" => $data
            ]);
        }else {
            return response()->json([
                "message" => "Parameter not found"
            ]);
        }
    }

    public function showIdCategoryProduct($id)
    {
        $findId = Category::find($id);
        $data = Category::where('id', $id)->with(array('product'=>function($query){
            $query->select();
        }))->get();
        if($findId) {
            Log::info('Showing category with product by id');

            return response()->json([
                "message" => "Success retrieve data",
                "status" => true,
                "data" => $data
            ]);
        }else {
            return response()->json([
                "message" => "Parameter not found"
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
            "message" => "Success added",
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
                "message" => "Success updated",
                "status" => true,
                "data" => $data
            ]);        
        }else {
            return response()->json([
                "message" => "Parameter not found"
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
                "message" => "Success deleted",
                "status" => true,
                "data" => $data
            ]);   
        }else {
            return response()->json([
                "message" => "Parameter not found"
            ]);
        }
    }
}