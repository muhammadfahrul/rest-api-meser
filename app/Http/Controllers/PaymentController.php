<?php

namespace App\Http\Controllers;

use App\Payment;
use App\Order;
use App\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

use App\Http\Controllers\Midtrans\Config;

// // Midtrans API Resources
// use App\Http\Controllers\Midtrans\Transaction;

// // Plumbing
// use App\Http\Controllers\Midtrans\ApiRequestor;
// use App\Http\Controllers\Midtrans\SnapApiRequestor;
// use App\Http\Controllers\Midtrans\Notification;
// use App\Http\Controllers\Midtrans\CoreApi;
// use App\Http\Controllers\Midtrans\Snap;

// // Sanitization
// use App\Http\Controllers\Midtrans\Sanitizer;

class PaymentController extends Controller
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
        $data = Payment::all();
        if($data) {
            Log::info('Showing all payment');

            return response()->json([
                "message" => "Success retrieve data",
                "status" => true,
                "data" => $data
            ]);
        }else {
            return response()->json([
                "message" => "Data Not Found"
            ]);
        }
    }

    public function showAllJoin()
    {
        $data = Payment::with(array('order'=>function($query){
            $query->select();
        }))->get();
        if($data) {
            Log::info('Showing all payment with order');

            return response()->json([
                "message" => "Success retrieve data",
                "status" => true,
                "data" => $data
            ]);
        }else {
            return response()->json([
                "message" => "Data Not Found"
            ]);
        }
    }

    public function showId($id)
    {
        $data = Payment::find($id);
        if($data) {
            Log::info('Showing payment by id');

            return response()->json([
                "message" => "Success retrieve data",
                "status" => true,
                "data" => $data
            ]);
        }else {
            return response()->json([
                "message" => "Parameter Not Found"
            ]);
        }
    }

    public function showIdJoin($id)
    {
        $findId = Payment::find($id);
        $data = Payment::where('id', $id)->with(array('order'=>function($query){
            $query->select();
        }))->get();
        if($findId) {
            Log::info('Showing payment with order by id');

            return response()->json([
                "message" => "Success retrieve data",
                "status" => true,
                "data" => $data
            ]);
        }else {
            return response()->json([
                "message" => "Parameter Not Found"
            ]);            
        }
    }

    public function add(Request $request)
    {
        $this->validate($request, [
            'payment_type' => 'required',
            'gross_amount' => 'required',
            'bank' => 'required_if:payment_type,bank_transfer',
            'order_code' => 'required|exists:t_orders,code'
        ]);
        
        $data = new Payment();
        $data->payment_type = $request->input('payment_type');
        $data->gross_amount = $request->input('gross_amount');
        $data->bank = $request->input('bank');
        $data->order_code = $request->input('order_code');
        if ($data->payment_type == "cash") {
            $data->transaction_id = 0;
            $data->transaction_time = "";
            $data->transaction_status = "success";
            $data->save();

            Log::info('Adding payment');   

            return response()->json([
                "message" => "Transaction with cash method is successful",
                "status" => true,
                "data" => $data
            ]);
        }elseif ($data->payment_type == "bank_transfer") {
            // $item_list = array();
            // $amount = 0;
            // Config::$serverKey = 'SB-Mid-server-VbqKS4xIPoo0ZR3Qu3xKt8Jj';
            // if (!isset(Config::$serverKey)) {
            //     return "Please set your payment server key";
            // }
            // Config::$isSanitized = true;

            // // Enable 3D-Secure
            // Config::$is3ds = true;
            
            $order_join = Order::where('code', $data->order_code)->with(array('product'=>function($query){
                $query->select();
            }))->get();
            $array_item = [];
            for ($i=0; $i < count($order_join); $i++) { 
                $array_item['id'] = $order_join[$i]['product']['id'];
                $array_item['price'] = $order_join[$i]['product']['price'];
                $array_item['quantity'] = $order_join[$i]['quantity'];
                $array_item['name'] = $order_join[$i]['product']['name'];
            }

            // Required
            $item_details = $array_item;

            // $transaction_details = array(
            //     'order_id' => $data->order_code,
            //     'gross_amount' => $data->gross_amount, // no decimal allowed for creditcard
            // );

            // $order = Order::find($data->order_code);
            // // $customer = Customer::find($order->user_id);

            // // Optional
            // $customer_details = array(
            //     'email' => 'messer@gmail.com',
            //     'first_name' => 'Messer',
            //     'last_name' => 'App',
            //     'phone' => '082467528825'
            // );

            // // Optional, remove this to display all available payment methods
            // $enable_payments = array($data->payment_type);

            // $bank_transfer_details = array(
            //     'bank' => $data->bank,
            //     'va_number' => mt_rand(100000, 999999)
            // );

            // // Fill transaction details
            // $transaction = array(
            //     'payment_type' => $enable_payments,
            //     'transaction_details' => $transaction_details,
            //     'customer_details' => $customer_details,
            //     'item_details' => $item_details,
            //     'bank_transfer' => $bank_transfer_details,
            // );
            // // return $transaction;
            // try {
            //     $chargeToken = CoreApi::charge($transaction);

            //     // return response()->json($chargeToken);
            //     return response()->json([
            //         "message" => "Transaction with bank transfer method is successful",
            //         "status" => true,
            //         "results" => $chargeToken,
            //         "data" => $data
            //     ]);
            // } catch (\Exception $e) {
            //     dd($e);
            //     // return ['code' => 0 , 'message' => 'failed'];
            //     return response()->json([
            //         "message" => "failed",
            //         "status" => false,
            //         // "results" => $chargeToken,
            //         // "data" => [
            //         //     "attributes" => $data
            //         // ]
            //     ]);
            // }
            
            $transaction_req = [
                "payment_type" => $data->payment_type,
                "transaction_details" => [
                    "order_id" => $data->order_code,
                    "gross_amount" => $data->gross_amount
                ],
                "customer_details" => [
                    "email" => "messer@gmail.com",
                    "first_name" => "Messer",
                    "last_name" => "App",
                    "phone" => "082576285642"
                ],
                'item_details' => $item_details,
                "bank_transfer" => [
                    "bank" => $data->bank,
                    "va_number" => mt_rand(100000, 999999),
                ]
            ];
    
            $url = 'https://api.sandbox.midtrans.com/v2/charge';

            $serverKey = base64_encode('SB-Mid-server-VbqKS4xIPoo0ZR3Qu3xKt8Jj:');
            
            $http_header = [
                'Content-Type' => 'application/json',
                'Authorization' => 'Basic '.$serverKey,
                'Accept' => 'application/json'
            ];
    
            $response = Http::withHeaders($http_header)->post($url, $transaction_req);
            // $results = response()->json($response);
            return $response->json();
            // if ( $data["status_code"] == "406") {
            //     return response()->json([
            //         "status" => "failed", 
            //         "message" => "Transaksi sudah dilakukan! periksa kembali order_id anda"
            //     ], 406);
            // }else {
            //     $data->transaction_id = $results["transaction_id"];
            //     $data->transaction_time = $results["transaction_time"];
            //     $data->transaction_status = $results["transaction_status"];
            //     if ($data->save()) {
            //         return response()->json([
            //             "message" => "Transaction with bank transfer method is successful",
            //             "status" => true,
            //             "data" => $data
            //         ]);
            //     }else {
            //         return response()->json([
            //             "message" => "failed",
            //             "status" => false
            //         ]);
            //     }

            //     Log::info('Adding payment'); 

            //     // $insertData = [
            //     //     "order_id" => $data["order_id"],
            //     //     "transaction_id" => $data["transaction_id"],
            //     //     "payment_type" => $data["payment_type"],
            //     //     "gross_amount" => $data["gross_amount"],
            //     //     "transaction_time" => $data["transaction_time"],
            //     //     "transaction_status" => $data["transaction_status"]
            //     // ];
            //     // if (Payment::create($insertData)){
            //     //     return response()->json(["status" => "success", 
            //     //                             "message" => "Transaksi berhasil mohon untuk menunggu konfirmasi!",
            //     //                             "results" => $insertData
            //     // ], 200);
            //     // } else {
            //     //     return response()->json(["status" => "failed",
            //     //                              "message" => "Data gagal disimpan!"
            //     // ], 401);
            //     // }
            // }
        }else {
            return response()->json([
                "message" => "An unexpected error occurred",
                "status" => false
            ]);
        }
    }

    public function delete($id)
    {
        $data = Payment::find($id);
        if($data) {
            $data->delete();

            Log::info('Deleting payment by id');

            return response()->json([
                "message" => "Transaction deleted successfully",
                "status" => true,
                "data" => [
                    "attributes" => $data
                ]
            ]);   
        }else {
            return response()->json([
                "message" => "Parameter Not Found"
            ]);
        }
    }

    public function midtransPush(Request $request)
    {
        $req = $request->all();
        $pay = Payment::where('order_code', $req['order_code'])->get();
        // return $pay;
        $pays = Payment::find($pay[0]->id);
        if($pay) {
            $pays->transaction_time = $req['transaction_time'];
            $pays->transaction_status = $req['transaction_status'];
            $pays->transaction_id = $req['transaction_id'];
            if($pays->save()) {
                return response()->json([
                    "messages" => "Transaction changes"
                ], 200);
            }
        }else {
            return response()->json([
                "messages" => "Order id not found",
                "status" => false
            ]);
        }
    }
}