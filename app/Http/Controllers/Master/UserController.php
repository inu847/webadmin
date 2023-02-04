<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\User;
use App\Models\Catalog;
use App\Models\Role;
use Auth;
use Carbon\Carbon;
use Illuminate\Validation\Rule;

class UserController extends Controller
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
        $data['titlepage']='User';
        $data['maintitle']='Manage User';
        $data['catalog']=Catalog::where('user_id',Auth::user()->id)->get();
        $data['role']=Role::where('owner_id',Auth::user()->id)->orderBy('name', 'asc')->get();

        return view('pages.master.user.data',$data);
    }

    public function getData(Request $request){
        $columns = ['name'];
        $keyword = trim($request->input('searchfield'));
        $query = User::select('users.*','catalog.catalog_title')
                    ->leftJoin('catalog','users.catalog','=','catalog.id')
                    ->with('role')
                    ->where('parent_id',Auth::user()->id)
                    ->where(function($result) use ($keyword,$columns){
                        foreach($columns as $column)
                        {
                            if($keyword != ''){
                                $result->orWhere($column,'LIKE','%'.$keyword.'%');
                            }
                        }
                    })
                    ->orderBy('users.id','desc');
        $data['request'] = $request->all();
        $data['getData'] = $query->paginate(10);
        $data['pagination'] = $data['getData']->links();
        // return $data;
        return view('pages.master.user.table',$data);
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
            'name' => 'required|min:3',
            'catalogid' => 'required',
            'role_id' => 'required',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:5',
            'repassword' => 'required_with:password|same:password|min:5',
            'phone' => 'required|min:10',
        ]);
        if(User::save_data($request)){
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
        $query=User::where('id',$id)->first();
        return $query;
        // $maxposition = User::max('user_order');
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
        if ($id == "0") {
            $this->validate($request, [
                'name' => 'required|min:3',
                'catalogid' => 'required',
                'email' => 'required|email',
                'phone' => 'required|min:10',
            ]);
            if(User::update_data($request)){
                $status='success';
                $message='Edit data was successful.';
            }else{
                $status='error';
                $message='Edit data something went wrong.';
            }
        }
        if ($id == "1") {
            $this->validate($request, [
                'repassword' => 'required_with:password|same:password|min:5',
                'phone' => 'required|min:10',
            ]);
            if(User::update_data($request)){
                $status='success';
                $message='Change password was successful.';
            }else{
                $status='error';
                $message='Change password went wrong.';
            }
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
        if(User::delete_data($id)){
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
