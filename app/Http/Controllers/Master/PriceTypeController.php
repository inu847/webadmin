<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\PriceType;
use App\Models\ItemsPrice;
use App\Models\Catalog;
use Illuminate\Http\Request;
use Auth;

class PriceTypeController extends Controller
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
        $data['titlepage']='Price Type';
        $data['maintitle']='Manage Price Type';
        $data['catalogs'] = Catalog::where('user_id',Auth::user()->id)->orderBy('catalog_title')->get();
        return view('pages.master.price_type.data',$data);
    }

    public function getData(Request $request){
        $columns = ['price_name'];
        $keyword = trim($request->input('searchfield'));
        $query = PriceType::with('catalog')->where('user_id',Auth::user()->id)
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
        return view('pages.master.price_type.table',$data);
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
            'price_name' => 'required|min:3',
        ]);
        if(PriceType::save_data($request)){
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
     * @param  \App\PriceType  $priceType
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $query=PriceType::where('id',$id)->first();
        return $query;
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\PriceType  $priceType
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
      $data['getData'] = $this->show($id);
      $data['catalogs'] = Catalog::where('user_id',Auth::user()->id)->orderBy('catalog_title')->get();
      return view('pages.master.price_type.edit_form',$data);
    }
  
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\PriceType  $priceType
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'price_name' => 'required|min:3',
        ]);
        if(PriceType::update_data($request)){
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
     * @param  \App\PriceType  $priceType
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if(ItemsPrice::where('price_type_id', $id)->first()){
            $status='error';
            $message='This type is used in several items.';
        }
        else{
            if(PriceType::delete_data($id)){
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
}
