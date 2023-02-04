<?php

namespace App\Http\Controllers\Master;

use App\Exports\ExportCustomer;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Customer;
use App\Models\Invoice;
use App\Models\InvoiceDetail;
use Auth;
use getData;
use Maatwebsite\Excel\Exporter;
use myFunction;
use Maatwebsite\Excel\Facades\Excel;

class CustomerController extends Controller
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
        $data['titlepage']='Customer Data';
        $data['maintitle']='Customer Data';
        return view('pages.master.customer.data',$data);
    }
    public function getData(Request $request){
        $columns = ['customer_name','customer_email','customer_phone'];
        $keyword = trim($request->input('searchfield'));
        $query = Customer::where('catalog_id',getData::getCatalogSession('id'))
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
        return view('pages.master.customer.table',$data);
    }
    public function show($id)
    {
        $query=Customer::where('id',$id)->first();
        return $query;
    }

    public function export()
    {
        // $data = Customer::where('catalog_id',getData::getCatalogSession('id'))
        //                     ->orderBy('id','desc')
        //                     ->get();

        // return view('pages.master.customer.export', ['data' => $data]);
        return Excel::download(new ExportCustomer(), 'customer.xlsx');
    }

    public function detailInvoice(Request $request)
    {
        $invoice = Invoice::where('catalog_id',getData::getCatalogSession('id'))
        ->where('phone', "0".$request->phone)
        ->get();

        // $invoice['pembelian'] = 0;
        // $invoice['total_belanja'] = 0;

        // foreach ($invoice as $key => $value) {
        //     $invoice['total_belanja'] += $value->amount;
        //     foreach ($value->invoiceDetail as $ckey => $cvalue) {
        //         $invoice['pembelian'] += $cvalue->qty;
        //     }
        // }

        return response()->json($invoice);
    }
}
