<?php

namespace App\Http\Controllers;

use App\Models\Service;
use App\Models\ServiceDetail;
use Illuminate\Http\Request;

use Auth;
use Session;

class ServiceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data['titlepage']='Service';
        $data['maintitle']='Manage Service';
        return view('pages.master.service.data',$data);
    }

    public function getData(Request $request){
        $columns = ['title'];
        $keyword = trim($request->input('searchfield'));
        $query = Service::where('user_id', Auth::user()->id)
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
        return view('pages.master.service.table',$data);
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

        if(Service::save_data($request)){
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
     * @param  \App\Models\Service  $service
     * @return \Illuminate\Http\Response
     */
    public function show(Service $service)
    {
        if(request('detail')){
            $data['titlepage']='Detail Item Service';
            $data['maintitle']='Detail Item Service';
            $data['service']=$service;
            $detail = ServiceDetail::where('service_id', $service->id)->get();
            
            if(request('id_detail')){
                $data['single_data']=ServiceDetail::where('id', request('id_detail'))->first();
            }
            
            return view('pages.master.service.detail', $data, ['detail' => $detail]);
        }

        return $service;
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Service  $service
     * @return \Illuminate\Http\Response
     */
    public function edit(Service $service)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Service  $service
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        // service_id
        // id_detail
        // title_detail
        // price
        // image
        // description_detail

        if($id > 0){
            $request->request->add(['service_id' => $id]);

            if($request->delete){
                if(ServiceDetail::delete_data($request->id_detail)){
                    $status='success';
                    $message='Your request was successful.';
                }else{
                    $status='error';
                    $message='Oh snap! something went wrong.';
                }
                $notif=['status'=>$status,'message'=>$message];
                return response()->json($notif);
            }

            $this->validate($request, [
                'title_detail' => 'required|min:3',
            ]);
    
            if ($request->hasFile('image')) {
                $this->validate($request, [
                    'image_detail' => 'max:1000|mimes:jpeg,jpg,png'
                ]);
            }
    
            if($request->id_detail){
                if(ServiceDetail::update_data($request)){
                    $status='success';
                    $message='Your request was successful.';
                }else{
                    $status='error';
                    $message='Oh snap! something went wrong.';
                }
            }
            else{
                if(ServiceDetail::save_data($request)){
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

        $this->validate($request, [
            'title' => 'required|min:3',
        ]);

        if ($request->hasFile('image')) {
            $this->validate($request, [
                'image' => 'max:1000|mimes:jpeg,jpg,png'
            ]);
        }

        if(Service::update_data($request)){
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
     * @param  \App\Models\Service  $service
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if(Service::delete_data($id)){
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
