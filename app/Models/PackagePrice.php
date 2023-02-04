<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Http\Requests;

use myFunction;

use Input;
use File;
use Auth;

class PackagePrice extends Model
{
    protected $table = 'package_price';

    public static function save_data($request){
        try {
            DB::transaction(function () use ($request) {
                $data=$request->all();

                $var=new PackagePrice;
                $var->id=myFunction::id('package_price','id');
                $var->user_id=Auth::user()->id;
                $var->package_id=trim($data['package_id']);
                $var->price=trim($data['price']);
                $var->period=$data['period'];
                $var->unit=$data['unit'];
                $var->notes=$data['notes'];
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
                PackagePrice::where('id',$data['id'])
                			->update([
                				'user_id'=>Auth::user()->id,
                				'package_id'=>trim($data['package_id']),
                				'price'=>trim($data['price']),
                				'period'=>trim($data['period']),
                				'unit'=>trim($data['unit']),
                				'notes'=>trim($data['notes']),
            				]);
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
                PackagePrice::where('id',$id)->delete();
            });
         }
        catch(\Exception $e) {
            return false;
        }
        return true;
    }
}
