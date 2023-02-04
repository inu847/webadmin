<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class TableResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        // multiple
        return $this->resource;

        // single data
        // return parent::toArray($request); 
        
        // multiple
        // return [
        //     'kode'=> $this->kode,
        //     'date'=> $this->transaction_date,
        //     'coupon_percent'=> $this->coupon_percent ?? 0,
        //     'coupon_nominal'=> $this->coupon_nominal ?? 0,
        //     'sub_total'=> $this->sub_total ?? 0,
        //     'total'=> $this->total ?? 0,
        //     'total_profit' => $this->total_profit ?? 0,
        //     'payment_method_name'=> $this->payment_method_name,
        //     'owner'=> $this->owner,
        //     'account_number'=> $this->account_number,
        //     'status_id'=> $this->status,
        //     'status' => optional($this->status_info)->name,
        //     'items'=> TransactionItemResource::collection($this->items()->orderBy('name')->get()),
        // ];

    }
}
