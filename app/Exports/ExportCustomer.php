<?php

namespace App\Exports;

use App\Helper\getData;
use App\Models\Customer;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\FromView;
use Illuminate\Contracts\View\View;

class ExportCustomer implements FromView
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function view(): View
    {
        $data = Customer::where('catalog_id', getData::getCatalogSession('id'))
                        ->orderBy('id','desc')
                        ->get();

        return view('pages.master.customer.export', ['data' => $data]);
    }
}
