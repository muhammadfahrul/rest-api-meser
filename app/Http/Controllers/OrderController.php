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
        $data = Order::where('code', $id)->with(array('product'=>function($query){
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
            'code' => 'required',
            // 'products.quantity' => 'required',
            // 'products.product_id' => 'required|exists:t_products,id'
        ]);

        $products = $request->input('products');

        for ($i=0; $i < count($products); $i++) { 
            $order = new Order();
            $order->code = $request->input('code');
            $order->quantity = $request->input('products.'.$i.'.quantity');
            $order->product_id = $request->input('products.'.$i.'.product_id');
            $order->save();
        }

        Log::info('Adding order');

        return response()->json([
            "message" => "Success Added",
            "status" => true,
        ]);
    }

    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'code' => 'required',
            // 'products.quantity' => 'required',
            // 'products.product_id' => 'required|exists:t_products,id'
        ]);
        
        $order = Order::find($id);
        if ($order) {
            $products = $request->input('products');

            for ($i=0; $i < count($products); $i++) { 
                $order->code = $request->input('code');
                $order->quantity = $request->input('products.'.$i.'.quantity');
                $order->product_id = $request->input('products.'.$i.'.product_id');
                $order->save();
            }

            Log::info('Updating order by id');

            return response()->json([
                "message" => "Success Updated",
                "status" => true
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
                "status" => true
            ]);   
        }else {
            return response()->json([
                "message" => "Parameter Not Found"
            ]);
        }
    }
}