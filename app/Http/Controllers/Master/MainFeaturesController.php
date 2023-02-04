<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\MainFeatures;

use Auth;

class MainFeaturesController extends Controller
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
      $data['titlepage']='Features';
      $data['maintitle']='Manage Features';
      return view('pages.master.mainfeatures.data',$data);
  }

  public function getData(Request $request){
      $columns = ['feature_name'];
      $keyword = trim($request->input('searchfield'));
      $query = MainFeatures::where(function($result) use ($keyword,$columns){
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
      return view('pages.master.mainfeatures.table',$data);
  }

  /**
   * Show the form for creating a new resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function create()
  {

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
          'feature_name' => 'required|min:3',
      ]);
      if(MainFeatures::save_data($request)){
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
      $query=MainFeatures::where('id',$id)->first();
      return $query;
      // $maxposition = MainFeatures::max('feature_order');
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
      //
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
        'feature_name' => 'required|min:3',
      ]);
      if(MainFeatures::update_data($request)){
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
      if(MainFeatures::delete_data($id)){
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
