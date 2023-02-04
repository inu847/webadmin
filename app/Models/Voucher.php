<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Http\Requests;

use App\Helper\myFunction;
use App\Models\Feature;

use Input;
use File;
use Auth;

class Voucher extends Model
{
    protected $table = 'voucher';

    public static function save_data($request){
        try {
            DB::transaction(function () use ($request) {
                $id = myFunction::id('voucher','id');
                $data=$request->all();

                $var=new Voucher;
                $var->id=$id;
                $var->user_id=Auth::user()->id;
                $var->voucher_code=trim(strtoupper(str_replace('/', '', $data['voucher_code'])));
                $var->voucher_type=trim($data['voucher_type']);
                $var->voucher_nominal=trim($data['voucher_nominal']);
                $var->voucher_owner=trim($data['voucher_owner']);
                $var->save();
            });
         }
        catch(\Exception $e) {
            return false;
        }
        return true;
    }
    public static function update_data($request){
        try {
            DB::transaction(function () use ($request) {
                $data=$request->all();
                $array=['voucher_code'=>trim(strtoupper(str_replace('/', '', $data['voucher_code']))),
                        'voucher_type'=>trim($data['voucher_type']),
                        'voucher_nominal'=>trim($data['voucher_nominal']),
                        'voucher_owner'=>trim($data['voucher_owner'])];
                Voucher::where('id',$data['id'])->update($array);
            });
         }
        catch(\Exception $e) {
            return false;
        }
        return true;
    }
    public static function delete_data($id){
        try {
            DB::transaction(function () use ($id) {
                Voucher::where('id',$id)->delete();
            });
         }
        catch(\Exception $e) {
            return false;
        }
        return true;
    }
}
