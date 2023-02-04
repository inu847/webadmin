<?php

namespace App\Http\Controllers;

use App\Models\MetodePembayaran;
use App\Models\MetodePembayaranGroup;
use Illuminate\Http\Request;

class MetodePembayaranController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data['titlepage']='Metode Pembayaran Group';
        $data['maintitle']='List Metode Pembayaran Group';
        $metodepembayaran = MetodePembayaranGroup::orderBy('id', 'desc')->paginate(10);

        return view('pages.metodePembayaranGroup.index', $data, ['metodepembayaran' => $metodepembayaran]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $data['titlepage']='Create Metode Pembayaran Group';
        $data['maintitle']='Create Metode Pembayaran Group';

        return view('pages.metodePembayaranGroup.create', $data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {
            $data = $request->all();
            unset($data['_token']);
            $image = $request->file('image');
            if ($image) {
                $file = $image->store('metode_pembayaran', 'public');
                $data['image'] = $file;
            }
            $insert = MetodePembayaranGroup::create($data);
    
            return redirect()->route('metode-pembayaran.index')->with('success', 'input data success!');
        } catch (\Throwable $th) {
            $insert = MetodePembayaranGroup::create($data);
    
            return redirect()->route('metode-pembayaran.index')->with('danger', 'failed input data!');
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $data['titlepage']='Detail Metode Pembayaran Group';
        $data['maintitle']='Detail Metode Pembayaran Group';
        $detail = MetodePembayaranGroup::findOrFail($id);
        $metodepembayaran = MetodePembayaran::where('metode_pembayaran_group_id', $id)->orderBy('id', 'asc')->get();

        return view('pages.metodePembayaranGroup.detail', $data, ['detail' => $detail, 'metodepembayaran' => $metodepembayaran]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $data['titlepage']='Edit Metode Pembayaran Group';
        $data['maintitle']='Edit Metode Pembayaran Group';
        $edit = MetodePembayaranGroup::findOrFail($id);

        return view('pages.metodePembayaranGroup.edit', $data, ['edit' => $edit]);
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
        try {
            $data = $request->all();
            $image = $request->file('image');
            if ($image) {
                $file = $image->store('metode_pembayaran', 'public');
                $data['image'] = $file;
            }
            $update_data = MetodePembayaranGroup::findOrFail($id)->update($data);
    
            return redirect()->route('metode-pembayaran.index')->with('success', 'edit data success!');
        } catch (\Throwable $th) {
            $update_data = MetodePembayaranGroup::findOrFail($id)->update($request->all());
    
            return redirect()->route('metode-pembayaran.index')->with('danger', 'failed edit data!');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $delete = MetodePembayaranGroup::findOrFail($id);
        if($delete->delete()){
            $status='success';
            $message='Your request was successful.';
        }else{
            $status='error';
            $message='Oh snap! something went wrong.';
        }
        $notif=['status'=>$status,'message'=>$message];
        return response()->json($notif);

        // return redirect()->route('metode-pembayaran.index')->with('success', 'delete data success!');
    }

    public function createMetodePembayaran($id)
    {
        $data['titlepage']='Metode Pembayaran';
        $data['maintitle']='List Metode Pembayaran';
        $data['id'] = $id;

        return view('pages.metodePembayaranGroup.createMp', $data);
    }

    public function storeMetodePembayaran(Request $request, $id)
    {
        $data = $request->all();
        $data['metode_pembayaran_group_id'] = $id;
        $create = MetodePembayaran::create($data);

        return redirect()->back()->with('success', 'input data success!');
    }

    public function editMetodePembayaran($id)
    {
        $edit = MetodePembayaran::findOrFail($id);
        
        return view('pages.metodePembayaranGroup.editMp', ['edit' => $edit]);
    }

    public function updateMetodePembayaran(Request $request, $id)
    {
        try {
            $update_data = MetodePembayaran::findOrFail($id)->update($request->all());
    
            return redirect()->route('metode-pembayaran.index')->with('success', 'edit data success!');
        } catch (\Throwable $th) {
            $update_data = MetodePembayaran::findOrFail($id)->update($request->all());
    
            return redirect()->back()->with('danger', 'failed edit data!');
        }
    }

    public function destroyMetodePembayaran($id)
    {
        $delete = MetodePembayaran::findOrFail($id);

        if($delete->delete()){
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
