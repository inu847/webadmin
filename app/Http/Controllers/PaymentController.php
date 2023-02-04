<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Invoice;
use App\Models\InvoiceDetail;

use myFunction;
use getData;

use Veritrans;
use Midtrans;
use Session;
use Auth;

class PaymentController extends Controller
{
	public function __construct(){
	    Midtrans::$serverKey = getData::getCatalogSession('server_key');
	    Midtrans::$isProduction = false;
	}
	public function token($total) 
	{
		$invoice = Invoice::where('invoice_number',Session::get('cartInvoice'))->first();
		$itemdata = InvoiceDetail::where('invoiceid',$invoice['id'])->get();
		$array=[];
		foreach($itemdata as $data){
			$array[]=[
						'id'=>$data['item_id'],
						'price'=>($data['price']-$data['discount']),
						'quantity'=>$data['qty'],
						'name'=>$data['item']
					];
		}
		$gettax= ($total * getData::getCatalogSession('tax'))/100;
		if($gettax > 0){
			$tax = [[
							'id'=>'P01',
							'price'=>$gettax,
							'quantity'=>1,
							'name'=>"( Extra ) Tax ".getData::getCatalogSession('tax')."%"
						]];
			$merge = array_merge($array,$tax);
		}else{
			$merge = $array;
		}

	    $midtrans = new Midtrans;
	    $transaction_details = array(
	        'order_id'          => $invoice['invoice_number'],
	        'gross_amount'  => $total + $gettax
	    );
	    // Populate items
	    $items = $merge;

	    $customer_details = array(
	        'first_name'            => "Andri",
	        'last_name'             => "Setiawan",
	        'email'                     => "andrisetiawan@asdasd.com",
	        'phone'                     => "081322311801",
	    );
	    // Data yang akan dikirim untuk request redirect_url.
	    $transaction_data = array(
	        'transaction_details'=> $transaction_details,
	        'item_details'           => $items,
	        //'customer_details'   => $customer_details
	    );
	    
	    try
	    {
	        $snap_token = $midtrans->getSnapToken($transaction_data);
	        //return redirect($vtweb_url);
	        echo $snap_token;
	    } 
	    catch (Exception $e) 
	    {   
	        return $e->getMessage;
	    }
	}
	public function finishPayment(Request $request) 
	{
		if($request->isMethod('post')){
			$result = $request->input('result_data');
	        $result = json_decode($result,true);
	        if($result['status_code']=='400' or $result['status_code']=='401' or $result['status_code']=='402' or $result['status_code']=='404' or $result['status_code']=='406' or $result['status_code']=='410' or $result['status_code']=='412'){
	            $status = 'error';
	            $message = 'Pembayaran gagal.';
	        }else{
	        	if (Invoice::checkout_data($request)) {
	        	    $ch = curl_init();
	        	    curl_setopt($ch, CURLOPT_URL, 'https://admin.liataja.id/notif/' . Session::get('cartInvoice') . '/Checkout');
	        	    //curl_setopt($ch, CURLOPT_URL, \URL::to('/cms/notif/'.Session::get('cartInvoice').'/Checkout'));
	        	    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
	        	    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	        	    $output = curl_exec($ch);
	        	    curl_close($ch);

	        	    Session::put('myorder', Session::get('cartInvoice'));
	        	    Session::forget('cartInvoice');
	        	    $status = 'success';
	        	    $message = 'Your request was successful.';
	        	} else {
	        	    $status = 'error';
	        	    $message = 'Oh snap! something went wrong.';
	        	}
	        }
			return ['status' => $status, 'message' => $message];
		}else{
			return "Ayee";
		}
	}
}