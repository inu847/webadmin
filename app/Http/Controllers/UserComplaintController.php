<?php

namespace App\Http\Controllers;

use App\Models\UserComplaint;
use Illuminate\Http\Request;
use Auth;

class UserComplaintController extends Controller
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
        $data['titlepage']='User Complaint';
        $data['maintitle']='Manage User Complaint';
        return view('pages.master.user_complaint.data',$data);
    }

    public function getData(Request $request){
        $columns = ['title'];
        $keyword = trim($request->input('searchfield'));
        // $query = UserComplaint::where('user_id', Auth::user()->id)
        //                 ->where('service_type', 2)
        //                 ->where(function($result) use ($keyword,$columns){
        //                     foreach($columns as $column)
        //                     {
        //                         if($keyword != ''){
        //                             $result->orWhere($column,'LIKE','%'.$keyword.'%');
        //                         }
        //                     }
        //                 })
        //                 ->orderBy('created_at')
        //                 ->orderBy('title');

        $query = UserComplaint::has('customer')
                        ->where('user_id', Auth::user()->id)
                        ->where('service_type', 2)
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

        if(UserComplaint::save_data($request)){
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
     * @param  \App\Models\UserComplaint  $user_complaint
     * @return \Illuminate\Http\Response
     */
    public function show(UserComplaint $user_complaint)
    {
        if($user_complaint->handled_on){
            $user_complaint->handled_on = \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $user_complaint->handled_on)->format('d-m-Y H:i');
        }
        
        $user_complaint->customer_name = $user_complaint->customer->customer_name ? $user_complaint->customer->customer_name : $user_complaint->customer->customer_email;
        $user_complaint->customer_phone = $user_complaint->customer->customer_phone ? $user_complaint->customer->customer_phone : '-';

        return $user_complaint;
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\UserComplaint  $user_complaint
     * @return \Illuminate\Http\Response
     */
    public function edit(UserComplaint $user_complaint)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\UserComplaint  $user_complaint
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

        if(UserComplaint::update_data($request)){
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
     * @param  \App\Models\UserComplaint  $user_complaint
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if(UserComplaint::delete_data($id)){
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
