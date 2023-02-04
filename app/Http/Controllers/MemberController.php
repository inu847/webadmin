<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\User;

use Auth;
use Carbon\Carbon;

class MemberController extends Controller
{
  public function __construct()
  {
      $this->middleware('auth');
  }
  public function index(Request $request)
  {
    $data['titlepage']='Affiliate Data';
    $data['maintitle']='Affiliate Data';
    return view('pages.member.data',$data);
  }
  public function getData(Request $request){
        $columns = ['username','name','email','phone'];
        $keyword = trim($request->input('searchfield'));
        $query = User::with(['catalog', 'member'])
                        ->whereNull('parent_id')
                        // ->where('legitimate','Y')
                        ->where(function($result) use ($keyword,$columns){
                            foreach($columns as $column)
                            {
                                if($keyword != ''){
                                    $result->orWhere($column,'LIKE','%'.$keyword.'%');
                                }
                            }
                        })
                        ->orderBy('id','desc');

        if($request->affiliate){
          $query->where('as_affiliate', 1);
        }
        else{
          $query->where('level','Member');
          $query->where('owner',1);
        }

        $data['request'] = $request->all();
        $data['getData'] = $query->paginate(10);
        $data['pagination'] = $data['getData']->links();
        return view('pages.member.table',$data);
    }
  public function show($id){
    $query = User::with(['catalog', 'member'])->where('level','Member')
                  ->where('owner',1)
                //   ->where('legitimate','Y')
                  ->where('id',$id)
                  ->first();

                  dd($query);
                  
    return $query;
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
  public function block($id,$active)
  {
      if(User::block_user($id,$active)){
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
