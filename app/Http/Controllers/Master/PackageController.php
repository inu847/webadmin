<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Package;
use App\Models\MainFeatures;
use App\Models\PackagePrice;

use Auth;

class PackageController extends Controller
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
      $data['titlepage']='Package';
      $data['maintitle']='Manage Package';
      return view('pages.master.package.data',$data);
  }

  public function getData(Request $request){
        $columns = ['package_name'];
        $keyword = trim($request->input('searchfield'));
        $query = Package::where(function($result) use ($keyword,$columns){
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
        return view('pages.master.package.table',$data);
    }

  /**
   * Show the form for creating a new resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function create()
  {
    $data['getData'] = $this->show(0);
    $data['features'] = MainFeatures::get();
    return view('pages.master.package.form',$data);
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
          'package_name' => 'required|min:3',
      ]);
      if(Package::save_data($request)){
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
      $query=Package::where('id',$id)->first();
      return $query;
      // $maxposition = Package::max('package_order');
      // return array_merge(json_decode($query,true),['maxposition'=>$maxposition]);
  }

  /**
   * Show the form for editing the specified resource.
   *
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function edit($id)
  {
      $data['getData'] = $this->show($id);
      $data['features'] = MainFeatures::get();
      return view('pages.master.package.form',$data);
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
        'package_name' => 'required|min:3',
      ]);
      if(Package::update_data($request)){
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
      if(Package::delete_data($id)){
          $status='success';
          $message='Your request was successful.';
      }else{
          $status='error';
          $message='Oh snap! something went wrong.';
      }
      $notif=['status'=>$status,'message'=>$message];
      return response()->json($notif);
  }
  public function price(Request $request,$id=null){
    $data['package']=$this->show($id);
    if($request->isMethod('post')){
      $data['getData'] = PackagePrice::select('package_price.*',
                                    'package.package_name'
                                  )
                                  ->leftJoin('package','package_price.package_id','=','package.id')
                                  ->where('package_id',$id)
                                  ->get();
      return view('pages.master.package.tableprice',$data);
    }else{
      $data['titlepage']='Package Price';
      $data['maintitle']='Manage Package Price';
      return view('pages.master.package.price',$data);
    }
  }
  public function pricedetail($id=null){
    $query = PackagePrice::where('id',$id)->first();
    return $query;
  }
  public function addprice(Request $request)
  {
      $this->validate($request, [
        'price' => 'required|numeric',
      ]);
      if(PackagePrice::save_data($request)){
          $status='success';
          $message='Your request was successful.';
      }else{
          $status='error';
          $message='Oh snap! something went wrong.';
      }
      $notif=['status'=>$status,'message'=>$message];
      return response()->json($notif);
  }
  public function updateprice(Request $request)
  {
      $this->validate($request, [
        'price' => 'required|numeric',
      ]);
      if(PackagePrice::update_data($request)){
          $status='success';
          $message='Your request was successful.';
      }else{
          $status='error';
          $message='Oh snap! something went wrong.';
      }
      $notif=['status'=>$status,'message'=>$message];
      return response()->json($notif);
  }
  public function pricedelete($id=null)
  {
      if(PackagePrice::delete_data($id)){
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
