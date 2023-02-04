<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Items;
use App\Models\ItemsStock;
use App\User;
use App\Models\Catalog;
use App\Models\CatalogItem;
use App\Models\Ingredient;
use App\Models\Invoice;
use App\Models\InvoiceDetail;
use App\Models\ItemIn;
use App\Models\Recipe;
use Auth;
use getData;
use League\Fractal\Resource\Item;
use Session;

class MaterialController extends Controller
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
    public function index()
    {
        $data['titlepage']=(Session::get('catalogsession')=='All')?'Warehouse':getData::getCatalogSession('catalog_title').' Material Stock';
        $data['maintitle']='';
        $data['page']="Stock";
        if(Session::get('catalogsession') == 'All'){
            $catalog = Catalog::where('user_id',Auth::user()->id)->orderBy('catalog_title','asc')->pluck('catalog_title', 'id')->toArray();
        }else{
            $catalog = Catalog::where('id',Session::get('catalogsession'))->orderBy('catalog_title','asc')->pluck('catalog_title', 'id')->toArray();
        }
        $data['catalog']=$catalog;
        return view('pages.master.material.data',$data);
    }

    public function getData(Request $request){
        if(Auth::user()->level == 'User'){
            $parent = User::find(Auth::user()->id);
            $child = User::select('id')->where('parent_id',$parent['parent_id'])->get();
            $userdata = array_merge(json_decode($child,true),[['id'=>$parent['parent_id']]]);
        }else{
            $child = User::select('id')->where('parent_id',Auth::user()->id)->get();
            $userdata = array_merge(json_decode($child,true),[['id'=>Auth::user()->id]]);
        }
        $columns = ['name'];
        $keyword = trim($request->input('searchfield'));

        if((Session::get('catalogsession')!='All')){
            $query = Ingredient::where('user_id', Auth::user()->id)
                                ->where(function($result) use ($keyword,$columns){
                                            foreach($columns as $column)
                                            {
                                                if($keyword != ''){
                                                    $result->orWhere($column,'LIKE','%'.$keyword.'%');
                                                }
                                            }
                                        });
                                // ->where('catalog_id', Session::get('catalogsession'));
        }else{
            $query = Ingredient::where('user_id', Auth::user()->id)
                    ->where(function($result) use ($keyword,$columns){
                        foreach($columns as $column)
                        {
                            if($keyword != ''){
                                $result->orWhere($column,'LIKE','%'.$keyword.'%');
                            }
                        }
                    });
        }

        $data['request'] = $request->all();
        $data['getData'] = $query->orderBy('id','desc')->paginate(10);
        foreach ($data['getData'] as $key => $value) {
            $data['getData'][$key]['total_stock'] = $value->itemStock->sum('stock');
            $data['getData'][$key]['stock_used'] = 0;

            $item_id = Recipe::where('ingredient_id', $value->id)->pluck('parent_id');
            $invoice_id = InvoiceDetail::whereIn('item_id', $item_id)->where('item_status', 'Completed')->pluck('invoiceid');
            $total_invoice = InvoiceDetail::whereIn('item_id', $item_id)->where('item_status', 'Completed')->count();

            if(Session::get('catalogsession')!='All'){
                $invoice_id = Invoice::where('catalog_id', Session::get('catalogsession'))->whereIn('id', $invoice_id)->pluck('id');
                $total_invoice = InvoiceDetail::whereIn('invoiceid', $invoice_id)->whereIn('item_id', $item_id)->where('item_status', 'Completed')->count();
            }


            $count_use = Recipe::where('ingredient_id', $value->id)->get();
            foreach ($count_use as $ckey => $cvalue) {
                $result_use = $cvalue->serving_size * $total_invoice;
                $data['getData'][$key]['stock_used'] += $result_use;
            }

            $data['getData'][$key]['stock_available'] = $data['getData'][$key]['total_stock'] - $data['getData'][$key]['stock_used'];
        }
        $data['pagination'] = $data['getData']->links();
        // dd($data['getData']);
        return view('pages.master.material.table',$data);
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
            'items_name' => 'required|min:3',
            'item_unit' => 'required',
        ]);
        if(Session::get('catalogsession')=='All'){
            $status='error';
            $message='Choose Catalog nya';
        }
        else{
            if(Items::save_material($request)){
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
     * @param  int  $id
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
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'items_name' => 'required|min:3',
            'item_unit' => 'required',
        ]);
        if(Items::update_material($request)){
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
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if(Items::delete_data_material($id)){
            $status='success';
            $message='Your request was successful.';
        }else{
            $status='error';
            $message='Oh snap! something went wrong.';
        }
        $notif=['status'=>$status,'message'=>$message];
        return response()->json($notif);
    }
    public function stock(Request $request,$id=null){
      $data['item']=Ingredient::find($id);
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
        $catalog = (Session::get('catalogsession')=='All')?0:Session::get('catalogsession');
        $keyword = trim($request->input('searchfield'));
        $query = ItemIn::where('ingredient_id', $id)
                        ->whereIn('user_id',$user)
                        ->where('catalog_id',$catalog)
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
        return view('pages.master.material.tablestock',$data);
      }else{
        $data['titlepage']='Manage Stock '.$data['item']['items_name'];
        $data['maintitle']='Manage Stock '.$data['item']['items_name'];
        $data['page']="Stock";
        return view('pages.master.material.stock',$data);
      }
    }
    public function addstock($id=null, Request $request){
        $this->validate($request, [
          'stock' => 'required|numeric|min:1|max:100000',
        ]);

        $item=Ingredient::find($request->input('ingredient_id'));

        $status='error';
        $message='Oh snap! something went wrong.';

        if($item){
            if(ItemsStock::save_data($request)){
                $status='success';
                $message='Your request was successful.';
            }
        }

        // if($item && $item->catalog_id){
        //     if(ItemsStock::save_data($request)){
        //         $status='success';
        //         $message='Your request was successful.';
        //     }else{
        //         $status='error';
        //         $message='Oh snap! something went wrong.';
        //     }
        // }
        // else {
        //     $allbranchstock = ItemsStock::where('item_id',$request->input('item_id'))
        //                         ->where('catalog','>',0)
        //                         ->sum('stock');
        //     $centralstock = getData::countStock($request->input('item_id'),0);
        //     if(($allbranchstock+$request->input('stock')) > $centralstock && Session::get('catalogsession')!='All'){
        //         $status='error';
        //         $message='Oh snap! Qty you entered is too large.';
        //     }else{
        //         if(ItemsStock::save_data($request)){
        //             $status='success';
        //             $message='Your request was successful.';
        //         }else{
        //             $status='error';
        //             $message='Oh snap! something went wrong.';
        //         }
        //     }
        // }

        $notif=['status'=>$status,'message'=>$message];
        return response()->json($notif);
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
    public function deletestock($item=null, $stock=null){
        try {
            $item_in = ItemIn::find($stock);
            $itemStock = ItemsStock::where('ingredient_id', $item)->first();
            $itemStock->stock = $itemStock->stock - $item_in->stock;
            $itemStock->save();
            $item_in->delete();

            $status='success';
            $message='Your request was successful.';
        } catch (\Throwable $th) {
            $status='error';
            $message='Oh snap! something went wrong.';
        }
        $notif=['status'=>$status,'message'=>$message];
        return response()->json($notif);
    }
}
