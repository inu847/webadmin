<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\Items;
use App\Models\ItemsStockNew;  
use App\Models\ItemsStock;  
use App\Models\Catalog;
use App\Models\CatalogItem;
use App\Models\InvoiceDetail;
use App\Models\ItemIn;
use App\User;
use Illuminate\Http\Request;
use Session;
use getData;
use Auth;
use DB;
use Illuminate\Support\Facades\Mail;
use League\Fractal\Resource\Item;

class StockController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    // public function index()
    // {
    //     $catalog_id = Session::get('catalogsession');
    //     $data['titlepage']= getData::getCatalogSession('catalog_title').' Item Stock';
    //     $data['maintitle']='';
    //     $data['page']="Stock";

    //     if ($catalog_id == 'All') {
    //         $item_id = CatalogItem::where('user_id', Auth::user()->id)->pluck('item_id');
    //     }else {
    //         $item_id = CatalogItem::where('catalog_id', $catalog_id)->where('user_id', Auth::user()->id)->pluck('item_id');
    //     }
    //     $data['catalog'] = Items::whereIn('id', $item_id)->get();
    //     // dd($data);
    //     return view('pages.master.stock.data',$data);
    // }

    public function index()
    {
        // $to_name = 'Nanang ';
        // $to_email = 'nanangkoesharwanto@gmail.com';
        // $data = array('name'=>"Sam Jose", "body" => "Test mail");
            
        // $send_email = Mail::send('emails.mail', $data, function($message) use ($to_name, $to_email) {
        //     $message->to($to_email, $to_name)
        //             ->subject('Artisans Web Testing Mail');
        //     $message->from('admin@scaneat.id','Admin Web');
        // });

        // dd($send_email);

        if(Auth::user()->level == 'User'){
            $parent = User::find(Auth::user()->id);
            $child = User::select('id')->where('parent_id',$parent['parent_id'])->get();
            $userdata = array_merge(json_decode($child,true),[['id'=>$parent['parent_id']]]);
        }else{
            $child = User::select('id')->where('parent_id',Auth::user()->id)->get();
            $userdata = array_merge(json_decode($child,true),[['id'=>Auth::user()->id]]);
        }

        $data['titlepage']= getData::getCatalogSession('catalog_title').' Item Stock';
        $data['maintitle']='';
        $data['page']="Stock";
        $data['items_data']=Items::whereIn('items.user_id',$userdata)
            ->where('item_type','Main')
            ->select('items_name', 'items.id')->get();

        if(Session::get('catalogsession') == 'All'){
            $catalog = Catalog::where('user_id',Auth::user()->id)->orderBy('catalog_title','asc')->pluck('catalog_title', 'id')->toArray();
        }else{
            $catalog = Catalog::where('id',Session::get('catalogsession'))->orderBy('catalog_title','asc')->pluck('catalog_title', 'id')->toArray();
        }
        $data['catalog']=$catalog;
        return view('pages.master.stock.data',$data);
    }

    // public function getData(Request $request)
    // {
    //     $catalog_id = Session::get('catalogsession');
    //     $data['titlepage']= getData::getCatalogSession('catalog_title').' Item Stock';
    //     $data['maintitle']='';
    //     $data['page']="Stock";

    //     if ($catalog_id == 'All') {
    //         $item_id = CatalogItem::where('user_id', Auth::user()->id)->pluck('item_id');
    //     }else {
    //         $item_id = CatalogItem::where('catalog_id', $catalog_id)->where('user_id', Auth::user()->id)->pluck('item_id');
    //     }
    //     $data['getData'] = Items::whereIn('id', $item_id)->get();
    //     // dd($data);
    //     return view('pages.master.stock.table',$data);
    // }
    public function getData(Request $request){
        if(Auth::user()->level == 'User'){
            $parent = User::find(Auth::user()->id);
            $child = User::select('id')->where('parent_id',$parent['parent_id'])->get();
            $userdata = array_merge(json_decode($child,true),[['id'=>$parent['parent_id']]]);
        }else{
            $child = User::select('id')->where('parent_id',Auth::user()->id)->get();
            $userdata = array_merge(json_decode($child,true),[['id'=>Auth::user()->id]]);
        }

        $catalog_id = Session::get('catalogsession');
        $item_id = [];
        if ($catalog_id == 'All') {
            $item_id = CatalogItem::where('status', 1)->where('user_id', Auth::user()->id)->pluck('item_id');
        }else {
            $item_id = CatalogItem::where('status', 1)->where('catalog_id', $catalog_id)->where('user_id', Auth::user()->id)->pluck('item_id');
        }

        $query = ItemsStock::whereIn('item_id', $item_id)
                    ->join('items', 'items_stock.item_id', '=', 'items.id')
                    ->join('catalog', 'catalog.id', '=', 'items_stock.catalog')
                    ->whereIn('items.user_id',$userdata)
                    ->where('item_type','Main')
                    ->where('catalog.user_id',Auth::user()->id)
                    ->select('items_name', 'items.created_at', 'catalog_title', 'items.id', DB::raw('SUM(stock) as stock'), 'items_stock.catalog');
        
        if((Session::get('catalogsession')!='All')){
            $query->where('items_stock.catalog', Session::get('catalogsession'));
        }
        else{
            $query->groupBy('items_stock.catalog');
        }
    
        $query->groupBy('items.id');

        if($request->searchfield){
            $query->where('items_name', 'LIKE','%'.$request->searchfield.'%');
        }

        // get items invoice detail
        $pagination = $query->orderBy('catalog_title','asc')->orderBy('items_name','asc')->paginate(10);
        $collection = collect($pagination->items());
        $ids = $collection->pluck('id')->toArray();

        $invoice_detail = InvoiceDetail::join('invoice', 'invoice.id', '=', 'invoicedetail.invoiceid')
            ->join('catalog', 'catalog.id', '=', 'invoice.catalog_id')
            ->where('catalog.user_id',Auth::user()->id)
            ->select('invoicedetail.id', 'invoicedetail.item_id', DB::raw('SUM(qty) as qty'), 'invoice.catalog_id')
            ->whereIn('invoicedetail.item_id', $ids);
            
        if((Session::get('catalogsession')!='All')){
            $invoice_detail = $invoice_detail->where('invoice.catalog_id', Session::get('catalogsession'))
                        ->groupBy('invoicedetail.item_id')->pluck('qty', 'item_id')->toArray();
            
            foreach ($pagination->items() as $key => $value) {
                $minus = isset($invoice_detail[$value->id]) ? $invoice_detail[$value->id] : 0;
                $value->stock = $value->stock - $minus;
            }
        }
        else{
            $invoice_detail = $invoice_detail->whereIn('catalog.user_id', $userdata)->groupBy('invoice.catalog_id')
                        ->groupBy('invoicedetail.item_id')->get();
                        
            foreach ($pagination->items() as $key => $value) {
                $tvalue = $invoice_detail->where('item_id', $value->id)->where('catalog_id', $value->catalog)->all();
                if($tvalue){
                    $minus = $tvalue[0]->qty;
                    $value->stock = $value->stock - $minus;
                }
            }
        }

        $data['request'] = $request->all();
        $data['getData'] = $pagination;
        $data['pagination'] = $data['getData']->links();
        return view('pages.master.stock.table',$data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'item_id' => 'required',
            'stock' => 'required',
        ]);

        if(Session::get('catalogsession')=='All'){
            $status='error';
            $message='Choose Catalog';
        }
        else{
            $valuesToAdd = ['catalog' => Session::get('catalogsession')];
            $request->merge($valuesToAdd);

            if(ItemsStock::save_data($request)){
                $status='success';
                $message='Your request was successful.';
            }else{
                $status='error';
                $message='Oh snap! something went wrong.';
            }
        }
        $notif=['status'=>$status,'message'=>$message];
        return response()->json($notif);
    }


    /**
     * Display the specified resource.
     *
     * @param  \App\Models\ItemsStock  $itemsStock
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $query=ItemsStock::find($id);
        return $query;
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\ItemsStock  $itemsStock
     * @return \Illuminate\Http\Response
     */
    public function edit(ItemsStock $itemsStock)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\ItemsStock  $itemsStock
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'item_id' => 'required',
            'stock' => 'required',
        ]);

        if(ItemsStock::update_data($request)){
            $status='success';
            $message='Your request was successful.';
        }else{
            $status='error';
            $message='Oh snap! something went wrong.';
        }
        $notif=['status'=>$status,'message'=>$message];
        return response()->json($notif);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\ItemsStock  $itemsStock
     * @return \Illuminate\Http\Response
     */
    public function destroy(ItemsStock $itemsStock)
    {
        //
    }

    public function stock(Request $request,$id=null,$catalog=null){
        $data['item']=ItemsStock::join('items', 'items.id', '=', 'items_stock.item_id')
            ->where('item_id', $id)
            ->where('catalog', $catalog)
            ->first();

        Session::forget('catalogsession');
        Session::put('catalogsession',$catalog);
    
        if($request->isMethod('post')){
          if(Auth::user()->level == 'User'){
              $parent = User::find(Auth::user()->id);
              $child = User::select('id')->where('parent_id',$parent['parent_id'])->get();
              $user = array_merge(json_decode($child,true),[['id'=>$parent['parent_id']]]);
          }else{
              $child = User::select('id')->where('parent_id',Auth::user()->id)->get();
              $user = array_merge(json_decode($child,true),[['id'=>Auth::user()->id]]);
          }
          $columns = ['name','stock','notes'];
          $keyword = trim($request->input('searchfield'));
          $query = ItemIn::select('item_ins.*', 'users.name')
                          ->leftJoin('users','item_ins.user_id','=','users.id')
                          ->whereIn('user_id',$user)
                          ->where('item_id',$id)
                          ->where(function($result) use ($keyword,$columns){
                              foreach($columns as $column)
                              {
                                  if($keyword != ''){
                                      $result->orWhere($column,'LIKE','%'.$keyword.'%');
                                  }
                              }
                          })
                          ->orderBy('id','desc');
          $data['request'] = $request->all();
          $data['getData'] = $query->paginate(10);
          $data['pagination'] = $data['getData']->links();
          return view('pages.master.stock.tablestock',$data);
        }else{
            $data['titlepage']='Manage Stock '.$data['item']['items_name'];
            $data['maintitle']='Manage Stock '.$data['item']['items_name'];
            $data['page']="Stock";
            return view('pages.master.stock.stock',$data);
        }
      }
  
      public function updatestock($id=null, Request $request){
        $this->validate($request, [
          'stock' => 'required|numeric|min:1|max:100000',
        ]);
        if(ItemsStock::update_data($request)){
          $status='success';
          $message='Your request was successful.';
        }else{
          $status='error';
          $message='Oh snap! something went wrong.';
        }
        $notif=['status'=>$status,'message'=>$message];
        return response()->json($notif);
    }

    public function deletestock($item=null, $id=null){
        if(ItemsStock::where('id',$id)->where('item_id',$item)->delete()){
          $status='success';
          $message='Your request was successful.';
        }else{
          $status='error';
          $message='Oh snap! something went wrong.';
        }
       $notif=['status'=>$status,'message'=>$message];
       return response()->json($notif);
    }


}
