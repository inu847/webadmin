<?php

namespace App\Http\Controllers\Master;

use App\Models\Loyalty;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Auth;

class LoyaltyController extends Controller
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
      $data['titlepage']='Loyalty';
      $data['maintitle']='Manage Loyalty';
      return view('pages.master.loyalty.data',$data);
    }
  
    public function getData(Request $request){
      $columns = ['name','description'];
      $keyword = trim($request->input('searchfield'));
      $query = Loyalty::where(function($result) use ($keyword,$columns){
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
      return view('pages.master.loyalty.table',$data);
    }
  
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
      return view('pages.master.loyalty.form');
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
            'name' => 'required',
            'min_order' => 'required|numeric',
            'max_order' => 'required|numeric',
        ]);
        if(Loyalty::save_data($request)){
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
        $query=Loyalty::where('id',$id)->first();
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
        $data['getData'] = $this->show($id);
        return view('pages.master.loyalty.form',$data);
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
            'name' => 'required',
            'min_order' => 'required|numeric',
            'max_order' => 'required|numeric',
        ]);
        if(Loyalty::update_data($request)){
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
        if(Loyalty::delete_data($id)){
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
