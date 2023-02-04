<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Catalog;
use App\Models\CatalogDetail;
use App\Models\Invoice;
use App\Models\InvoiceDetail;
use App\Models\Items;
use App\Models\ItemsDetail;
use App\Models\InvoiceAddons;
use Xendit\Xendit;
use DB;
use QrCode;
use Auth;
use Session;
use getData;

class POSController extends Controller
{
    public function __construct()
	{
	    $this->middleware('auth');
	}
	public function index()
    {
        if(request('test')){
            $xendit_api = DB::table('settings')->pluck('value', 'label')->toArray();
            $apiKey = $xendit_api['xendit_live'];
            dd($this->cekBalance($apiKey));
        }

        // getData::getCatalogSession('advance_payment')
        $data['titlepage']='Point of Sales';
        $data['maintitle']='Point of Sales';
        return view('pages.pos.index',$data);
    }

    public function cekBalance($apiKey)
    {
        $url = 'https://api.xendit.co/balance?account_type='.request('type');
        $headers = [];
        $headers[] = 'Content-Type: application/json';
        $headers[] = 'for-user-id: '.request('id');
  
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl, CURLOPT_USERPWD, $apiKey.":");
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
  
        $result = curl_exec($curl);
        return json_decode($result);
    }

    public function getTable(Request $request)
    {
        $columns = ['items_name','category_name'];
        $keyword = trim($request->input('searchfield'));

        $query = CatalogDetail::select('catalogdetail.*','items.*','category.category_name')
                                ->leftJoin('items','catalogdetail.item','=','items.id')
                                ->leftJoin('category','catalogdetail.category_id','=','category.id')
                                ->where('catalogdetail.catalog_id',getData::getCatalogSession('id'))
                                //->where('ready_stock','Y')
                                ->where(function($result) use ($keyword,$columns){
                                    foreach($columns as $column)
                                    {
                                        if($keyword != ''){
                                            $result->orWhere($column,'LIKE','%'.$keyword.'%');
                                        }
                                    }
                                });
        $data['request'] = $request->all();
        $data['getData'] = $query->paginate(10);
        $data['pagination'] = $data['getData']->appends(['searchfield'=>($request->searchfield == '')?"":$request->searchfield])->links();
        return view('pages.pos.table',$data);
    }
    public function getTablePending(Request $request)
    {
        if(Session::get('catalogsession') == 'All'){
            $catalog = Catalog::orderBy('id','desc')->first();
        }else{
            $catalog = Catalog::where('id',Session::get('catalogsession'))->orderBy('id','desc')->first();
        }
        $query = Invoice::where('catalog_id',$catalog['id'])
                        ->where(function($qry){
                            $qry->where(function($q1){
                                    $q1->where('status','Completed')
                                       ->where('pending','Y');
                                })
                                ->orWhere(function($q2){
                                    $q2->where('status','Checkout')
                                       ->where('via','System')
                                       ->where('pending','N');
                                });
                        })
                        //->where('payment_method','>',0)
                        ->where('via','POS')
                        ->orderBy('id', 'desc');
        if($request->has('searchinvoice')){
            $query->where('invoice_number', 'LIKE', '%'.$request->searchinvoice.'%');
        }
        $data['request'] = $request->all();
        $data['getData'] = $query->paginate(10);
        $data['pagination'] = $data['getData']->appends(['searchinvoice'=>($request->searchinvoice == '')?"":$request->searchinvoice])->links();
        $data['via'] = 'POS';
        return view('pages.pos.tableinvoice',$data);
    }
    public function getTableOnline(Request $request)
    {
        if(Session::get('catalogsession') == 'All'){
            $catalog = Catalog::orderBy('id','desc')->first();
        }else{
            $catalog = Catalog::where('id',Session::get('catalogsession'))->orderBy('id','desc')->first();
        }
        $query = Invoice::where('catalog_id',$catalog['id'])
                        ->where(function($qry){
                            $qry->where(function($q1){
                                    $q1->where('status','Completed')
                                       ->where('pending','Y');
                                })
                                ->orWhere(function($q2){
                                    $q2->where('status','Checkout')
                                       ->where('via','System')
                                       ->where('pending','N');
                                });
                        })
                        //->where('payment_method','>',0)
                        ->where('via','System')
                        ->orderBy('id', 'desc');
        if($request->has('searchOnline')){
            $query->where('invoice_number', 'LIKE', '%'.$request->searchOnline.'%');
        }
        $data['request'] = $request->all();
        $data['getData'] = $query->paginate(10);
        $data['pagination'] = $data['getData']->appends(['searchOnline'=>($request->searchOnline == '')?"":$request->searchOnline])->links();
        $data['via'] = 'System';
        return view('pages.pos.tableinvoice',$data);
    }

    public function getData(Request $request)
    {
        if($request->isMethod('post')){
            if(getData::getCatalogSession('advance_payment') == 'Y'){
                if(Invoice::save_data($request)){
                    $status='success';
                    $message='Your request was successful.';
                }else{
                    $status='error';
                    $message='Oh snap! something went wrong.';
                }
            }else{
                if(Invoice::save_data_last($request)){
                    $status='success';
                    $message='Your request was successful.';
                }else{
                    $status='error';
                    $message='Oh snap! something went wrong.';
                }
            }
            
            $notif=['status'=>$status,'message'=>$message];
            return response()->json($notif);
        }else{
            $data['invoice'] = Invoice::where('invoice_number', Session::get('cartInvoice'))->where('device_session',Session::get('device_session'))->first();
            if(!empty($data['invoice'])){
                $data['item'] = InvoiceDetail::where('invoiceid', $data['invoice']['id'])
                    ->groupBy('category')
                    ->orderBy('id')
                    ->get();
            }else{
                $data['item']=[];
            }

            $data['getProfile'] = Catalog::where('id',Session::get('catalogsession'))->first();
            
            $payer_email = '';
            if($request->init){
                if(Session::has('display_email')){
                    $request->session()->forget('display_email');
                }
            }
            else{
                if(is_null($request->inv_email)){
                    $payer_email = '';
                }
                elseif($request->inv_email){
                    $payer_email = $request->input('inv_email');
                    $data['display_email'] = $payer_email;
                    
                    $validator = Validator::make($request->all(), [
                        'inv_email' => 'email:rfc,dns'
                    ]);
                    
                    if ($validator->fails()) {
                        if(Session::has('display_email') && !empty(Session::get('display_email'))){
                            $payer_email = Session::get('display_email');
                        }
                        else{
                            $payer_email = '';
                        }
                    }
                }
                else{
                    if(Session::has('display_email') && !empty(Session::get('display_email'))){
                        $payer_email = Session::get('display_email');
                    }
                }
            }

            if(!$payer_email){
                $payer_email = $data['getProfile']->email_contact;
            }
            
            $data['display_email'] = $payer_email;
            Session::put('display_email', $data['display_email']);

            return view('pages.pos.transaction_front',$data);

            // if(getData::getCatalogSession('advance_payment') == 'Y'){
            //     return view('pages.pos.transaction_front',$data);
            // }else{
            //     return view('pages.pos.transaction_back',$data);
            // }
        }
    }
    public function getEditData(Request $request,$id)
    {
        if($request->isMethod('post')){
            if(getData::getCatalogSession('advance_payment') == 'Y'){
                if(Invoice::save_data($request)){
                    $status='success';
                    $message='Your request was successful.';
                }else{
                    $status='error';
                    $message='Oh snap! something went wrong.';
                }
            }else{
                if(Invoice::save_data_last($request)){
                    $status='success';
                    $message='Your request was successful.';
                }else{
                    $status='error';
                    $message='Oh snap! something went wrong.';
                }
            }
            
            $notif=['status'=>$status,'message'=>$message];
            return response()->json($notif);
        }else{
            $data['invoice'] = Invoice::where('id', $id)->first();
            if(!empty($data['invoice'])){
                $data['item'] = InvoiceDetail::where('invoiceid', $data['invoice']['id'])
                                                ->groupBy('category')
                                                ->orderBy('id')
                                                ->get();
            }else{
                $data['item']=[];
            }
            return view('pages.pos.transaction_edit',$data);
        }
    }
    public function updateData(Request $request)
    {
        if(InvoiceDetail::update_data($request)){
            $status='success';
            $message='Your request was successful.';
        }else{
            $status='error';
            $message='Oh snap! something went wrong.';
        }
        $notif=['status'=>$status,'message'=>$message];
        return response()->json($notif);
    }
    public function updateCartBackData(Request $request)
    {
        if(InvoiceDetail::update_data_back_payment($request)){
            $status='success';
            $message='Your request was successful.';
        }else{
            $status='error';
            $message='Oh snap! something went wrong.';
        }
        $notif=['status'=>$status,'message'=>$message];
        return response()->json($notif);
    }
    public function checkoutData(Request $request)
    {
        if($request->input('pendingstatus')=='N'){
            $this->validate($request, [
                'amount' => 'required|numeric',
                //'position' => 'required',
            ]);
        }
        
        if($request->online){

        }
        else{
            if($request->input('payment_method')==2){
                $this->validate($request, [
                    'imagefile' => 'required|max:1000|mimes:jpeg,jpg,png'
                ]);
            }
        }

        $invoice_url='';
        if($invoice = Invoice::checkout_data($request)){
            if($request->input('payment_method') == 2){
                $data['getProfile'] = Catalog::where('id',Session::get('catalogsession'))->first();
                $createInvoice = '';

                if($data['getProfile']->xendit_user_id){
                    if($data['getProfile']->online_type){
                        $grand = $invoice->amount;
                        $payer_email = $invoice->email ? $invoice->email : $data['getProfile']->email_contact;
            
                        if($invoice->invoice_number){
                            $xendit_api = DB::table('settings')->pluck('value', 'label')->toArray();
                            Xendit::setApiKey($xendit_api[$data['getProfile']->online_type]);
            
                            $rule_fee_id = '';
                            if(!isset($xendit_api['rule_fee']) || !$xendit_api['rule_fee']){
                                $rule_fee_req = $this->createRuleFee($xendit_api[$data['getProfile']->online_type]);
                                $rule_fee_id = $rule_fee_req->id;
                                DB::table('settings')->updateOrCreate(
                                    [
                                        'label' => 'rule_fee',
                                    ],
                                    [
                                        'value' => $rule_fee_id
                                    ]
                                );
                            }
                            else{
                                $rule_fee_id = $xendit_api['rule_fee'];
                            }

                            // xednit production
                            // Xendit::setApiKey('xnd_production_gn2uYWGrfuO1cT15KAbgAfFX60czGKGVo1mRFGtnl8h6FlwMEkh6Bkdfakva8');
                
                            // xendit sandbox
                            // Xendit::setApiKey('xnd_development_HsHyrDu9CIRaCoUA1Zqcab3LKo77ixkJcbNEfFXNJxxfS7xyB3M99VxD5xk3xgKv');

                            $params = [
                                'for-user-id' => $data['getProfile']->xendit_user_id,
                                'external_id' => $invoice->invoice_number,
                                'payer_email' => $payer_email,
                                'description' => $data['getProfile']->catalog_title . ($data['getProfile']->position ? ' - ' . $data['getProfile']->position : ''),
                                'amount' => $grand ? $grand : 1,
                                'should_send_email' => true,
                                'invoice_duration' => 86400, // 24 hours in seconds
                                'success_redirect_url' => url('pos'),
                                //'failure_redirect_url' => '' // URL that end user will be redirected to upon expiration of this invoice.
                            ];

                            if($rule_fee_id){
                                $params['with-fee-rule'] = $rule_fee_id;
                            }
                            
                            try {
                                DB::transaction(function () use (&$params, &$createInvoice){
                                    $createInvoice = \Xendit\Invoice::create($params);
                                });
                            }
                            catch(\Exception $e) {
                                // xendit sandbox
                                Xendit::setApiKey('xnd_development_HsHyrDu9CIRaCoUA1Zqcab3LKo77ixkJcbNEfFXNJxxfS7xyB3M99VxD5xk3xgKv');
                                $createInvoice = \Xendit\Invoice::create($params);
                            }
                        }
                    }
                }
                
                $invoice_url=isset($createInvoice['invoice_url']) ? $createInvoice['invoice_url'] : '';
            }

            Session::forget('cartInvoice');
            $status='success';
            $message='Your request was successful.';
        }else{
            $status='error';
            $message='Oh snap! something went wrong.';
        }
        $notif=['status'=>$status,'message'=>$message, 'invoice_url'=>$invoice_url];
        return response()->json($notif);
    }
    public function createRuleFee($apiKey)
    {
        $url = 'https://api.xendit.co/fee_rules';
        $headers = [];
        $headers[] = 'Content-Type: application/json';
        $data = [
            'name' => 'standard_platform_fee',
            'description' => 'fee_for_all_transactions_accepted_on_behalf_of_vendors',
            'routes' => [
                [
                    'unit' => 'percent',
                    'amount' => 1,
                    'currency' => 'IDR'
                ]
            ]
        ];
  
        $curl = curl_init();
  
        $payload = json_encode($data);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl, CURLOPT_USERPWD, $apiKey.":");
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $payload);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
  
        $result = curl_exec($curl);
        return json_decode($result);
    }
    public function completePending(Request $request)
    {
        $this->validate($request, [
            'amount' => 'required|numeric',
        ]);

        if($request->online){

        }
        else{
            if($request->input('payment_method')==2){
                $this->validate($request, [
                    'imagefile' => 'required|max:1000|mimes:jpeg,jpg,png'
                ]);
            }
        }
        
        if(Invoice::complete_pending($request)){
            Session::forget('cloneOrder');
            $status='success';
            $message='Your request was successful.';
        }else{
            $status='error';
            $message='Oh snap! something went wrong.';
        }
        $notif=['status'=>$status,'message'=>$message];
        return response()->json($notif);
    }
    public function cancelData()
    {
        if(Invoice::delete_data()){
            Session::forget('cartInvoice');
            $status='success';
            $message='Your request was successful.';
        }else{
            $status='error';
            $message='Oh snap! something went wrong.';
        }
        $notif=['status'=>$status,'message'=>$message];
        return response()->json($notif);
    }
    public function deleteData($id=null)
    {
        if(InvoiceDetail::delete_data($id)){
            $status='success';
            $message='Your request was successful.';
        }else{
            $status='error';
            $message='Oh snap! something went wrong.';
        }
        $notif=['status'=>$status,'message'=>$message];
        return response()->json($notif);
    }
    public function deleteaddon($detail = null,$group = null)
    {
        if (InvoiceAddons::delete_addon($detail,$group)) {
            $status = 'success';
            $message = 'Your request was successful.';
        } else {
            $status = 'error';
            $message = 'Oh snap! something went wrong.';
        }
        return ['status' => $status, 'message' => $message];
    }
    public function getCloneData(){
        $data['invoice'] = Invoice::where('invoice_number', Session::get('cloneOrder'))->first();
        if(empty($data['invoice'])){
            return "";
        }
        $data['item'] = InvoiceDetail::where('invoiceid', $data['invoice']['id'])
                        ->groupBy('category')
                        ->orderBy('id')
                        ->get();
        if($data['item']->count() > 0){
            return view('pages.pos.transaction_split',$data);
        }else{
            return "";
        }
    }
    public function clonedata($item = null,$detailinvoice = null)
    {
        if(!Session::has('cloneOrder')){
            Session::put('cloneOrder',date('YmdHis'));
        }
        $invoice = Session::get('cloneOrder');
        if (InvoiceDetail::clone_data($item,$detailinvoice,$invoice)) {
            $status = 'success';
            $message = 'Your request was successful.';
        } else {
            $status = 'error';
            $message = 'Oh snap! something went wrong.';
        }
        return ['status' => $status, 'message' => $message];
    }
    public function showInvoice($id)
    {
        $data = Invoice::find($id);
        return $data;
    }
    public function edit($id)
    {
        $data['invoice'] = $this->showInvoice($id);
        $data['titlepage']='Order Number '.$data['invoice']['invoice_number'];
        $data['maintitle']='Order Number '.$data['invoice']['invoice_number'];
        return view('pages.pos.edit',$data);
    }
    public function showDetail($id)
    {
        $data = InvoiceDetail::find($id);
        return $data;
    }
    public function getDataPending($id)
    {
        $data['invoice'] = Invoice::where('id', $id)->first();

        if(!empty($data['invoice'])){
            $data['item'] = InvoiceDetail::where('invoiceid', $data['invoice']['id'])
                ->groupBy('category')
                ->orderBy('id')
                ->get();
        }else{
            $data['item']=[];
        }

        $data['getProfile'] = Catalog::where('id', $data['invoice']['catalog_id'])->first();
        $createInvoice = '';
        $invoice_url='';

        if($data['getProfile']->xendit_user_id){
            if($data['getProfile']->online_type){
                $grand = 0;
                $itemgroup= [];

                foreach($data['item'] as $item){
                    $total = 0;
                    $totaladdons = 0;

                    foreach(getData::getItemCart($data['invoice']['invoice_number'],$item['category']) as $listitem){
                        $price = ($listitem['price']-$listitem['discount']) * $listitem['qty'];
                        $total = $total + $price;
                        $itemgroup[]= $listitem['item'].' x '.$listitem['qty'];

                        if(getData::getInvoiceAddons($listitem['id'])->count() > 0){
                            foreach(getData::getInvoiceAddons($listitem['id']) as $addondata){
                                $priceaddons = $addondata['addon_qty']*getData::addonsPrice($addondata['single_addon'],$addondata['multiple_addon']);
                                $totaladdons = $totaladdons+$priceaddons;
                            }
                        }
                    }
    
                    $grand = $grand +  $total + $totaladdons;
                }
    
                $gettax = ($grand*getData::getCatalogSession('tax')) /100;
                $grand = $grand + $gettax;
    
                $xendit_api = DB::table('settings')->pluck('value', 'label')->toArray();
                Xendit::setApiKey($xendit_api[$data['getProfile']->online_type]);

                $rule_fee_id = '';
                if(!isset($xendit_api['rule_fee']) || !$xendit_api['rule_fee']){
                    $rule_fee_req = $this->createRuleFee($xendit_api[$data['getProfile']->online_type]);
                    $rule_fee_id = $rule_fee_req->id;
                    DB::table('settings')->updateOrCreate(
                        [
                            'label' => 'rule_fee',
                        ],
                        [
                            'value' => $rule_fee_id
                        ]
                    );
                }
                else{
                    $rule_fee_id = $xendit_api['rule_fee'];
                }

                // xednit production
                // Xendit::setApiKey('xnd_production_gn2uYWGrfuO1cT15KAbgAfFX60czGKGVo1mRFGtnl8h6FlwMEkh6Bkdfakva8');
    
                // xendit sandbox
                // Xendit::setApiKey('xnd_development_HsHyrDu9CIRaCoUA1Zqcab3LKo77ixkJcbNEfFXNJxxfS7xyB3M99VxD5xk3xgKv');
    
                $params = [
                    'for-user-id' => $data['getProfile']->xendit_user_id,
                    'external_id' => $data['invoice']['invoice_number'],
                    'payer_email' => $data['getProfile']->email_contact,
                    'description' => $data['getProfile']->catalog_title . ($data['getProfile']->position ? ' Meja ' . $data['getProfile']->position : ''),
                    'amount' => $grand ? $grand : 1500,
                    'should_send_email' => true,
                    'invoice_duration' => 86400, // 24 hours in seconds
                    'success_redirect_url' => url('pos'),
                    //'failure_redirect_url' => '' // URL that end user will be redirected to upon expiration of this invoice.
                ];

                if($rule_fee_id){
                    $params['with-fee-rule'] = $rule_fee_id;
                }
                
                try {
                    DB::transaction(function () use (&$params, &$createInvoice){
                        $createInvoice = \Xendit\Invoice::create($params);
                    });
                }
                catch(\Exception $e) {
                    // // xendit sandbox
                    // Xendit::setApiKey('xnd_development_HsHyrDu9CIRaCoUA1Zqcab3LKo77ixkJcbNEfFXNJxxfS7xyB3M99VxD5xk3xgKv');
                    // $createInvoice = \Xendit\Invoice::create($params);
                }

                // QRIS payment
                $invoice = $data['invoice'];
                // $grand = $invoice->amount ? $invoice->amount : 1500;
                $payer_email = $invoice->email ? $invoice->email : $data['getProfile']->email_contact;

                if($invoice->invoice_number){
                    if(strlen($invoice->qr_string) > 3){
                        if(\Request::getHttpHost()=='localhost'){
                            $invoice_url='<img src="data:image/png;base64, '. base64_encode(QrCode::format('png')->size(350)->generate('00020101021226660014ID.LINKAJA.WWW011893600911002100622202152007271100622270303UME51450015ID.OR.GPNQR.WWW02150000000000000000303UME520454995802ID5906Yoscan6015Jakarta Selatan61051243062380115IjgNMhECLNVh5NO0715IjgNMhECLNVh5NO530336054061250006304B0EF')).'">';
                        }else{
                            $qr_string = $invoice->qr_string;
                            $invoice_url='<img src="data:image/png;base64, '. base64_encode(QrCode::format('png')->size(350)->generate($qr_string)).'">';
                        }
                    }
                    else{
                        // $xendit_api = DB::table('settings')->pluck('value', 'label')->toArray();

                        // if($data['getProfile']->user_id == 1){
                        //     $apiKey = $xendit_api['xendit_test'];
                        // }
                        // else{
                            $apiKey = $xendit_api[$data['getProfile']->online_type];
                        // }

                        $permitted_chars = '0123456789abcdefghijklmnopqrstuvwxyz';
                        $external_id = $invoice->invoice_number.'_'.substr(str_shuffle($permitted_chars), 0, 10);
        
                        $ch = curl_init();
                        curl_setopt($ch, CURLOPT_URL, 'https://api.xendit.co/qr_codes');
                        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                        curl_setopt($ch, CURLOPT_POST, 1);

                        if($rule_fee_id){
                            curl_setopt($ch, CURLOPT_POSTFIELDS, "external_id=".$external_id."&currency=IDR&type=DYNAMIC&callback_url=https://yoscan.id/api/xendit/qris_handling&amount=".$grand."&for-user-id=".$data['getProfile']->xendit_user_id."&with-fee-rule=".$rule_fee_id);
                        }
                        else{
                            curl_setopt($ch, CURLOPT_POSTFIELDS, "external_id=".$external_id."&currency=IDR&type=DYNAMIC&callback_url=https://yoscan.id/api/xendit/qris_handling&amount=".$grand."&for-user-id=".$data['getProfile']->xendit_user_id);
                        }
                        curl_setopt($ch, CURLOPT_USERPWD, $apiKey.":");
                        
                        $headers = array();
                        $headers[] = 'Content-Type: application/x-www-form-urlencoded';
                        $headers[] = 'for-user-id: '.$data['getProfile']->xendit_user_id;
                        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
                        
                        $result = curl_exec($ch);
                        if (curl_errno($ch)) {
                            // echo 'Error:' . curl_error($ch);
                            curl_close($ch);
                        }
                        else{
                            curl_close($ch);

                            if(\Request::getHttpHost()=='localhost'){
                                $invoice_url='<img src="data:image/png;base64, '. base64_encode(QrCode::format('png')->size(350)->generate('00020101021226660014ID.LINKAJA.WWW011893600911002100622202152007271100622270303UME51450015ID.OR.GPNQR.WWW02150000000000000000303UME520454995802ID5906Yoscan6015Jakarta Selatan61051243062380115IjgNMhECLNVh5NO0715IjgNMhECLNVh5NO530336054061250006304B0EF')).'">';
                            }else{
                                // dd($result);
                                $result = json_decode($result);
                                $qr_string = $result->qr_string;
                                $invoice_url='<img src="data:image/png;base64, '. base64_encode(QrCode::format('png')->size(350)->generate($qr_string)).'">';
                            }

                            Invoice::where('id', $invoice->id)->update([
                                'qr_string'=>$qr_string,
                            ]);
                        }
                    }
                }

            }
        }

        $data['qris_image'] = $invoice_url;
        $data['xendit'] = $createInvoice;
        return view('pages.pos.transaction_pending',$data);
    }
    public function showItem($id)
    {
        $data['getProfile'] = Catalog::where('id',Session::get('catalogsession'))->first();
        $data['invoice'] = Invoice::where('id', $id)->first();
        if(!empty($data['invoice'])){
            $data['item'] = InvoiceDetail::where('invoiceid', $data['invoice']['id'])
                // ->wherenotIn('item_status',['Order','Checkout'])
                ->groupBy('category')
                ->orderBy('id')
                ->get();
        }else{
            $data['item']=[];
        }

        if(request()->edit_status){
            return view('pages.pos.edit_status',$data);
        }
        else{
            return view('pages.pos.select_item',$data);
        }
    }
    public function addons($item,$group)
    {
        $data['addons'] = ItemsDetail::select('items_detail.*',
                            'category.category_name',
                            'items.items_name',
                            'items.items_price')
                            ->leftJoin('items','items_detail.addon','=','items.id')
                            ->leftJoin('category','items_detail.category_id','=','category.id')
                            ->where('items.item_type','Add')
                            ->where('items.ready_stock','Y')
                            ->where('item_id',$item)
                            ->groupBy('category_id')
                            ->get();
        $data['item']=$item;
        $data['group']=$group;
        return view('pages.pos.addons', $data);
    }
    public function updatecartaddons(Request $request)
    {
        if (InvoiceAddons::update_data($request)) {
            $status = 'success';
            $message = 'Your request was successful.';
        } else {
            $status = 'error';
            $message = 'Oh snap! something went wrong.';
        }
        return ['status' => $status, 'message' => $message];
    }
}
