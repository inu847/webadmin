<?php

namespace App\Http\Controllers;

use App\Models\Pengeluaran;
use App\Models\PengeluaranDetail;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use App\Models\Catalog;
use Session;

class PengeluaranController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data['titlepage']='Pengeluaran';
        $data['maintitle']='Manage Pengeluaran';

		if (Session::get('catalogsession') == 'All') {
            $catalogs = Catalog::where('user_id',Auth::user()->id)->pluck('id');
            $pengeluarans = Pengeluaran::whereIn('catalog_id', $catalogs)->orderBy('id', 'desc');
        }else{
            $pengeluarans = Pengeluaran::where('catalog_id', Session::get('catalogsession'))->orderBy('id', 'desc');
        }

        $columns = ['judul'];
        $keyword = trim(request()->searchfield);

        $pengeluarans->where(function($result) use ($keyword,$columns) {
            foreach($columns as $column)
            {
                if($keyword != ''){
                    $result->orWhere($column,'LIKE','%'.$keyword.'%');
                }
            }
        });

        if(request()->searchMonth && request()->searchYear){
            if(request()->searchMonth != 'all'){
                $pengeluarans->whereMonth('datetime', request()->searchMonth);
            }
            $pengeluarans->whereYear('datetime', request()->searchYear);
        }
        else{
            $pengeluarans->whereMonth('datetime', date('m'));
            $pengeluarans->whereYear('datetime', date('Y'));
        }
    
        $data['pengeluarans'] = $pengeluarans->get();
        $data['searchMonth'] = request()->searchMonth ? request()->searchMonth : date('m');
        $data['searchYear'] = request()->searchYear ? request()->searchYear : date('Y');
        $data['searchfield'] = request()->searchfield ? request()->searchfield : '';
    
        return view('pages.pengeluaran.index', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $data['titlepage']='Create Pengeluaran';
        $data['maintitle']='Create Pengeluaran';
        $catalogs = Catalog::where('user_id',Auth::user()->id)->orderBy('id', 'desc')->get();
        return view('pages.pengeluaran.create', $data, ['catalogs' => $catalogs]);
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
            'judul' => 'required',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->with('failed', $validator->messages()->first());
        }

        $data = $request->except(['_token']);
        $data['user_id'] = Auth::user()->id;
        $insert = Pengeluaran::create($data);

        return redirect()->route('pengeluaran.show', $insert->id)->with('success', 'input data success!');
    }

    public function save_detail(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'harga' => 'required',
            'qty' => 'required',
            'nama' => 'required',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->with('failed', $validator->messages()->first());
        }

        $data = $request->except(['_token']);
        $data['pengeluaran_id'] = $id;
        $insert = PengeluaranDetail::create($data);

        return response()->json([
            'status' => "success",
            'message' => ''
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $data['titlepage']='Detail Pengeluaran';
        $data['maintitle']='Detail Pengeluaran';
        $detail = Pengeluaran::findOrFail($id);
        $transactions = $detail->detail()->get();

        return view('pages.pengeluaran.detail', $data, ['detail' => $detail, 'transactions' => $transactions]);
    }

    public function detail($id)
    {
        $detail = Pengeluaran::findOrFail($id);
        $transactions = $detail->detail()->get();
        $return = '';
        $total = 0;

        foreach ($transactions as $key => $value){
            $return .= '<tr>
                <td>'. ($key+1) .'</td>
                <td>'. $value->nama .'</td>
                <td>'. $value->keterangan .'</td>
                <td>'. number_format($value->harga, 0, ',', '.') .'</td>
                <td>'. number_format($value->qty, 0, ',', '.') .'</td>
                <td>'. number_format($value->harga * $value->qty, 0, ',', '.') .'</td>
                <td><button type="button" class="btn btn-danger" onClick="remove_list('. $value->id .')"><i class="fa fa-trash"></i></button></td>
            </tr>';

            // <a href="javascript:void(0)" data-id="{{ $value['id'] }}" class="deletelink btn btn-shadow btn-danger">Delete</a>

            $total += ($value->harga * $value->qty);
        }

        return response()->json([
            'data' => $return,
            'total' => number_format($total, 0, ',', '.')
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $data['titlepage']='Edit Pengeluaran';
        $data['maintitle']='Edit Pengeluaran';
        $edit = Pengeluaran::findOrFail($id);
        $catalogs = Catalog::where('user_id',Auth::user()->id)->orderBy('id', 'desc')->get();

        return view('pages.pengeluaran.edit', $data, ['edit' => $edit, 'catalogs' => $catalogs]);
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
            'judul' => 'required',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->with('failed', $validator->messages()->first());
        }

        $data = $request->all();
        $update = Pengeluaran::findOrFail($id);
        $update->update($data);

        return redirect()->route('pengeluaran.index')->with('success', 'edit data success!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $delete = Pengeluaran::findOrFail($id);
        if($delete->delete()){
            $status='success';
            $message='Your request was successful.';
        }else{
            $status='error';
            $message='Oh snap! something went wrong.';
        }
        // $notif=['status'=>$status,'message'=>$message];
        // return response()->json($notif);

        return redirect()->route('pengeluaran.index')->with($status, $message);
    }

    public function delete_detail($id)
    {
        $delete = PengeluaranDetail::findOrFail($id);
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
