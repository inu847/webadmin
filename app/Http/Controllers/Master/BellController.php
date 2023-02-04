<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Bell;

use Auth;
use getData;
use myFunction;

use Carbon\Carbon;

class BellController extends Controller
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
    public function index(Request $request)
    {
        $data['titlepage']='Bell Notification';
        $data['maintitle']='Bell Notification';
        return view('pages.master.bell.data',$data);
    }
    public function getData(Request $request){
        $columns = ['table_position','message'];
        $keyword = trim($request->input('searchfield'));
        $query = Bell::where('catalog_id',getData::getCatalogSession('id'))
                        ->whereDate('created_at', Carbon::today())
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
        return view('pages.master.bell.table',$data);
    }
    public function show($id)
    {
        $query=Bell::where('id',$id)->first();
        return $query;
    }
}
