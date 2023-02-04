<?php

namespace App\Http\Controllers;

use App\Models\Complaint;
use Illuminate\Http\Request;

use Auth;
use Session;

class ComplaintController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data['titlepage']='Complaint';
        $data['maintitle']='Manage Complaint';
        return view('pages.master.complaint.data',$data);
    }

    public function getData(Request $request){
        $columns = ['title'];
        $keyword = trim($request->input('searchfield'));
        $query = Complaint::where('user_id', Auth::user()->id)
                        ->where(function($result) use ($keyword,$columns){
                            foreach($columns as $column)
                            {
                                if($keyword != ''){
                                    $result->orWhere($column,'LIKE','%'.$keyword.'%');
                                }
                            }
                        })
                        ->orderBy('title');
        $data['request'] = $request->all();
        $data['getData'] = $query->paginate(10);
        $data['pagination'] = $data['getData']->links();
        return view('pages.master.complaint.table',$data);
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
            'title' => 'required|min:3',
        ]);

        if ($request->hasFile('image')) {
            $this->validate($request, [
                'image' => 'max:1000|mimes:jpeg,jpg,png'
            ]);
        }

        if(Complaint::save_data($request)){
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
     * @param  \App\Models\Complaint  $complaint
     * @return \Illuminate\Http\Response
     */
    public function show(Complaint $complaint)
    {
        return $complaint;
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Complaint  $complaint
     * @return \Illuminate\Http\Response
     */
    public function edit(Complaint $complaint)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Complaint  $complaint
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'title' => 'required|min:3',
        ]);

        if ($request->hasFile('image')) {
            $this->validate($request, [
                'image' => 'max:1000|mimes:jpeg,jpg,png'
            ]);
        }

        if(Complaint::update_data($request)){
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
     * @param  \App\Models\Complaint  $complaint
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if(Complaint::delete_data($id)){
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
