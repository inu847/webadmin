<?php

namespace App\Http\Controllers;

use App\Models\UserService;
use Illuminate\Http\Request;
use Auth;

class UserServiceController extends Controller
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
        $data['titlepage']='User Service';
        $data['maintitle']='Manage User Service';
        return view('pages.master.user_service.data',$data);
    }

    public function getData(Request $request){
        $columns = ['title'];
        $keyword = trim($request->input('searchfield'));
        $query = UserService::has('customer')
                        ->where('user_id', Auth::user()->id)
                        ->where('service_type', 1)
                        ->where(function($result) use ($keyword,$columns){
                            foreach($columns as $column)
                            {
                                if($keyword != ''){
                                    $result->orWhere($column,'LIKE','%'.$keyword.'%');
                                }
                            }
                        })
                        ->orderBy('created_at')
                        ->orderBy('title');
        $data['request'] = $request->all();
        $data['getData'] = $query->paginate(10);
        $data['pagination'] = $data['getData']->links();
        return view('pages.master.user_complaint.table',$data);
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
            'handler' => 'required|min:3',
            'handled_on' => 'required|min:16',
        ]);

        if ($request->hasFile('image')) {
            $this->validate($request, [
                'image' => 'max:1000|mimes:jpeg,jpg,png'
            ]);
        }

        if(UserService::save_data($request)){
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
     * @param  \App\Models\UserService  $user_service
     * @return \Illuminate\Http\Response
     */
    public function show(UserService $user_service)
    {
        if($user_service->handled_on){
            $user_service->handled_on = \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $user_service->handled_on)->format('d-m-Y H:i');
        }
        
        $user_service->customer_name = $user_service->customer->customer_name ? $user_service->customer->customer_name : $user_service->customer->customer_email;
        $user_service->customer_phone = $user_service->customer->customer_phone ? $user_service->customer->customer_phone : '-';

        return $user_service;
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\UserService  $user_service
     * @return \Illuminate\Http\Response
     */
    public function edit(UserService $user_service)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\UserService  $user_service
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'handler' => 'required|min:3',
            'handled_on' => 'required|min:16',
        ]);

        if ($request->hasFile('image')) {
            $this->validate($request, [
                'image' => 'max:1000|mimes:jpeg,jpg,png'
            ]);
        }

        if(UserService::update_data($request)){
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
     * @param  \App\Models\UserService  $user_service
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if(UserService::delete_data($id)){
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
