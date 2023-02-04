<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\Addon;
use Illuminate\Http\Request;

use App\Models\Items;
use App\Models\ItemsDetail;
use App\Models\Category;
use App\Models\Recipe;
use App\Models\ItemsStock;
use App\User;
use App\Models\Catalog;
use App\Models\Ingredient;
use App\Models\ItemAddon;
use App\Models\PriceType;
use Auth;
use Session;
use DB;

class ItemsController extends Controller
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
        // $data['categoryadd'] = Category::where('user_id',Auth::user()->id)->where('category_type','Add')->get();
        // $data['addons'] = Items::where('user_id',Auth::user()->id)->where('item_type','Add')->orderBy('id','desc')->get();
        $data['titlepage']='Items';
        $data['maintitle']='Manage Items';
        $data['catalog'] = Catalog::where('user_id',Auth::user()->id)->orderBy('catalog_title','asc')->pluck('catalog_title', 'id')->toArray();
        return view('pages.master.items.data',$data);
    }

    public function getData(Request $request){
        $columns = ['items_name'];
        $keyword = trim($request->input('searchfield'));

        $query = Items::with('catalog')->select('items.*','items_stock.stock')
                        ->leftJoin('items_stock', 'items_stock.item_id', '=', 'items.id')
                        ->where('items.user_id',Auth::user()->id)
                        ->where('item_type','Main')
                        ->where(function($result) use ($keyword,$columns){
                            foreach($columns as $column)
                            {
                                if($keyword != ''){
                                    $result->orWhere($column,'LIKE','%'.$keyword.'%');
                                }
                            }
                        })
                        ->orderBy('items.id','desc');

        if($request->searchCatalog){
          $catalog = trim($request->input('searchCatalog'));
          $items = DB::table('catalog_items')->where([
              'user_id' => Auth::user()->id,
              'catalog_id' => $catalog
          ])->pluck('item_id')->toArray();

          if($items){
            $query = $query->whereIn('id', $items);
          }
        }

        $data['request'] = $request->all();
        $data['getData'] = $query->paginate(10);
        $data['pagination'] = $data['getData']->links();
        return view('pages.master.items.table',$data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
      $data['catalog'] = Catalog::where('user_id',Auth::user()->id)->orderBy('catalog_title','asc')->pluck('catalog_title', 'id')->toArray();
      // $data['catalogs'] = Catalog::where('user_id',Auth::user()->id)->orderBy('catalog_title')->get();
      // $data['price_types'] = PriceType::where('user_id',Auth::user()->id)->orderBy('price_name')->get();
      return view('pages.master.items.form',$data);
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
            'imagefile_one' => 'required|max:1000|mimes:jpeg,jpg,png',
            //'items_description' => 'required|min:20',
            'items_price' => 'required|numeric',
            'items_discount' => 'required|numeric',
            'items_youtube' => 'nullable|url',
        ]);
        if ($request->hasFile('imagefile_two')) {
            $this->validate($request, [
                'imagefile_two' => 'required|max:1000|mimes:jpeg,jpg,png'
            ]);
        }
        elseif ($request->hasFile('imagefile_three')) {
            $this->validate($request, [
                'imagefile_three' => 'required|max:1000|mimes:jpeg,jpg,png'
            ]);
        }
        elseif ($request->hasFile('imagefile_four')) {
            $this->validate($request, [
                'imagefile_four' => 'required|max:1000|mimes:jpeg,jpg,png'
            ]);
        }
        if(Items::save_data($request)){
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
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $response['catalog'] = DB::table('catalog_items')->where([
            'user_id' => Auth::user()->id,
            'item_id' => $id
        ])->pluck('catalog_id')->toArray();

        // $data['price_types'] = PriceType::where('user_id',Auth::user()->id)->orderBy('price_name')->get();
        $response['data'] = Items::select('items.*','items_stock.stock')
        ->leftJoin('items_stock', 'items_stock.item_id', '=', 'items.id')
        ->where('items.id',$id)->first();

        return $response;
        // $query=Items::where('id',$id)->first();
        // return $query;
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
            'items_price' => 'required|numeric',
            //'items_description' => 'required|min:20',
            'items_discount' => 'required|numeric',
            'items_youtube' => 'nullable|url',
        ]);
        if ($request->hasFile('imagefile_one')) {
            $this->validate($request, [
                'imagefile_one' => 'required|max:1000|mimes:jpeg,jpg,png'
            ]);
        }
        elseif ($request->hasFile('imagefile_two')) {
            $this->validate($request, [
                'imagefile_two' => 'required|max:1000|mimes:jpeg,jpg,png'
            ]);
        }
        elseif ($request->hasFile('imagefile_three')) {
            $this->validate($request, [
                'imagefile_three' => 'required|max:1000|mimes:jpeg,jpg,png'
            ]);
        }
        elseif ($request->hasFile('imagefile_four')) {
            $this->validate($request, [
                'imagefile_four' => 'required|max:1000|mimes:jpeg,jpg,png'
            ]);
        }
        if(Items::update_data($request)){
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
        if(Items::delete_data($id)){
            $status='success';
            $message='Your request was successful.';
        }else{
            $status='error';
            $message='Oh snap! something went wrong.';
        }
        $notif=['status'=>$status,'message'=>$message];
        return response()->json($notif);
    }

    public function gallery($id){
        $data['getData'] = $this->show($id)['data'];
        return view('pages.master.items.gallery',$data);
    }

    public function primaryimage($id,$image){
        if(Items::primary_image($id,$image)){
            $status='success';
            $message='Your request was successful.';
        }else{
            $status='error';
            $message='Oh snap! something went wrong.';
        }
    }
    public function deleteimage($id,$image,$position){
        if(Items::delete_image($id,$image,$position)){
            $status='success';
            $message='Your request was successful.';
        }else{
            $status='error';
            $message='Oh snap! something went wrong.';
        }
    }
    public function addons(Request $request,$id=null){
      $data['item']=$this->show($id)['data'];
      if($request->isMethod('post')){
        $data['detail'] = ItemAddon::where('user_id', Auth::user()->id)->where('item_id',$id)->orderBy('id', 'desc')->paginate(10);
        
        return view('pages.master.items.tableaddons',$data);
      }else{
        $data['titlepage']='Manage Add Ons';
        $data['maintitle']='Manage Add Ons';

        return view('pages.master.items.addons',$data);
      }
    }
    public function addaddons($id=null, Request $request){
        if($request->isMethod('post')){
          $this->validate($request, [
            'category_id' => 'required',
          ]);
          // if(ItemsDetail::save_data($request)){
          //   $status='success';
          //   $message='Your request was successful.';
          // }else{
          //   $status='error';
          //   $message='Oh snap! something went wrong.';
          // }
          try {
            $data = $request->all();
            foreach (explode(',', $data['addons_id']) as $key => $value) {
              $dataAddon['user_id'] = Auth::user()->id;
              $dataAddon['catalog_id'] = Session::get('catalogsession') == 'All' ? null : Session::get('catalogsession');;
              $dataAddon['category_id'] = $data['category_id'];
              $dataAddon['item_id'] = $data['item_id'];
              $dataAddon['check_type'] = $data['check_type'];
              $dataAddon['addons_id'] = $value;

              ItemAddon::create($dataAddon);
            }
            $status='success';
            $message='Your request was successful.';
          } catch (\Throwable $th) {
            $status='error';
            $message='Oh snap! something went wrong.';
          }

          $notif=['status'=>$status,'message'=>$message];
          return response()->json($notif);
        }else{
          $data['itemid']=$id;
          $data['category'] = Category::where('user_id',Auth::user()->id)->where('category_type','Add')->get();
          // $data['addons'] = Items::where('user_id',Auth::user()->id)->where('item_type','Add')->orderBy('id','desc')->get();
          $data['addons'] = Addon::where('user_id',Auth::user()->id)->orderBy('id','desc')->get();
          return view('pages.master.items.formaddons',$data);
        }
    }
    public function deleteaddons($item=null, $addon=null){
      // if(ItemsDetail::delete_data($addon)){
      if(ItemAddon::find($addon)->delete()){
        $status='success';
        $message='Your request was successful.';
      }else{
        $status='error';
        $message='Oh snap! something went wrong.';
      }
      $notif=['status'=>$status,'message'=>$message];
      return response()->json($notif);
    }
    public function checkaddons($item,$addons)
    {
        $query = ItemsDetail::where('item_id',$item)->get();
        if(count((array)$query) > 0){
            foreach($query as $value){
              if($value['addon'] == $addons){
                return 1;
              }
            }
        }else{
            return 0;
        }
    }
    public function ingredient(Request $request,$id=null){
      $child = User::select('id')->where('parent_id',Auth::user()->id)->get();
      $userdata = array_merge(json_decode($child,true),[['id'=>Auth::user()->id]]);

      $data['detail'] = Recipe::where('parent_id',$id)->get();

      $data['item']=$this->show($id)['data'];
      $data['material'] = Ingredient::where('user_id', Auth::user()->id)->get();

      if($request->isMethod('post')){
        return view('pages.master.items.tableingredient',$data);
      }else{
        $data['titlepage']='Manage Ingredient';
        $data['maintitle']='Manage Ingredient';
        return view('pages.master.items.ingredient',$data);
      }
    }
    public function detailingredient($id=null){
      $query = Recipe::select('recipe.*',
                          'items.items_name',
                          'items.item_unit',
                      )
                      ->leftJoin('items','recipe.item_id','=','items.id')
                      ->where('recipe.id',$id)
                      ->first();
      return $query;
    }
    public function addingredient($id=null, Request $request){
      $this->validate($request, [
        'serving_size' => 'numeric|min:1|max:100000',
      ]);
      if(Recipe::save_data($request)){
        $status='success';
        $message='Your request was successful.';
      }else{
        $status='error';
        $message='Oh snap! something went wrong.';
      }
      $notif=['status'=>$status,'message'=>$message];
      return response()->json($notif);
    }
    public function updateingredient($id=null, Request $request){
      $this->validate($request, [
        'serving_size' => 'numeric|min:1|max:100000',
      ]);
      if(Recipe::update_data($request)){
        $status='success';
        $message='Your request was successful.';
      }else{
        $status='error';
        $message='Oh snap! something went wrong.';
      }
      $notif=['status'=>$status,'message'=>$message];
      return response()->json($notif);
    }
    public function deleteingredient($item=null, $addon=null){
      if(Recipe::delete_data($addon)){
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
