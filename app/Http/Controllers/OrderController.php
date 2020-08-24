<?php

namespace App\Http\Controllers;

use App\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class OrderController extends Controller
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
        $data = Order::all();
        if(!$data) {
            return response()->json([
                "message" => "Data Not Found"
            ]);
        }

        Log::info('Showing all order');

        return response()->json([
            "message" => "Success retrieve data",
            "status" => true,
            "data" => $data
        ]);
    }

    public function showAllJoin()
    {
        $data = Order::with(array('product'=>function($query){
            $query->select();
        }))->get();
        if(!$data) {
            return response()->json([
                "message" => "Data Not Found"
            ]);
        }

        Log::info('Showing all order with product');

        return response()->json([
            "message" => "Success retrieve data",
            "status" => true,
            "data" => $data
        ]);
    }

    public function showId($id)
    {
        $data = Order::find($id);
        if(!$data) {
            return response()->json([
                "message" => "Parameter Not Found"
            ]);
        }

        Log::info('Showing order by id');

        return response()->json([
            "message" => "Success retrieve data",
            "status" => true,
            "data" => $data
        ]);
    }

    public function showIdJoin($id)
    {
        $findId = Order::find($id);
        $data = Order::where('id', $id)->with(array('product'=>function($query){
            $query->select();
        }))->get();
        if(!$findId) {
            return response()->json([
                "message" => "Parameter Not Found"
            ]);
        }

        Log::info('Showing order with product by id');

        return response()->json([
            "message" => "Success retrieve data",
            "status" => true,
            "data" => $data
        ]);
    }

    public function add(Request $request)
    {
        $this->validate($request, [
            'id' => 'required',
            // 'products.quantity' => 'required',
            // 'products.product_id' => 'required|exists:t_products,id'
        ]);
        
        $order = new Order();
        $order->id = $request->input('id');

        $products = $request->input('products');

        for ($i=0; $i < count($products); $i++) { 
            $order->quantity = $request->input('products.'.$i.'.quantity');
            $order->product_id = $request->input('products.'.$i.'.product_id');
            $order->save();
        }

        Log::info('Adding order');

        return response()->json([
            "message" => "Success Added",
            "status" => true,
            "data" => $order
        ]);
    }

    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'id' => 'required',
            // 'products.quantity' => 'required',
            // 'products.product_id' => 'required|exists:t_products,id'
        ]);
        
        $order = Order::find($id);
        if ($order) {
            $order->id = $request->input('id');

            $products = $request->input('products');

            for ($i=0; $i < count($products); $i++) { 
                $order->quantity = $request->input('products.'.$i.'.quantity');
                $order->product_id = $request->input('products.'.$i.'.product_id');
                $order->save();
            }

            Log::info('Updating order by id');

            return response()->json([
                "message" => "Success Updated",
                "status" => true,
                "data" => $order
            ]);        
        }else {
            return response()->json([
                "message" => "Parameter Not Found"
            ]);
        }

    }

    public function delete($id)
    {
        $order = Order::find($id);
        if($order) {
            $order->delete();

            Log::info('Deleting order by id');

            return response()->json([
                "message" => "Success Deleted",
                "status" => true,
                "data" => $order
            ]);   
        }else {
            return response()->json([
                "message" => "Parameter Not Found"
            ]);
        }
    }
}