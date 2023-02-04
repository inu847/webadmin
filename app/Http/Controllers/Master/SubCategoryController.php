<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use App\Models\SubCategory;
use App\Models\Catalog;
use Auth;

class SubCategoryController extends Controller
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
        $data['titlepage']='Sub Category';
        $data['maintitle']='Manage Sub Category';
        $data['catalog'] = Catalog::where('user_id',Auth::user()->id)->orderBy('catalog_title','asc')->pluck('catalog_title', 'id')->toArray();
        return view('pages.master.subcategory.data',$data);
    }

    public function getData(Request $request){
        $columns = ['subcategory_name'];
        $keyword = trim($request->input('searchfield'));
        $query = SubCategory::with('catalog')->where('user_id',Auth::user()->id)
                        ->where(function($result) use ($keyword,$columns){
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
        return view('pages.master.subcategory.table',$data);
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
            'subcategory_name' => 'required|min:3',
            'imagefile' => 'required|max:1000|mimes:jpeg,jpg,png'
        ]);
        if(SubCategory::save_data($request)){
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
        $response['catalog'] = DB::table('catalog_sub_categories')->where([
            'user_id' => Auth::user()->id,
            'sub_category_id' => $id
        ])->pluck('catalog_id')->toArray();
        $response['data'] = SubCategory::where('id',$id)->first();
        return $response;
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        
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
            'subcategory_name' => 'required|min:3',
        ]);
        if($request->hasFile('imagefile')) {
            $this->validate($request, [
                'imagefile' => 'required|max:1000|mimes:jpeg,jpg,png'
            ]);
        }
        if(SubCategory::update_data($request)){
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
        if(SubCategory::delete_data($id)){
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
