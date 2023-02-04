<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\Ingredient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class IngredientController extends Controller
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
        $data['titlepage']='Ingredient Data';
        $data['maintitle']='Ingredient Data';
        return view('pages.master.ingredient.data',$data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $data['titlepage']='Create Ingredient';
        $data['maintitle']='Create Ingredient';
        $data = Ingredient::orderBy('id', 'desc')->get();

        return view('pages.master.ingredient.create', $data, ['data' => $data]);
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
        $data['user_id'] = Auth::user()->id;
        $data['catalog_id'] = Session::get('catalogsession') == 'All' ? null : Session::get('catalogsession');
        if(Ingredient::create($data)){
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
        $query = Ingredient::findOrFail($id);

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
        $data['titlepage']='Edit Ingredient';
        $data['maintitle']='Edit Ingredient';
        $edit = Ingredient::findOrFail($id);

        return view('pages.master.ingredient.edit', $data, ['edit' => $edit]);
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
        if(Ingredient::updateData($request)){
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
        if(Ingredient::findOrFail($id)->delete()){
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
      $catalog = (Session::get('catalogsession')=='All')?0:Session::get('catalogsession');
        $keyword = trim($request->input('searchfield'));
        $query = Ingredient::where(function($result) use ($keyword){
            if($keyword != ''){
                $result->where('name','LIKE','%'.$keyword.'%')->pluck('id');
                }
            })
            ->orderBy('id','desc');

        $data['request'] = $request->all();
        $data['getData'] = $query->paginate(10);
        $data['pagination'] = $data['getData']->links();
        return view('pages.master.ingredient.table',$data);
    }

    public function block($id,$active)
    {
        if(Ingredient::block_user($id,$active)){
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
