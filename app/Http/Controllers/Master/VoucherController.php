<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Voucher;
use App\Models\Register;

use Auth;

class VoucherController extends Controller
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
    $data['titlepage']='Voucher';
    $data['maintitle']='Manage Voucher';
    return view('pages.master.voucher.data',$data);
  }

  public function getData(Request $request){
    $columns = ['voucher_code','voucher_owner'];
    $keyword = trim($request->input('searchfield'));
    $query = Voucher::where(function($result) use ($keyword,$columns){
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
    return view('pages.master.voucher.table',$data);
  }

  /**
   * Show the form for creating a new resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function create()
  {
    return view('pages.master.voucher.form');
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
          'voucher_code' => 'required|min:5|unique:voucher,voucher_code',
          'voucher_type' => 'required',
          'voucher_nominal' => 'required|numeric',
          'voucher_owner' => 'required|min:2',
      ]);
      if(Voucher::save_data($request)){
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
      $query=Voucher::where('id',$id)->first();
      return $query;
      // $maxposition = Voucher::max('voucher_order');
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
      $data['getData'] = $this->show($id);
      return view('pages.master.voucher.form',$data);
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
        'voucher_code' => 'required|min:5',
        'voucher_type' => 'required',
        'voucher_nominal' => 'required|numeric',
        'voucher_owner' => 'required|min:2',
      ]);
      if(Voucher::update_data($request)){
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
      if(Voucher::delete_data($id)){
          $status='success';
          $message='Your request was successful.';
      }else{
          $status='error';
          $message='Oh snap! something went wrong.';
      }
      toastr()->$status($message);
      return \Redirect::route('voucher.index');
  }
  public function price($id=null){
    $data['getData'] = VoucherPrice::select('voucher_price.*',
                                  'voucher.voucher_code'
                                )
                                ->leftJoin('voucher','voucher_price.voucher_id','=','voucher.id')
                                ->where('voucher_id',$id)
                                ->get();
    $data['voucher']=$this->show($id);
    $data['titlepage']='Voucher Price';
    $data['maintitle']='Manage Voucher Price';
    return view('pages.master.voucher.price',$data);
  }
  public function pricedetail($id=null){
    $query = VoucherPrice::where('id',$id)->first();
    return $query;
  }
  public function addprice(Request $request)
  {
      $this->validate($request, [
        'price' => 'required|numeric',
      ]);
      if(VoucherPrice::save_data($request)){
          $status='success';
          $message='Your request was successful.';
      }else{
          $status='error';
          $message='Oh snap! something went wrong.';
      }
      $notif=['status'=>$status,'message'=>$message];
      return response()->json($notif);
  }
  public function updateprice(Request $request)
  {
      $this->validate($request, [
        'price' => 'required|numeric',
      ]);
      if(VoucherPrice::update_data($request)){
          $status='success';
          $message='Your request was successful.';
      }else{
          $status='error';
          $message='Oh snap! something went wrong.';
      }
      $notif=['status'=>$status,'message'=>$message];
      return response()->json($notif);
  }
  public function pricedelete($id=null)
  {
      if(VoucherPrice::delete_data($id)){
          $status='success';
          $message='Your request was successful.';
      }else{
          $status='error';
          $message='Oh snap! something went wrong.';
      }
      $notif=['status'=>$status,'message'=>$message];
      return response()->json($notif);
  }
  public function usagevoucher($code=null)
  {
    $data['checkout']=Register::where('voucher_code',$code)->where('status','Checkout')->orderBy('id','desc')->get();
    $data['confirmation']=Register::where('voucher_code',$code)->where('status','Confirmation')->orderBy('id','desc')->get();
    $data['approved']=Register::where('voucher_code',$code)->where('status','Approved')->orderBy('id','desc')->get();
    $data['rejected']=Register::where('voucher_code',$code)->where('status','Rejected')->orderBy('id','desc')->get();
    return view('pages.master.voucher.listusage',$data);
  }
}
