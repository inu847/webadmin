<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Catalog;
use App\Models\Invoice;
use App\Models\InvoiceDetail;
use App\Models\InvoiceAddons;
use App\Models\Items;
use App\Models\ItemsViews;
use App\Models\Pengeluaran;
use App\Models\PengeluaranDetail;

use Auth;
use Session;
use DB;
use getData;

class TransactionController extends Controller
{
    public function __construct()
	{
	    $this->middleware('auth');
	}
	public function index(Request $request,$getstatus)
    {
    	if($getstatus == 'checkout'){
    		$status = 'Checkout';
    		$icon = 'pe-7s-next-2';
    	}
    	elseif($getstatus == 'approve'){
    		$status = 'Approve';
    		$icon = 'lnr-select';
    	}
    	elseif($getstatus == 'process'){
    		$status = 'Process';
    		$icon = 'lnr-hourglass';
    	}
    	elseif($getstatus == 'delivered'){
    		$status = 'Delivered';
    		$icon = 'lnr-location';
    	}
    	elseif($getstatus == 'completed'){
    		$status = 'Completed';
    		$icon = 'lnr-checkmark-circle';
    	}
    	elseif($getstatus == 'cancel'){
    		$status = 'Cancel';
    		$icon = 'lnr-cross-circle';
    	}
        if(Session::get('catalogsession') == 'All'){
            $catalog = Catalog::where('user_id',Auth::user()->id)->orderBy('id','desc')->first();
        }else{
            $catalog = Catalog::where('id',Session::get('catalogsession'))->orderBy('id','desc')->first();
        }

        // if($catalog['advance_payment'] == "Y"){
        //     $query = Invoice::where('catalog_id',$catalog['id'])
        //                     ->where('status',$status)
        //                     ->where(function($result) use ($status){
        //                         if($status == 'Completed'){
        //                             $result->where('pending','N');
        //                         }
        //                         elseif($status != 'Checkout'){
        //                             $result->where('lunas',1);
        //                         }
        //                     })
        //                     ->orderBy('id');
        // }
        // else{
            $query = Invoice::where('catalog_id',$catalog['id'])
                            ->where('status',$status)
                            ->where(function($result) use ($status){
                                // if($status == 'Completed'){
                                //     $result->where('pending','N');
                                // }
                            })
                            //->where('invoice_type','Permanent')
                            ->orderBy('id');
        // }

        if($request->has('searchfield')){
            $query->where('invoice_number', 'LIKE', '%'.$request->searchfield.'%');
        }
        
        $data['request'] = $request->all();
        $data['getData'] = $query->paginate(10);
        $data['pagination'] = $data['getData']->appends(['searchfield'=>($request->searchfield == '')?"":$request->searchfield])->links();
        $data['titlepage']='Order List '.$status;
        $data['maintitle']='Order List '.$status;
        $data['status']=$getstatus;
        $data['icon']=$icon;
        $data['invoice_type']=DB::table('invoice_types')->pluck('title', 'id');
        return view('pages.transaction.order',$data);
    }
    public function detail(Request $request,$invoice)
    {
    	$data['invoice']=Invoice::where('invoice_number',$invoice)->first();
    	$data['item'] =  InvoiceDetail::where('invoiceid',$data['invoice']['id'])->groupBy('category')->orderBy('id')->get();
    	$data['titlepage']='Detail order '.$invoice;
        $data['maintitle']='Detail order '.$invoice;
        $data['invoice_type']=DB::table('invoice_types')->pluck('title', 'id');
        $data['catalog'] = Catalog::where('id', $data['invoice']['catalog_id'])->orderBy('id','desc')->first();

        // dd($data['catalog']['advance_payment']);

        $btn_bayar = false;
        // prepaid
        // if($data['catalog']['advance_payment'] == "Y"){
            if($data['invoice']['status'] == 'Checkout' && $data['invoice']['lunas'] != 1){
                // not lunas
                $btn_bayar = true;
            }
        // }

        // postpaid
        if($data['catalog']['advance_payment'] != "Y"){
            if($data['invoice']['lunas'] != 1){
                // not lunas
                $btn_bayar = true;
            }
        }

        $data['btn_bayar'] = $btn_bayar;
    	return view('pages.transaction.detail',$data);
    }
    public function detailpopup($invoice)
    {
        $data['invoice']=Invoice::where('invoice_number',$invoice)->first();
        $data['item'] =  InvoiceDetail::where('invoiceid',$data['invoice']['id'])->where('clone_data','N')->groupBy('category')->orderBy('id')->get();
        return view('pages.transaction.detailpopup',$data);
    }
    public function deleteitem($id=null)
    {
    	if(InvoiceDetail::delete_data($id)){
    	    $status='success';
    	    $message='Your request was successful.';
    	}else{
    	    $status='error';
    	    $message='Oh snap! something went wrong.';
    	}
    	return ['status'=>$status,'message'=>$message];
    }
    public function cancelorder(Request $request)
    {
    	if(Invoice::cancel_order($request)){
    	    $status='success';
    	    $message='Your request was successful.';
    	}else{
    	    $status='error';
    	    $message='Oh snap! something went wrong.';
    	}
    	return ['status'=>$status,'message'=>$message];
    }
    public function updatestatus($invoice=null,$status=null,$lunas=null)
    {
        if(Invoice::update_status($invoice,$status,$lunas)){
            $ch = curl_init(); 
            curl_setopt($ch, CURLOPT_URL, \URL::to('/notif/'.$invoice.'/'.$status));
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
            $output = curl_exec($ch); 
            curl_close($ch);

            $status='success';
            $message='Your request was successful.';
        }else{
            $status='error';
            $message='Oh snap! something went wrong.';
        }
        return ['status'=>$status,'message'=>$message];
    }
    public function lunas($invoice)
    {
        if(Invoice::where('invoice_number', $invoice)->update(['lunas' => 1])){
            $status='success';
            $message='Your request was successful.';
        }else{
            $status='error';
            $message='Oh snap! something went wrong.';
        }
        return ['status'=>$status,'message'=>$message];
    }
    public function report(Request $request,$status=null,$start=null,$end=null)
    {
        // if(Session::get('catalogsession') == 'All'){
        //     $catalog = Catalog::where('user_id', Auth::user()->id)->orderBy('id','desc')->get();
        // }else{
        //     $catalog = Catalog::where('id',Session::get('catalogsession'))->orderBy('id','desc')->get();
        // }
        // if($request->has('status')){
        //     $status = $request->status;
        // }else{
        //     $status = $status;
        // }

        $user_id = Auth::user()->id;
        if(Auth::user()->parent_id){
            $user_id = Auth::user()->parent_id;
        }

        if(Session::get('catalogsession') == 'All'){
            $catalog = Catalog::where('user_id', $user_id)->pluck('id');
        }else{
            $catalog = Catalog::where('id', Session::get('catalogsession'))->orderBy('id','desc')->pluck('id');
        }

        if($request->has('start') && $request->start){
            $start = $request->start;
        }else{
            $start = $start ? $start : date('Y-m-01');
        }
        if($request->has('end') && $request->end){
            $end = $request->end;
        }else{
            $end = $end ? $end : date('Y-m-d');
        }
        
        // if(!$start){
        //     $start = date('Y-m-01');
        // }
        // if(!$end){
        //     $end = date('Y-m-d');
        // }

        $status = 'Completed';

        // if($status == 'All'){
        //     $query = Invoice::where('catalog_id',$catalog['id'])->where('created_at','>=',$start.' 00:00:00')->where('created_at','<=',$end.' 23:59:59')->where('invoice_type','Permanent')->where('status','<>','Order')->orderBy('invoice_number');
        //     if(Session::get('catalogsession') == 'All'){
        //         $periodday = Invoice::select('invoice.*','catalog.catalog_title','catalog.catalog_logo','catalog.catalog_username','catalog.domain')
        //                             ->leftJoin('catalog','invoice.catalog_id','=','catalog.id')
        //                             ->where('invoice_type','Permanent')
        //                             ->where('status','<>','Order')
        //                             ->where('catalog.user_id',Auth::user()->id)
        //                             ->where('invoice.created_at','>=',$start.' 00:00:00')->where('invoice.created_at','<=',$end.' 23:59:59')
        //                             ->groupBy(DB::raw('Date(invoice.created_at)'))
        //                             ->orderBy('invoice_number');
        //         $periodmonth = Invoice::select('invoice.*','catalog.catalog_title','catalog.catalog_logo','catalog.catalog_username','catalog.domain')
        //                             ->leftJoin('catalog','invoice.catalog_id','=','catalog.id')
        //                             ->where('invoice_type','Permanent')
        //                             ->where('status','<>','Order')
        //                             ->where('catalog.user_id',Auth::user()->id)
        //                             ->where('invoice.created_at','>=',$start.' 00:00:00')->where('invoice.created_at','<=',$end.' 23:59:59')
        //                             ->groupBy(DB::raw('Month(invoice.created_at)'))
        //                             ->orderBy('invoice_number');
        //     }else{
        //         $periodday = Invoice::where('catalog_id',$catalog['id'])
        //                                 ->where('invoice_type','Permanent')
        //                                 ->where('status','<>','Order')
        //                                 ->where('created_at','>=',$start.' 00:00:00')
        //                                 ->where('created_at','<=',$end.' 23:59:59')
        //                                 ->groupBy(DB::raw('Date(created_at)'))
        //                                 ->orderBy('invoice_number');
        //         $periodmonth = Invoice::where('catalog_id',$catalog['id'])
        //                                 ->where('invoice_type','Permanent')
        //                                 ->where('status','<>','Order')
        //                                 ->where('created_at','>=',$start.' 00:00:00')
        //                                 ->where('created_at','<=',$end.' 23:59:59')
        //                                 ->groupBy(DB::raw('Month(created_at)'))
        //                                 ->orderBy('invoice_number');
        //     }
            
        //     $itemDay = InvoiceDetail::select('invoicedetail.*','invoice.invoice_number')
        //                         ->leftJoin('invoice','invoicedetail.invoiceid','invoice.id')
        //                         ->where('invoice.invoice_type','Permanent')
        //                         ->where('invoice.catalog_id',$catalog['id'])
        //                         ->where('invoice.created_at','>=',$start.' 00:00:00')
        //                         ->where('invoice.created_at','<=',$end.' 23:59:59')
        //                         ->groupBy(DB::raw('Date(invoicedetail.created_at)'),'item_id')
        //                         ->orderBy('invoicedetail.id');
        //     $itemMonth = InvoiceDetail::select('invoicedetail.*','invoice.invoice_number')
        //                         ->leftJoin('invoice','invoicedetail.invoiceid','invoice.id')
        //                         ->where('invoice.invoice_type','Permanent')
        //                         ->where('invoice.catalog_id',$catalog['id'])
        //                         ->where('invoice.created_at','>=',$start.' 00:00:00')
        //                         ->where('invoice.created_at','<=',$end.' 23:59:59')
        //                         ->groupBy(DB::raw('Month(invoicedetail.created_at)'),'item_id')
        //                         ->orderBy('invoicedetail.id');
        // }else{
            $main_query = Invoice::leftJoin('invoice_types','invoice.invoice_type_id','=','invoice_types.id')
                ->select('invoice.*', 'invoice_types.title')
                ->whereIn('catalog_id', $catalog)
                ->where('invoice.status',$status)
                ->where('invoice.invoice_type','Permanent')
                ->where('invoice.created_at','>=',$start.' 00:00:00')
                ->where('invoice.created_at','<=',$end.' 23:59:59')
                ->get();
                
            $query = $main_query->countBy('title')->toArray();
            
            if(isset($query[""])){
                $query['Dine In'] = isset($query['Dine In']) ? $query['Dine In'] + $query[""] : $query[""];
                unset($query[""]);
            }

            $pie['data'] = implode(',', $query);
            $colorArray = ['#f56954', '#00a65a', '#f39c12', '#00c0ef', '#3c8dbc', '#d2d6de'];
            $pie['color'] = implode(',', array_slice($colorArray,0,(count($query))));
            $pie['label'] = implode(',', array_keys($query));

            arsort($query);

            // get item stats
            $inv_details = InvoiceDetail::join('invoice','invoicedetail.invoiceid','invoice.id')
                ->join('items','invoicedetail.item_id','items.id')
                ->whereIn('invoice.catalog_id', $catalog)
                ->where('invoice.status',$status)
                ->where('invoice.invoice_type','Permanent')
                ->where('invoice.created_at','>=',$start.' 00:00:00')
                ->where('invoice.created_at','<=',$end.' 23:59:59')
                ->get();

            $item_views = ItemsViews::select('*')
                ->whereIn('catalog_id', $catalog)
                ->where('created_at','>=',$start.' 00:00:00')
                ->where('created_at','<=',$end.' 23:59:59')
                ->get();

                // ->groupBy('item_id')
                // ->get('total', 'item_id')
                // ->toArray();

                // grouping by item_id for all catalogs
                // grouping by item_id for each catalogs

                // dd($item_views);
                
            $item_views = $item_views->groupBy('item_id');

            $items = [];
            $viewed = [];
            $item_name = [];
            if(count($inv_details)){
                foreach ($inv_details as $key => $value) {
                    // sold status
                    if($value->item_status == 'Completed'){
                        $items[$value->item_id][] = $value->qty;
                    }
                    
                    // viewed status
                    // $viewed[$value->item_id][] = $value->viewed;
                    $viewed[$value->item_id] = isset($item_views[$value->item_id]) ? $item_views[$value->item_id] : 0;

                    // item name
                    $item_name[$value->item_id] = $value->items_name;
                }
            }

            // get item ids
            $array_keys = array_keys($viewed);
            
            $new_sold = [];
            $new_viewed = [];

            // if($request->test){
            //     // $viewed[608] = 0;
            //     // dd($viewed[608]->count());
            //     // dd(is_numeric($viewed[608]));
            //     // dd(count($viewed[608]));
            // }

            if(count($array_keys)){
                foreach ($array_keys as $key => $value) {
                    $new_sold[$value] = [
                        'items_name' => $item_name[$value],
                        'sell' => isset($items[$value]) ? array_sum($items[$value]) : 0
                    ];

                    $new_viewed[$value] = [
                        'items_name' => $item_name[$value],
                        'viewed' => (isset($viewed[$value]) && is_numeric($viewed[$value])) ? $viewed[$value] : count($viewed[$value])
                        // 'viewed' => isset($viewed[$value]) ? $viewed[$value] : 0
                    ];
                }
            }

            $collection = collect($new_sold);
            $sold = $collection->where('sell', '>', 0)->sortByDesc('sell')->take(10)->toArray();
            $collection = collect($new_viewed);
            $viewed = $collection->where('viewed', '>', 0)->sortByDesc('viewed')->take(10)->toArray();

            $sorted_sold = [];
            if(count($sold)){
                foreach ($sold as $key => $value) {
                    $sorted_sold[$value['items_name']] = $value['sell'];
                }
            }

            $sorted_viewed = [];
            if(count($viewed)){
                foreach ($viewed as $key => $value) {
                    $sorted_viewed[$value['items_name']] = $value['viewed'];
                }
            }

            $sold['data'] = implode(',', $sorted_sold);
            $sold['label'] = implode(',', array_keys($sorted_sold));

            $viewed['data'] = implode(',', $sorted_viewed);
            $viewed['label'] = implode(',', array_keys($sorted_viewed));

            $periodday = Invoice::whereIn('catalog_id', $catalog)
                                    ->where('invoice.invoice_type','Permanent')
                                    ->where('status',$status)
                                    ->where('created_at','>=',$start.' 00:00:00')
                                    ->where('created_at','<=',$end.' 23:59:59')
                                    ->groupBy(DB::raw('Date(created_at)'))
                                    ->orderBy('invoice_number');
            $periodmonth = Invoice::whereIn('catalog_id', $catalog)
                                    ->where('invoice.invoice_type','Permanent')
                                    ->where('status',$status)
                                    ->where('created_at','>=',$start.' 00:00:00')
                                    ->where('created_at','<=',$end.' 23:59:59')
                                    ->groupBy(DB::raw('Month(created_at)'))
                                    ->orderBy('invoice_number');
            $itemDay = InvoiceDetail::select('invoicedetail.*','invoice.invoice_number','items.items_name')
                                ->leftJoin('invoice','invoicedetail.invoiceid','invoice.id')
                                ->join('items','invoicedetail.item_id','items.id')
                                ->where('invoice.invoice_type','Permanent')
                                // ->where('invoice.catalog_id',$catalog['id'])
                                ->whereIn('invoice.catalog_id', $catalog)
                                ->where('invoicedetail.item_status',$status)
                                ->where('invoicedetail.created_at','>=',$start.' 00:00:00')
                                ->where('invoicedetail.created_at','<=',$end.' 23:59:59')
                                ->groupBy(DB::raw('Date(invoicedetail.created_at)'),'item_id')
                                ->orderBy('invoicedetail.id');
            $itemMonth = InvoiceDetail::select('invoicedetail.*','invoice.invoice_number','items.items_name')
                                ->leftJoin('invoice','invoicedetail.invoiceid','invoice.id')
                                ->join('items','invoicedetail.item_id','items.id')
                                ->where('invoice.invoice_type','Permanent')
                                // ->where('invoice.catalog_id',$catalog['id'])
                                ->whereIn('invoice.catalog_id', $catalog)
                                ->where('invoicedetail.item_status',$status)
                                ->where('invoicedetail.created_at','>=',$start.' 00:00:00')
                                ->where('invoicedetail.created_at','<=',$end.' 23:59:59')
                                ->groupBy(DB::raw('Month(invoicedetail.created_at)'),'item_id')
                                ->orderBy('invoicedetail.id');
        // }
        // if($request->has('searchfield')){
        //     $query->where('invoice_number', 'LIKE', '%'.$request->searchfield.'%');
        //     $periodday->where('invoice_number', 'LIKE', '%'.$request->searchfield.'%');
        //     $periodmonth->where('invoice_number', 'LIKE', '%'.$request->searchfield.'%');
        // }
        $data['request'] = $request->all();
        // $data['getData'] = $query->paginate(10);
        $data['getDay'] = $periodday->get();
        $data['getMonth'] = $periodmonth->get();

        // regenerate summary all catalogs
        $getMonth = $data['getMonth'];
        $summary = [];
        if($getMonth){
            foreach ($getMonth as $key => $value) {
                $date = $value->created_at;
                $month = explode("-",$date)[1];
                $year = explode("-",$date)[0];

                $summary[$value->catalog_id]['total'][] = getData::getCountTransactionMonth($month,$year);
                $summary[$value->catalog_id]['payment'][] = getData::getTotalTransactionMonth($month,$year);
            }
        }

        $data['summary'] = $summary;
        $data['catalogs'] = Catalog::where('user_id', $user_id)->orderBy('catalog_title')->pluck('catalog_title', 'id');

        $data['itemDay'] = $itemDay->get();
        $data['itemMonth'] = $itemMonth->get();
        // $data['pagination'] = $data['getData']->appends(['searchfield'=>($request->searchfield == '')?"":$request->searchfield])->links();
        $data['titlepage']='Report Transaction '.$status;
        $data['maintitle']='Report Transaction '.$status;
        $data['status']=$status;
        $data['start']=$start;
        $data['end']=$end;
        $data['pie']=$pie;
        $data['queries']=$query;
        $data['sold']=$sold;
        $data['viewed']=$viewed;
        $data['sorted_sold']=$sorted_sold;
        $data['sorted_viewed']=$sorted_viewed;
        return view('pages.transaction.report',$data);
    }
    public function descendingOrder($a, $b) {   
        if ($a == $b) {        
            return 0;
        }   
        return ($a > $b) ? -1 : 1; 
    }  
    public function income(Request $request,$status=null,$start=null,$end=null)
    {
        // user check
        $user_id = Auth::user()->id;
        if(Auth::user()->parent_id){
            $user_id = Auth::user()->parent_id;
        }

        // catalog active session
        if(Session::get('catalogsession') == 'All'){
            $catalog = Catalog::where('user_id', $user_id)->pluck('id');
        }else{
            $catalog = Catalog::where('id', Session::get('catalogsession'))->orderBy('id','desc')->pluck('id');
        }

        // filter date start and end
        if($request->has('start') && $request->start){
            $start = $request->start;
        }else{
            $start = $start ? $start : date('Y-m-01');
        }
        if($request->has('end') && $request->end){
            $end = $request->end;
        }else{
            $end = $end ? $end : date('Y-m-d');
        }

        // invoice status
        $status = 'Completed';

        // get invoices
        $invoices = Invoice::whereIn('catalog_id', $catalog)
            ->where('invoice.status',$status)
            ->where('invoice.invoice_type','Permanent')
            ->where('invoice.created_at','>=',$start.' 00:00:00')
            ->where('invoice.created_at','<=',$end.' 23:59:59')
            ->get();

        $invoice_ids = $invoices->pluck('id');

        // get details
        $inv_details = InvoiceDetail::join('items','invoicedetail.item_id','items.id')
            ->select('invoicedetail.*', 'items.hpp')
            ->whereIn('invoiceid', $invoice_ids)
            ->get()
            ->groupBy('invoiceid');

        // recount total in and tax each invoice
        $total_in = 0;
        $total_tax = 0;
        $summary = [];

        foreach ($invoices as $invoice) {
            if(isset($inv_details[$invoice->id])){
                $details = $inv_details[$invoice->id];

                $total = 0;
                $totaladdons = 0;
                $detail_ids = $details->pluck('id');

                $all_addons = InvoiceAddons::join('invoicedetail','invoiceaddons.invoicedetailid','invoicedetail.id')
                    ->join('items','invoicedetail.item_id','items.id')
                    ->select('invoiceaddons.*', 'items.hpp')
                    ->whereIn('invoicedetailid',$detail_ids)
                    ->groupBy('row_group')
                    ->get()
                    ->groupBy('invoicedetailid');
                
                foreach ($details as $key => $value) {
                    $price = ($value['price']-$value['discount']-($value['hpp'] ? $value['hpp'] : 0)) * $value['qty'];
                    $total += $price;

                    if(isset($all_addons[$value['id']])){
                        $addons = $all_addons[$value['id']];
                        if($addons->count() > 0){
                            $item_ids_on_addons = [];

                            foreach($addons as $addondata){
                                $single = $addondata['single_addon'];
                                $multiple = $addondata['multiple_addon'];
                                
                                if(!empty($single)){
                                    $arraysingle=explode(',', $single);
                                    foreach($arraysingle as $valsingle){
                                        $item_ids_on_addons[] = explode('-', $valsingle)[1];
                                    }
                                }
                        
                                if(!empty($multiple)){
                                    $arraymultiple=explode(',', $multiple);
                                    foreach($arraymultiple as $valmultiple){
                                        $item_ids_on_addons[] = explode('-', $valmultiple)[1];
                                    }
                                }
                            }

                            $item_ids_on_addons = array_unique($item_ids_on_addons);
                            sort($item_ids_on_addons);

                            $item_hpp = Items::whereIn('id', $item_ids_on_addons)->pluck('hpp', 'id')->toArray();

                            foreach($addons as $addondata){
                                $single = $addondata['single_addon'];
                                $multiple = $addondata['multiple_addon'];

                                $pricesingle=0;
                                if(!empty($single)){
                                    $arraysingle=explode(',', $single);
                                    foreach($arraysingle as $valsingle){
                                        $addon_price = explode('-', $valsingle)[2];
                                        $addon_hpp = isset($item_hpp[explode('-', $valsingle)[1]]) ? $item_hpp[explode('-', $valsingle)[1]] : 0;
                                        $pricesingle=$pricesingle+($addon_price - $addon_hpp);
                                    }
                                }
                        
                                $pricemultiple=0;
                                if(!empty($multiple)){
                                    $arraymultiple=explode(',', $multiple);
                                    foreach($arraymultiple as $valmultiple){
                                        $addon_price = explode('-', $valmultiple)[2];
                                        $addon_hpp = isset($item_hpp[explode('-', $valmultiple)[1]]) ? $item_hpp[explode('-', $valmultiple)[1]] : 0;
                                        $pricemultiple=$pricemultiple+($addon_price - $addon_hpp);
                                    }
                                }
                                
                                $addonsPrice = $pricesingle+$pricemultiple;

                                $priceaddons = $addondata['addon_qty']*$addonsPrice;
                                $totaladdons = $totaladdons+$priceaddons;
                            }
                        }                        
                    }
                }
            }

            $gettax = (($total + $totaladdons) * $invoice->tax) / 100;
            $total_tax = $total_tax + $gettax;

            $getcharge = (($total + $totaladdons) * $invoice->charge) / 100;
            $total_in = $total_in + ($total + $totaladdons + $getcharge);

            $invoice->total_tax = $gettax;
            $invoice->total_in = $total + $totaladdons + $getcharge;

            $summary[$invoice->catalog_id]['pengeluaran'][] = $gettax;
            $summary[$invoice->catalog_id]['pemasukan'][] = $total + $totaladdons + $getcharge;
        }

        $pengeluarans = PengeluaranDetail::join('pengeluaran','pengeluaran_detail.pengeluaran_id','pengeluaran.id')
            ->select('pengeluaran_detail.*', DB::raw('(harga * qty) as subtotal'), 'pengeluaran.catalog_id')
            ->whereIn('pengeluaran.catalog_id', $catalog)
            ->where('pengeluaran.datetime','>=',$start.' 00:00:00')
            ->where('pengeluaran.datetime','<=',$end.' 23:59:59')
            ->get();

        $total_pengeluaran = $pengeluarans->sum('subtotal');
        $total_pengeluaran = $total_pengeluaran + $total_tax;

        $income = [];
        $income['Pengeluaran'] = $total_pengeluaran;
        $income['Pemasukan'] = $total_in;
        if(($total_in - $total_pengeluaran) > 0){
            $income['Keuntungan'] = $total_in - $total_pengeluaran;
        }

        // income statement pie chart
        $pie['data'] = implode(',', $income);
        $colorArray = ['#f56954', '#00a65a', '#f39c12', '#00c0ef', '#3c8dbc', '#d2d6de'];
        $pie['color'] = implode(',', array_slice($colorArray,0,(count($income))));
        $pie['label'] = implode(',', array_keys($income));

        $income['Keuntungan'] = $total_in - $total_pengeluaran;
        // arsort($income);
        
        // listing outcome
        $out_temp = [];
        foreach ($pengeluarans as $key => $value) {
            $out_temp[$value->nama][] = $value->subtotal;
            $summary[$value->catalog_id]['pengeluaran'][] = $value->subtotal;
        }

        $out = [];
        $out['Tax'] = $total_tax;
        foreach ($out_temp as $key => $values) {
            $out[$key] = array_sum($values);
        }

        $svalue=array_values($out);
        rsort($svalue);
        $sorted_out=array();
        foreach ($svalue as $key => $value) {
            $kk=array_search($value,$out);
            $sorted_out[$kk]=$value;
            unset($out[$kk]);
        }

        // take only biggest 10
        $sorted_out = array_slice($sorted_out, 0, 10);

        $out['data'] = implode(',', $sorted_out);
        $out['label'] = implode(',', array_keys($sorted_out));



        // $periodday = Invoice::whereIn('catalog_id', $catalog)
        //                         ->where('invoice.invoice_type','Permanent')
        //                         ->where('status',$status)
        //                         ->where('created_at','>=',$start.' 00:00:00')
        //                         ->where('created_at','<=',$end.' 23:59:59')
        //                         ->groupBy(DB::raw('Date(created_at)'))
        //                         ->orderBy('invoice_number');
        // $periodmonth = Invoice::whereIn('catalog_id', $catalog)
        //                         ->where('invoice.invoice_type','Permanent')
        //                         ->where('status',$status)
        //                         ->where('created_at','>=',$start.' 00:00:00')
        //                         ->where('created_at','<=',$end.' 23:59:59')
        //                         ->groupBy(DB::raw('Month(created_at)'))
        //                         ->orderBy('invoice_number');
        // $itemDay = InvoiceDetail::select('invoicedetail.*','invoice.invoice_number','items.items_name')
        //                     ->leftJoin('invoice','invoicedetail.invoiceid','invoice.id')
        //                     ->join('items','invoicedetail.item_id','items.id')
        //                     ->where('invoice.invoice_type','Permanent')
        //                     // ->where('invoice.catalog_id',$catalog['id'])
        //                     ->whereIn('invoice.catalog_id', $catalog)
        //                     ->where('invoicedetail.item_status',$status)
        //                     ->where('invoicedetail.created_at','>=',$start.' 00:00:00')
        //                     ->where('invoicedetail.created_at','<=',$end.' 23:59:59')
        //                     ->groupBy(DB::raw('Date(invoicedetail.created_at)'),'item_id')
        //                     ->orderBy('invoicedetail.id');
        // $itemMonth = InvoiceDetail::select('invoicedetail.*','invoice.invoice_number','items.items_name')
        //                     ->leftJoin('invoice','invoicedetail.invoiceid','invoice.id')
        //                     ->join('items','invoicedetail.item_id','items.id')
        //                     ->where('invoice.invoice_type','Permanent')
        //                     // ->where('invoice.catalog_id',$catalog['id'])
        //                     ->whereIn('invoice.catalog_id', $catalog)
        //                     ->where('invoicedetail.item_status',$status)
        //                     ->where('invoicedetail.created_at','>=',$start.' 00:00:00')
        //                     ->where('invoicedetail.created_at','<=',$end.' 23:59:59')
        //                     ->groupBy(DB::raw('Month(invoicedetail.created_at)'),'item_id')
        //                     ->orderBy('invoicedetail.id');

        // $data['request'] = $request->all();
        // $data['getDay'] = $periodday->get();
        // $data['getMonth'] = $periodmonth->get();

        // // regenerate summary all catalogs
        // $getMonth = $data['getMonth'];
        
        // if($getMonth){
        //     foreach ($getMonth as $key => $value) {
        //         $date = $value->created_at;
        //         $month = explode("-",$date)[1];
        //         $year = explode("-",$date)[0];

        //         $summary[$value->catalog_id]['total'][] = getData::getCountTransactionMonth($month,$year);
        //         $summary[$value->catalog_id]['payment'][] = getData::getTotalTransactionMonth($month,$year);
        //     }
        // }

        $data['summary'] = $summary;
        $data['catalogs'] = Catalog::where('user_id', $user_id)->orderBy('catalog_title')->pluck('catalog_title', 'id');

        // $data['itemDay'] = $itemDay->get();
        // $data['itemMonth'] = $itemMonth->get();
        // $data['pagination'] = $data['getData']->appends(['searchfield'=>($request->searchfield == '')?"":$request->searchfield])->links();
        $data['titlepage']='Income Statement';
        $data['maintitle']='Income Statement';
        $data['status']=$status;
        $data['start']=$start;
        $data['end']=$end;
        $data['pie']=$pie;
        $data['queries']=$income;
        $data['sold']=$out;
        // $data['viewed']=$viewed;
        $data['sorted_sold']=$sorted_out;
        // $data['sorted_viewed']=$sorted_viewed;
        return view('pages.transaction.income',$data);
    }
    public function generateStruk($inv)
    {
        $data['invoice'] = Invoice::where('id', $inv)->first();
        $data['item'] = InvoiceDetail::where('invoiceid', $data['invoice']['id'])
            ->groupBy('category')
            ->orderBy('id')
            ->get();
        return view('pages.transaction.print', $data);
    }
    public function generateItem()
    {
        // Generate invoice detail, to get item sold and viewed number
        $user_id = Auth::user()->id;
        if(Auth::user()->parent_id){
            $user_id = Auth::user()->parent_id;
        }

        if(Session::get('catalogsession') == 'All'){
            $catalog = Catalog::where('user_id', $user_id)->pluck('id');
        }else{
            $catalog = Catalog::where('id', Session::get('catalogsession'))->orderBy('id','desc')->pluck('id');
        }

        $invoices = Invoice::whereIn('catalog_id', $catalog)->pluck('id');

        $inv_details = [];
        if(count($invoices)){
            if(request()->update){
                $inv_details = InvoiceDetail::whereIn('invoiceid', $invoices)->get();
            }
            else{
                $inv_details = InvoiceDetail::whereIn('invoiceid', $invoices)->whereNull('generated')->get();
            }
        }

        $items = [];
        $viewed = [];
        $ids_detail = [];
        if(count($inv_details)){
            foreach ($inv_details as $key => $value) {
                // sold status
                if($value->item_status == 'Completed'){
                    $items[$value->item_id][] = $value->qty;
                }
                
                // viewed status
                $viewed[$value->item_id][] = $value->item_id;
                
                // update generated status
                $ids_detail[] = $value->id;
            }
        }

        if(count($ids_detail)){
            InvoiceDetail::whereIn('id', $ids_detail)->update([
                'generated' => 1
            ]);
        }

        // get item ids
        $array_keys = array_keys($viewed);

        $new_items = [];
        $new_viewed = [];
        if(count($array_keys)){
            foreach ($array_keys as $key => $value) {
                $new_items[$value] = isset($items[$value]) ? array_sum($items[$value]) : 0;
                $new_viewed[$value] = isset($viewed[$value]) ? count($viewed[$value]) : 0;
            }
        }

        $saved = 0;
        $update = [];
        if(count($new_items)){
            $array_keys = array_keys($new_items);
            $items = Items::whereIn('id', $array_keys)->pluck('id');
            foreach ($items as $key => $value) {
                $viewed_item = isset($new_viewed[$value]) ? $new_viewed[$value] : 0;
                $sold_item = isset($new_items[$value]) ? $new_items[$value] : 0;

                $update[] = '('.$value.','.$sold_item.','.$viewed_item.')';
            }
        }

        if($update){
            $results = DB::select("INSERT into `items` (id,sell,viewed)
            VALUES 
                ".implode(',', $update)."
            ON DUPLICATE KEY UPDATE 
                sell = VALUES(sell),
                viewed = VALUES(viewed);");
        }

        return $saved;
    }

}
