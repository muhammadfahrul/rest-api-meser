<?php

namespace App\Http\Controllers;

use App\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

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
        if(!empty($data)) {
            Log::info('Showing all product');

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

    public function showAllImage()
    {
        // $image_path = storage_path('images') . '/' . $name;
        // if (file_exists($image_path)) {
        //     $file = file_get_contents($image_path);

        //     return response($file, 200)->header('Content-Type', 'image/jpeg');
        // }

        // return response()->json([
        //     "message" => "Image Not Found",
        //     "status" => false
        // ]);

        $images = [];
        $files = Storage::disk('gcs')->files('product-images');
        if(!empty($files)) {
            foreach ($files as $file) {
                $images[] = [
                    'name' => str_replace('product-images/', '', $file),
                    'src'  => Storage::disk('gcs')->url($file),
                ];
            }
    
            return response()->json([
                "message" => "Success retrieve data",
                "status" => true,
                "data" => $images
            ]);
        }else {
            return response()->json([
                "message" => "Images not found",
                "status" => false
            ]);
        }
    }

    public function showAllProductOrder()
    {
        $data = Product::with(array('order'=>function($query){
            $query->select();
        }))->get();
        if(!empty($data)) {
            Log::info('Showing all product with order');

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

    public function showAllProductCategory()
    {
        $data = Product::with(array('category'=>function($query){
            $query->select();
        }))->get();
        if(!empty($data)) {
            Log::info('Showing all product with category');

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
        $data = Product::find($id);
        if($data) {
            Log::info('Showing product by id');

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

    public function showIdCategory($id)
    {
        $data = Product::where('category_id', $id)->get();
        if($data) {
            Log::info('Showing product with category by id');

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

    public function showIdProductCategory($id)
    {
        $findId = Product::find($id);
        $data = Product::where('id', $id)->with(array('category'=>function($query){
            $query->select();
        }))->get();
        if($findId) {
            Log::info('Showing product with category by id');

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
            'name' => 'required',
            'price' => 'required',
            'stock' => 'required',
            // 'image' => 'required|image',
            'category_id' => 'required|exists:t_categories,id'
        ]);
        
        $data = new Product();
        $data->name = $request->input('name');
        $data->price = $request->input('price');
        $data->stock = $request->input('stock');
        $data->image = $request->input('image');
        // $image = $request->file('image');
        // if(!empty($image)){
        //     // $rand = bin2hex(openssl_random_pseudo_bytes(100)).".".$image->extension();
        //     // $rand_md5 = md5($rand).".".$image->extension();
        //     // $data->image = $rand_md5;

        //     // $image->move(storage_path('images'),$rand_md5);
            
        //     $name = time() . '-' . $image->getClientOriginalName();
        //     $data->image = $name;
        //     $filePath = 'product-images/' . $name;
        //     Storage::disk('gcs')->put($filePath, file_get_contents($image));
        // }
        $data->category_id = $request->input('category_id');
        $data->save();

        Log::info('Adding product');

        return response()->json([
            "message" => "Success added",
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
            // 'image' => 'required|image',
            'category_id' => 'required|exists:t_categories,id'
        ]);
        
        $data = Product::find($id);
        if ($data) {
            // Storage::disk('gcs')->delete('product-images/' . $data->image);
            
            $data->name = $request->input('name');
            $data->price = $request->input('price');
            $data->stock = $request->input('stock');
            $data->image = $request->input('image');
            // $image = $request->file('image');
            // if(!empty($image)){
            //     // $rand = bin2hex(openssl_random_pseudo_bytes(100)).".".$image->extension();
            //     // $rand_md5 = md5($rand).".".$image->extension();
            //     // $data->image = $rand_md5;

            //     // $image->move(storage_path('images'),$rand_md5);
                
            //     $name = time() . '-' . $image->getClientOriginalName();
            //     $data->image = $name;
            //     $filePath = 'product-images/' . $name;
            //     Storage::disk('gcs')->put($filePath, file_get_contents($image));
            // }
            $data->category_id = $request->input('category_id');
            $data->save();

            Log::info('Updating product by id');

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
        $data = Product::find($id);
        if($data) {
            $data->delete();
            
            // Storage::disk('gcs')->delete('product-images/' . $data->image);

            Log::info('Deleting product by id');

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
}