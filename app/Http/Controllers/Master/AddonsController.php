<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\Addon;
use Illuminate\Http\Request;

use App\Models\Items;
use App\Models\Recipe;
use App\Models\ItemsStock;
use App\Models\InvoiceRecipe;

use Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;

class AddonsController extends Controller
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
    //     $data['titlepage']='Add Ons';
    //     $data['maintitle']='Manage Add Ons';
    //     return view('pages.master.addons.data',$data);
    // }

    // public function getData(Request $request){
    //     $columns = ['items_name'];
    //     $keyword = trim($request->input('searchfield'));
    //     $query = Items::where('user_id',Auth::user()->id)
    //                     ->where('item_type','Add')
    //                     ->where(function($result) use ($keyword,$columns){
    //                         foreach($columns as $column)
    //                         {
    //                             if($keyword != ''){
    //                                 $result->orWhere($column,'LIKE','%'.$keyword.'%');
    //                             }
    //                         }
    //                     })
    //                     ->orderBy('id','desc');


    //     $data['request'] = $request->all();
    //     $data['getData'] = $query->paginate(10);

    //     foreach ($data['getData'] as $key => $value) {
    //       $serving = Recipe::select('recipe.*',
    //                       'items.items_name',
    //                       'items.item_unit',
    //                   )
    //                   ->leftJoin('items','recipe.item_id','=','items.id')
    //                   ->where('parent_id',$value->id)
    //                   ->first();
    //       $data['getData'][$key]['serving_size'] = $serving->serving_size ?? '-';
    //       $data['getData'][$key]['item_unit'] = $serving->item_unit ?? '-';
    //     }

    //     $data['pagination'] = $data['getData']->links();
    //     return view('pages.master.addons.table',$data);
    // }

    // /**
    //  * Show the form for creating a new resource.
    //  *
    //  * @return \Illuminate\Http\Response
    //  */
    // public function create()
    // {
    //     //
    // }

    // /**
    //  * Store a newly created resource in storage.
    //  *
    //  * @param  \Illuminate\Http\Request  $request
    //  * @return \Illuminate\Http\Response
    //  */
    // public function store(Request $request)
    // {
    //     $this->validate($request, [
    //         'items_name' => 'required|min:3',
    //         //'imagefile_one' => 'required|max:1000|mimes:jpeg,jpg,png',
    //         //'items_description' => 'nullable|min:20',
    //         'items_price' => 'required|numeric',
    //     ]);
    //     if(Items::save_data($request)){
    //         $status='success';
    //         $message='Your request was successful.';
    //     }else{
    //         $status='error';
    //         $message='Oh snap! something went wrong.';
    //     }
    //     $notif=['status'=>$status,'message'=>$message];
    //     return response()->json($notif);
    // }

    // /**
    //  * Display the specified resource.
    //  *
    //  * @param  int  $id
    //  * @return \Illuminate\Http\Response
    //  */
    // public function show($id)
    // {
    //     $query=Items::where('id',$id)->first();
    //     return $query;
    // }

    // /**
    //  * Show the form for editing the specified resource.
    //  *
    //  * @param  int  $id
    //  * @return \Illuminate\Http\Response
    //  */
    // public function edit($id)
    // {

    // }

    // /**
    //  * Update the specified resource in storage.
    //  *
    //  * @param  \Illuminate\Http\Request  $request
    //  * @param  int  $id
    //  * @return \Illuminate\Http\Response
    //  */
    // public function update(Request $request, $id)
    // {
    //     $this->validate($request, [
    //         'items_name' => 'required|min:3',
    //         'items_price' => 'required|numeric',
    //         //'items_description' => 'nullable|min:20',
    //     ]);
    //     if ($request->hasFile('imagefile_one')) {
    //         $this->validate($request, [
    //             'imagefile_one' => 'required|max:1000|mimes:jpeg,jpg,png'
    //         ]);
    //     }
    //     if(Items::update_data($request)){
    //         $status='success';
    //         $message='Your request was successful.';
    //     }else{
    //         $status='error';
    //         $message='Oh snap! something went wrong.';
    //     }
    //     $notif=['status'=>$status,'message'=>$message];
    //     return response()->json($notif);
    // }

    // /**
    //  * Remove the specified resource from storage.
    //  *
    //  * @param  int  $id
    //  * @return \Illuminate\Http\Response
    //  */
    // public function destroy($id)
    // {
    //     if(Items::delete_data($id)){
    //         $status='success';
    //         $message='Your request was successful.';
    //     }else{
    //         $status='error';
    //         $message='Oh snap! something went wrong.';
    //     }
    //     $notif=['status'=>$status,'message'=>$message];
    //     return response()->json($notif);
    // }

    // public function serving(Request $request,$id=null){
    //   $data['item']=$this->show($id);
    //   $data['material'] = ItemsStock::select('items_stock.*','items.id as itemid','items.items_name')
    //                             ->leftJoin('items','items_stock.item_id','items.id')
    //                             ->where('items.item_type','Material')
    //                             ->where('items.ready_stock','Y')
    //                             ->groupBy('items_stock.item_id')
    //                             ->orderBy('items.items_name','asc')
    //                             ->get();
    //   $data['detail'] = Recipe::select('recipe.*',
    //                       'items.items_name',
    //                       'items.item_unit',
    //                   )
    //                   ->leftJoin('items','recipe.item_id','=','items.id')
    //                   ->where('parent_id',$id)
    //                   ->get();
    //   if($request->isMethod('post')){
    //     return view('pages.master.addons.tableserving',$data);
    //   }else{
    //     $data['titlepage']='Manage Serving';
    //     $data['maintitle']='Manage Serving';
    //     return view('pages.master.addons.serving',$data);
    //   }
    // }
    // public function detailserving($id=null){
    //   $query = Recipe::select('recipe.*',
    //                       'items.items_name',
    //                       'items.item_unit',
    //                   )
    //                   ->leftJoin('items','recipe.item_id','=','items.id')
    //                   ->where('recipe.id',$id)
    //                   ->first();
    //   return $query;
    // }
    // public function addserving($id=null, Request $request){
    //   $this->validate($request, [
    //     'item_id' => 'required',
    //     'serving_size' => 'numeric|min:1|max:100000',
    //   ]);
    //   if(Recipe::save_data($request)){
    //     $status='success';
    //     $message='Your request was successful.';
    //   }else{
    //     $status='error';
    //     $message='Oh snap! something went wrong.';
    //   }
    //   $notif=['status'=>$status,'message'=>$message];
    //   return response()->json($notif);
    // }
    // public function updateserving($id=null, Request $request){
    //   $this->validate($request, [
    //     'item_id' => 'required',
    //     'serving_size' => 'numeric|min:1|max:100000',
    //   ]);
    //   if(Recipe::update_data($request)){
    //     $status='success';
    //     $message='Your request was successful.';
    //   }else{
    //     $status='error';
    //     $message='Oh snap! something went wrong.';
    //   }
    //   $notif=['status'=>$status,'message'=>$message];
    //   return response()->json($notif);
    // }
    // public function deleteserving($item=null, $addon=null){
    //   $check = InvoiceRecipe::where('recipe_id',$addon)->first();
    //   if(!empty($check)){
    //     $status='error';
    //     $message='Oh snap! The row you selected has been used for transaction data.';
    //   }else{
    //     if(Recipe::delete_data($addon)){
    //       $status='success';
    //       $message='Your request was successful.';
    //     }else{
    //       $status='error';
    //       $message='Oh snap! something went wrong.';
    //     }
    //   }
    //   $notif=['status'=>$status,'message'=>$message];
    //   return response()->json($notif);
    // }

    public function index()
    {
        $data['titlepage']='Addons Data';
        $data['maintitle']='Addons Data';
        return view('pages.master.addons.data',$data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $data['titlepage']='Create Addons';
        $data['maintitle']='Create Addons';
        $data = Addon::orderBy('id', 'desc')->get();

        return view('pages.master.addons.create', $data, ['data' => $data]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $data = $request->all();
        
        $data['user_id'] = Auth::user()->id;
        $data['catalog_id'] = Session::get('catalogsession') == 'All' ? null : Session::get('catalogsession');

        $subpath = 'users/'.Auth::user()->id.'/addons';
        if($request->hasFile('image')){
            $file = $request->file('image');
            $filename = uniqid();
            $extension = $file->extension();
            $filenametostore = $filename.'.'.$extension;
            Storage::disk('s3')->putFileAs($subpath, $file, $filenametostore);
            $file_path = Storage::disk('s3')->url($subpath.'/'.$filenametostore);
            $data['image'] = $file_path;
        }

        if(Addon::create($data)){
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
        $query = Addon::findOrFail($id);

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
        $data['titlepage']='Edit Ingredient';
        $data['maintitle']='Edit Ingredient';
        $edit = Addon::findOrFail($id);

        return view('pages.master.addons.edit', $data, ['edit' => $edit]);
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
        $data = $request->all();
        if(Addon::updateData($request)){
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
        if(Addon::findOrFail($id)->delete()){
            $status='success';
            $message='Your request was successful.';
        }else{
            $status='error';
            $message='Oh snap! something went wrong.';
        }
        $notif=['status'=>$status,'message'=>$message];
        return response()->json($notif);
    }

    public function getData(Request $request){
        $keyword = trim($request->input('searchfield'));
        $query = Addon::where(function($result) use ($keyword){
            if($keyword != ''){
                $result->where('name','LIKE','%'.$keyword.'%')->pluck('id');
                }
            })
            ->orderBy('id','desc');

        $data['request'] = $request->all();
        $data['getData'] = $query->paginate(10);
        $data['pagination'] = $data['getData']->links();
        return view('pages.master.addons.table',$data);
    }

    public function block($id,$active)
    {
        if(Addon::block_user($id,$active)){
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
