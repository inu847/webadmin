<?php

namespace App\Http\Controllers;

use App\FoodCourt;
use App\Http\Controllers\Controller;
use App\Models\Catalog;
use App\Models\RequestCatalog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class RequestCatalogController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data['titlepage']='Approval Foodcourt';
        $data['maintitle']='List Approval Foodcourt';
        // $foodcourts = RequestCatalog::orderBy('id', 'desc')->get();
        $foodcourts = FoodCourt::where('user_id',Auth::user()->id)->orderBy('name')->get();

        return view('pages.foodcourtApproval.index', $data, ['foodcourts' => $foodcourts]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $data['titlepage']='Create Approval Foodcourt';
        $data['maintitle']='Create Approval Foodcourt';
        $foodcourts = FoodCourt::get();
        // $edit = RequestCatalog::findOrFail($id);
        // $foodcourts = RequestCatalog::where('foodcourt_id', $edit->foodcourt_id)->get();
        // $datas = array();
        // foreach ($foodcourts as $key => $foodcourt) {
        //     $datas[$key] = $foodcourt->catalog->id;
        // }
        $catalogs = Catalog::orderBy('id', 'desc')->get();

        return view('pages.foodcourtApproval.create', $data, ['catalogs' => $catalogs, 'foodcourts' => $foodcourts]);
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
            'foodcourt_id' => 'required',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->with('failed', $validator->messages()->first());
        }

        $data = $request->all();
        unset($data['_token']); 
        $data['foodcourt_id'] = $request->foodcourt_id;
        $data['user_id'] = Auth::user()->id;
        $data['status'] = 0;
        $insert = RequestCatalog::create($data);

        return redirect()->route('request-catalog.index')->with('success', 'input data success!');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $data['titlepage']='Detail Approval Foodcourt';
        $data['maintitle']='Detail Approval Foodcourt';
        $detail = RequestCatalog::findOrFail($id);

        return view('pages.foodcourtApproval.detail', $data, ['detail' => $detail]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $data['titlepage']='Edit Approval Foodcourt';
        $data['maintitle']='Edit Approval Foodcourt';
        $edit = FoodCourt::findOrFail($id);
        $edit->status = 1;
        $edit->save();

        return response()->json([
            'status' => 'success',
            'message' => 'edit data success!'
        ]);

        // return view('pages.foodcourtApproval.edit', $data, ['edit' => $edit]);
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
        $data = $request->all();
        $update = RequestCatalog::findOrFail($id);
        $update->update($data);

        return redirect()->route('request-catalog.index')->with('success', 'edit data success!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        // $delete = RequestCatalog::findOrFail($id);
        // $delete->delete();

        $edit = FoodCourt::findOrFail($id);
        $edit->status = 2;
        $edit->save();

        return response()->json([
            'status' => 'success',
            'message' => 'edit data success!'
        ]);

        return redirect()->route('request-catalog.index')->with('success', 'delete data success!');
    }
}
