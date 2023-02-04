<?php

namespace App\Http\Controllers;

use App\Models\Catalog;
use App\Models\Table;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Session;

class TableController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data['titlepage']='Table';
        $data['maintitle']='Manage Table';
        if (Session::get('catalogsession') == 'All') {
            $catalogs = Catalog::where('user_id',Auth::user()->id)->pluck('id');
            $tables = Table::whereIn('catalog_id', $catalogs)->orderBy('id', 'desc')->get();
        }else{
            $tables = Table::where('catalog_id', Session::get('catalogsession'))->orderBy('id', 'desc')->get();
        }

        return view('pages.table.index', $data, ['tables' => $tables]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $data['titlepage']='Create Table';
        $data['maintitle']='Create Table';
        $catalogs = Catalog::where('user_id',Auth::user()->id)->orderBy('id', 'desc')->get();
        return view('pages.table.create', $data, ['catalogs' => $catalogs]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'catalog_id' => 'required',
            'table' => 'required',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->with('failed', $validator->messages()->first());
        }

        $data = $request->all();
        $data['code'] = 'SCE-'.now()->format('YmdHis').rand(10, 99);
        $insert = Table::create($data);

        return redirect()->route('table.index')->with('success', 'input data success!');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $data['titlepage']='Detail Table';
        $data['maintitle']='Detail Table';
        $detail = Table::findOrFail($id);
        $transactions = $detail->tableTransaction()->paginate(10);

        return view('pages.table.detail', $data, ['detail' => $detail, 'transactions' => $transactions]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $data['titlepage']='Edit Table';
        $data['maintitle']='Edit Table';
        $edit = Table::findOrFail($id);
        $catalogs = Catalog::where('user_id',Auth::user()->id)->orderBy('id', 'desc')->get();

        return view('pages.table.edit', $data, ['edit' => $edit, 'catalogs' => $catalogs]);
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
        $validator = Validator::make($request->all(), [
            'catalog_id' => 'required',
            'table' => 'required',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->with('failed', $validator->messages()->first());
        }

        $data = $request->all();
        $update = Table::findOrFail($id);
        $update->update($data);

        return redirect()->route('table.index')->with('success', 'edit data success!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $delete = Table::findOrFail($id);
        if($delete->delete()){
            $status='success';
            $message='Your request was successful.';
        }else{
            $status='error';
            $message='Oh snap! something went wrong.';
        }
        // $notif=['status'=>$status,'message'=>$message];
        // return response()->json($notif);

        return redirect()->route('table.index')->with($status, $message);
    }
}
