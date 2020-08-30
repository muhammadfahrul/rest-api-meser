<?php

namespace App\Http\Controllers;

use App\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

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
        if($data) {
            Log::info('Showing all order');

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

    public function showAllOrderProduct()
    {
        $data = Order::with(array('product'=>function($query){
            $query->select();
        }))->get();
        if($data) {
            Log::info('Showing all order with product');

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

    public function showId($code)
    {
        $data = Order::where('code', $code)->get();
        if($data) {
            Log::info('Showing order by id');

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

    public function showIdOrderProduct($code)
    {
        $findId = Order::where('code', $code)->get();
        // $data = Order::where('code', $code)->with(array('product'=>function($query){
        //     $query->select();
        // }))->get();
        $data = DB::table('t_orders')
                    ->where('code', '=', $code)
                    ->join('t_products', 't_orders.product_id', '=', 't_products.id')
                    ->select('t_orders.*', 't_products.name', '=', 'product_name')
                    ->get();
        if($findId) {
            Log::info('Showing order with product by id');

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
            'code' => 'required',
        ]);

        $products = $request->input('products');

        for ($i=0; $i < count($products); $i++) { 
            $order = new Order();
            $order->code = $request->input('code');
            $order->quantity = $request->input('products.'.$i.'.quantity');
            $order->product_id = $request->input('products.'.$i.'.product_id');
            $order->save();

            DB::table('t_products')->where('id', '=', $order->product_id)->decrement('stock', $order->quantity);
        }

        Log::info('Adding order');

        return response()->json([
            "message" => "Success added",
            "status" => true,
        ]);
    }

    public function update(Request $request, $code)
    {
        $this->validate($request, [
            // 'code' => 'required',
        ]);
        
        $order = Order::where('code', $code)->first();
        if ($order) {
            $products = $request->input('products');

            for ($i=0; $i < count($products); $i++) { 
                $order->code = $request->input('code');
                $order->quantity = $request->input('products.'.$i.'.quantity');
                $order->product_id = $request->input('products.'.$i.'.product_id');
                $order->save();

                DB::table('t_products')->where('id', '=', $order->product_id)->increment('stock', $order->quantity);
                DB::table('t_products')->where('id', '=', $order->product_id)->decrement('stock', $order->quantity);
            }

            Log::info('Updating order by id');

            return response()->json([
                "message" => "Success updated",
                "status" => true
            ]);        
        }else {
            return response()->json([
                "message" => "Parameter not found",
                "status" => false
            ]);
        }

    }

    public function delete($code)
    {
        $order = Order::where('code', $code);
        if($order) {
            $order->delete();

            Log::info('Deleting order by id');

            return response()->json([
                "message" => "Success deleted",
                "status" => true
            ]);   
        }else {
            return response()->json([
                "message" => "Parameter not found",
                "status" => false
            ]);
        }
    }
}