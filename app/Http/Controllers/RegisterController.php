<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\User;
use App\Models\Package;
use App\Models\Register;

use Auth;
use Carbon\Carbon;

class RegisterController extends Controller
{
  public function __construct()
  {
      $this->middleware('auth');
  }
  public function index($status)
  {
    if($status=='checkout'){
      $data['icon']='lnr-enter-down';
    }
    elseif($status=='confirmation'){
      $data['icon']='pe-7s-exapnd2';
    }
    elseif($status=='approved'){
      $data['icon']='pe-7s-check';
    }
    elseif($status=='rejected'){
      $data['icon']='lnr-cross';
    }
    $data['status'] = ucwords($status);
    $data['package']=Package::get();
    $data['titlepage']='Register Data '.ucwords($status);
    $data['maintitle']='Register Data '.ucwords($status);
    return view('pages.register.data',$data);
  }
  public function getData(Request $request,$status){
    $columns = ['name','invoice','voucher_code','package_name','number_catalog'];
    $keyword = trim($request->input('searchfield'));
    $query = Register::select('register.*','users.name','users.active')
                ->leftJoin('users','register.user_id','=','users.id')
                ->where('register.status',ucwords($status))
                ->where(function($result) use ($keyword,$columns){
                    foreach($columns as $column)
                    {
                        if($keyword != ''){
                            $result->orWhere($column,'LIKE','%'.$keyword.'%');
                        }
                    }
                })
                ->orderBy('register.id','desc');
    $data['status'] = ucwords($status);
    $data['request'] = $request->all();
    $data['getData'] = $query->paginate(10);
    $data['pagination'] = $data['getData']->links();
    return view('pages.register.tablemember',$data);
  }
  public function show($id){
    $query = Register::select('register.*','users.name','users.email','users.phone','users.active')
                    ->leftJoin('users','register.user_id','=','users.id')
                    ->where('register.id',$id)
                    ->first();
    // $query = $query = User::select('users.*',
    //               'register.package_id',
    //               'register.package_name',
    //               'register.affiliate',
    //               'register.price',
    //               'register.expired'
    //               )
    //               ->leftJoin('register','register.user_id','=','users.id')
    //               ->leftJoin('package','register.package_id','=','package.id')
    //               ->where('level','Member')
    //               ->where('owner',1)
    //               ->where('users.id',$id)
    //               ->orderBy('users.id','desc')
    //               ->first();
    return $query;
  }
  public function detail($id){
    $data['myData'] = Register::select('register.*','users.name','users.email','users.phone','users.active')
                    ->leftJoin('users','register.user_id','=','users.id')
                    ->where('register.id',$id)
                    ->first();
    return view('pages.register.detail',$data);
  }
  public function update(Request $request, $id)
  {
      $this->validate($request, [
        'catalog' => 'required|numeric',
      ]);
      if(User::update_register($request)){
          $status='success';
          $message='Your request was successful.';
      }else{
          $status='error';
          $message='Oh snap! something went wrong.';
      }
      $notif=['status'=>$status,'message'=>$message];
      return response()->json($notif);
  }
  public function approve($id)
  {
      if(Register::approve_data($id)){
          $status='success';
          $message='Your request was successful.';
      }else{
          $status='error';
          $message='Oh snap! something went wrong.';
      }
      $notif=['status'=>$status,'message'=>$message];
      return response()->json($notif);
  }
  public function rejected(Request $request)
  {
      $this->validate($request, [
        'notes' => 'required|min:5',
      ]);
      if(Register::reject_data($request)){
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
