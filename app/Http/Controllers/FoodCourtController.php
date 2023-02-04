<?php

namespace App\Http\Controllers;

use App\FoodCourt;
use App\FoodCourtCatalog;
use App\Http\Controllers\Controller;
use App\Models\Catalog;
use App\Models\Invoice;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FoodCourtController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data['titlepage'] = 'Foodcourt';
        $data['maintitle'] = 'List Foodcourt';
        $foodcourts = FoodCourt::where('user_id', Auth::user()->id)->orderBy('id', 'desc')->paginate(10);

        return view('pages.foodcourt.index', $data, ['foodcourts' => $foodcourts]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $data['titlepage'] = 'Create Foodcourt';
        $data['maintitle'] = 'Create Foodcourt';
        $catalogs = Catalog::orderBy('id', 'desc')->get();
        $users = User::orderBy('id', 'desc')->get();

        return view('pages.foodcourt.create', $data, ['catalogs' => $catalogs, 'users' => $users]);
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
            $data['user_id'] = Auth::user()->id;
            $insert = FoodCourt::create($data);

            return redirect()->route('foodcourt.index')->with('success', 'input data success!');
        } catch (\Throwable $th) {
            $insert = FoodCourt::create($data);

            return redirect()->route('foodcourt.index')->with('danger', 'failed input data!');
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
        $data['titlepage'] = 'Detail Foodcourt';
        $data['maintitle'] = 'Detail Foodcourt';
        $detail = FoodCourt::findOrFail($id);
        $foodcourtsCatalog = FoodCourtCatalog::where('food_court_id', $id)->get();

        return view('pages.foodcourt.detail', $data, ['detail' => $detail, 'foodcourtsCatalog' => $foodcourtsCatalog]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $data['titlepage'] = 'Edit Foodcourt';
        $data['maintitle'] = 'Edit Foodcourt';
        $edit = FoodCourt::findOrFail($id);
        $catalogs = Catalog::orderBy('id', 'desc')->get();
        $users = User::orderBy('id', 'desc')->get();

        return view('pages.foodcourt.edit', $data, ['edit' => $edit, 'catalogs' => $catalogs, 'users' => $users]);
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
            $update_data = FoodCourt::findOrFail($id)->update($request->all());

            return redirect()->route('foodcourt.index')->with('success', 'edit data success!');
        } catch (\Throwable $th) {
            $update_data = FoodCourt::findOrFail($id)->update($request->all());

            return redirect()->route('foodcourt.index')->with('danger', 'failed edit data!');
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
        $delete = FoodCourt::findOrFail($id);
        if ($delete->delete()) {
            $status = 'success';
            $message = 'Your request was successful.';
        } else {
            $status = 'error';
            $message = 'Oh snap! something went wrong.';
        }
        $notif = ['status' => $status, 'message' => $message];
        return response()->json($notif);

        // return redirect()->route('foodcourt.index')->with('success', 'delete data success!');
    }

    public function createFoodcourtCatalog($id)
    {
        $data['titlepage'] = 'Foodcourt';
        $data['maintitle'] = 'List Foodcourt';
        $foodcourts = FoodCourtCatalog::where('food_court_id', $id)->get();
        $datas = array();
        foreach ($foodcourts as $key => $foodcourt) {
            $datas[$key] = $foodcourt->catalog->id;
        }

        $catalogs = Catalog::whereNotIn('id', $datas)->orderBy('id', 'desc')->get();

        return view('pages.foodcourtCatalog.create', $data, ['id' => $id, 'foodcourts' => $foodcourts, 'catalogs' => $catalogs]);
    }

    public function checkCatalog(Request $request)
    {
        $catalog = Catalog::where('catalog_key', $request->catalog_key)->first();

        if ($catalog) {
            $status = true;
            $message = 'Your request was successful.';
        } else {
            $status = false;
            $message = 'Oh snap! something went wrong.';
        }
        $notif = ['status' => $status, 'message' => $message, 'catalog' => $catalog];
        return response()->json($notif);
    }

    public function storeFoodcourtCatalog(Request $request, $id)
    {
        $data = $request->all();
        $data['food_court_id'] = $id;
        $create = FoodCourtCatalog::create($data);

        return redirect()->back()->with('success', 'input data success!');
    }

    public function editFoodcourtCatalog($id)
    {
        $edit = FoodCourtCatalog::findOrFail($id);
        // $foodcourts = FoodCourtCatalog::where('food_court_id', $edit->food_court_id)->get();
        // $datas = array();
        // foreach ($foodcourts as $key => $foodcourt) {
        //     $datas[$key] = $foodcourt->catalog->id;
        // }
        // $catalogs = Catalog::whereNotIn('id', $datas)->orderBy('id', 'desc')->get();
        $catalogs = Catalog::orderBy('id', 'desc')->get();

        return view('pages.foodcourtCatalog.edit', ['edit' => $edit, 'catalogs' => $catalogs]);
    }

    public function updateFoodcourtCatalog(Request $request, $id)
    {
        try {
            $update_data = FoodCourtCatalog::findOrFail($id)->update($request->all());

            return redirect()->route('foodcourt.index')->with('success', 'edit data success!');
        } catch (\Throwable $th) {
            $update_data = FoodCourtCatalog::findOrFail($id)->update($request->all());

            return redirect()->back()->with('danger', 'failed edit data!');
        }
    }

    public function destroyFoodcourtCatalog($id)
    {
        $delete = FoodCourtCatalog::findOrFail($id);

        if ($delete->delete()) {
            $status = 'success';
            $message = 'Your request was successful.';
        } else {
            $status = 'error';
            $message = 'Oh snap! something went wrong.';
        }
        $notif = ['status' => $status, 'message' => $message];
        return response()->json($notif);
    }

    public function monitoringFoodcourt(Request $request, $id)
    {
        $catalogs = FoodCourtCatalog::where('food_court_id', $id)->get();

        $invoice = Invoice::select(['created_at', 'catalog_id', 'amount', 'payment_method'])->selectRaw('SUM(amount) as amount')->whereIn('catalog_id', $catalogs->pluck('catalog_id')->toArray());

        if (request()->searchMonth && request()->searchYear) {
            if (request()->searchMonth != 'all') {
                $invoice->whereMonth('created_at', request()->searchMonth);
            }
            $invoice->whereYear('created_at', request()->searchYear);
        }

        $data['invoice'] = $invoice->groupBy('catalog_id')->get();

        // dd($catalogs);
        
        foreach ($catalogs as $val) {
            $exsited_catalog = null;
            if($data['invoice'] && count($data['invoice'])){
                foreach ($data['invoice'] as $key => $value) {
                    if ($value->catalog_id == $val->catalog_id) {
                        $exsited_catalog = $value->catalog_id;

                        $where = [
                            'catalog_id' => $value->catalog_id,
                        ];

                        $transaksiTunai = Invoice::where($where)->where('payment_method', 1);
                        if (request()->searchMonth && request()->searchYear) {
                            if (request()->searchMonth != 'all') {
                                $transaksiTunai->whereMonth('created_at', request()->searchMonth);
                            }
                            $transaksiTunai->whereYear('created_at', request()->searchYear);
                        }
                        $transaksiTunai = $transaksiTunai->sum('amount');
                        
                        $data['invoice'][$key]['transaksiTunai'] = $transaksiTunai;

                        $transaksiOnline = Invoice::where($where)->where(function ($query) {
                            $query->whereNull('payment_method', );
                            $query->orWhere('payment_method', '<>', 1);
                        });
                        if (request()->searchMonth && request()->searchYear) {
                            $transaksiOnline->whereMonth('created_at', request()->searchMonth);
                            $transaksiOnline->whereYear('created_at', request()->searchYear);
                        }
                        $transaksiOnline = $transaksiOnline->sum('amount');

                        $data['invoice'][$key]['transaksiOnline'] = $transaksiOnline;
                    }
                }
            }
            else{
                // if (is_null($exsited_catalog) || !$exsited_catalog){
                    $object = new \stdClass();
                    $object->catalog_id = $val->catalog_id;
                    $object->amount = 0;
                    $object->payment_method = null;
                    $object->transaksiTunai = 0;
                    $object->transaksiOnline = 0;

                    $catalog_data = new \stdClass();
                    $catalog_data->catalog_title = optional($val->catalog)->catalog_title;
                    $catalog_data->email_contact = optional($val->catalog)->email_contact;
                    $object->catalog = $catalog_data;

                    $data['invoice'][] = $object;
                // }
            }
        }

        $data['searchMonth'] = request()->searchMonth ? request()->searchMonth : 'all';
        $data['searchYear'] = request()->searchYear ? request()->searchYear : '';

        return view('pages.monitoringMerchant.table_monitor', $data);
    }
}
