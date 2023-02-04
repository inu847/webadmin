<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Resources\TableResource;

class TableController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     * @param  \Illuminate\Http\Request  $request
     */
    public function index(Request $request)
    {
        $appends['table'] = $request->table;
        $table = DB::table($request->table);

        $pagination = 10;
        if($request->pagination){
            $appends['pagination'] = $request->pagination;
            $pagination = $request->pagination;
        }

        $order = 'created_at';
        if($request->order){
            $appends['order'] = $request->order;
            $order = $request->order;
        }

        $dir = 'ASC';
        if($request->dir){
            $appends['dir'] = $request->dir;
            $dir = $request->dir;
        }

        $table = $table->orderBy($order, $dir)->paginate($pagination);
        $data = TableResource::collection($table);
        $data->appends($appends)->links();

        return $data->additional([
            'status' => true,
            'table' => $request->table,
            // 'message' => 'Table ' . $request->table . ' Data.',
            ]);
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
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
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
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
