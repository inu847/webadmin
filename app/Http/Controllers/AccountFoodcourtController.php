<?php

namespace App\Http\Controllers;

use App\AffiliateFoodcourt;
use App\FoodCourt;
use Illuminate\Http\Request;

class AccountFoodcourtController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $data['titlepage']='Account Foodcourt';
        $data['maintitle']='Account Foodcourt';
        return view('pages.account_foodcourt.data',$data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $data['titlepage']='Create Account Foodcourt';
        $data['maintitle']='Create Account Foodcourt';
        $foodcourts = FoodCourt::orderBy('id', 'desc')->get();

        return view('pages.account_foodcourt.create', $data, ['foodcourts' => $foodcourts]);
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

        if(AffiliateFoodcourt::create($data)){
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
        $query = AffiliateFoodcourt::findOrFail($id);
                    
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
        $data['titlepage']='Edit Account Foodcourt';
        $data['maintitle']='Edit Account Foodcourt';
        $edit = AffiliateFoodcourt::findOrFail($id);
        $foodcourts = FoodCourt::orderBy('id', 'desc')->get();

        return view('pages.account_foodcourt.edit', $data, ['edit' => $edit, 'foodcourts' => $foodcourts]);
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

        if(AffiliateFoodcourt::findOrFail($id)->update($data)){
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
        if(AffiliateFoodcourt::findOrFail($id)->delete()){
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
        $query = AffiliateFoodcourt::where(function($result) use ($keyword){
            if($keyword != ''){
                $foodcourts_id = FoodCourt::where('name','LIKE','%'.$keyword.'%')->pluck('id');
                $result->whereIn('foodcourt_id', $foodcourts_id);
                }
            })
            ->orderBy('id','desc');

        $data['request'] = $request->all();
        $data['getData'] = $query->paginate(10);
        $data['pagination'] = $data['getData']->links();
        return view('pages.account_foodcourt.table',$data);
        }

    public function block($id,$active)
    {
        if(AffiliateFoodcourt::block_user($id,$active)){
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
