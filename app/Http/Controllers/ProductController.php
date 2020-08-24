<?php

namespace App\Http\Controllers;

use App\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ProductController extends Controller
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
        $data = Product::all();
        if(!$data) {
            return response()->json([
                "message" => "Data Not Found"
            ]);
        }else {
            Log::info('Showing all product');

            return response()->json([
                "message" => "Success retrieve data",
                "status" => true,
                "data" => $data
            ]);
        }
    }

    public function showAllJoin()
    {
        $data = Product::with(array('category'=>function($query){
            $query->select();
        }))->get();
        if(!$data) {
            return response()->json([
                "message" => "Data Not Found"
            ]);
        }

        Log::info('Showing all product with category');

        return response()->json([
            "message" => "Success retrieve data",
            "status" => true,
            "data" => $data
        ]);
    }

    public function showId($id)
    {
        $data = Product::find($id);
        if(!$data) {
            return response()->json([
                "message" => "Parameter Not Found"
            ]);
        }else {
            Log::info('Showing product by id');

            return response()->json([
                "message" => "Success retrieve data",
                "status" => true,
                "data" => $data
            ]);
        }
    }

    public function showIdJoin($id)
    {
        $findId = Product::find($id);
        $data = Product::where('id', $id)->with(array('category'=>function($query){
            $query->select();
        }))->get();
        if(!$findId) {
            return response()->json([
                "message" => "Parameter Not Found"
            ]);
        }

        Log::info('Showing product with category by id');

        return response()->json([
            "message" => "Success retrieve data",
            "status" => true,
            "data" => $data
        ]);
    }

    public function add(Request $request)
    {
        $this->validate($request, [
            'name' => 'required',
            'price' => 'required',
            'stock' => 'required',
            'image' => 'required|image',
            'category_id' => 'required|exists:t_categories,id'
        ]);
        
        $data = new Product();
        $data->name = $request->input('name');
        $data->price = $request->input('price');
        $data->stock = $request->input('stock');
        $image = $request->file('image');
        if(!empty($image)){
            $rand = bin2hex(openssl_random_pseudo_bytes(100)).".".$image->extension();
            $rand_md5 = md5($rand).".".$image->extension();
            $data->image = $rand_md5;

            $image->move(storage_path('images'),$rand_md5);
        }
        $data->category_id = $request->input('category_id');
        $data->save();

        Log::info('Adding product');

        return response()->json([
            "message" => "Success Added",
            "status" => true,
            "data" => $data
        ]);
    }

    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'name' => 'required',
            'price' => 'required',
            'stock' => 'required',
            'image' => 'required|image',
            'category_id' => 'required|exists:t_categories,id'
        ]);
        
        $data = Product::find($id);
        if ($data) {
            $data->name = $request->input('name');
            $data->price = $request->input('price');
            $data->stock = $request->input('stock');
            $image = $request->file('image');
            if(!empty($image)){
                $rand = bin2hex(openssl_random_pseudo_bytes(100)).".".$image->extension();
                $rand_md5 = md5($rand).".".$image->extension();
                $data->image = $rand_md5;

                $image->move(storage_path('images'),$rand_md5);
            }
            $data->category_id = $request->input('category_id');
            $data->save();

            Log::info('Updating product by id');

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
        $data = Product::find($id);
        if($data) {
            $data->delete();

            Log::info('Deleting product by id');

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