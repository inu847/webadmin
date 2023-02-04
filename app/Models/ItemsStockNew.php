<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Http\Requests;

use App\Helper\myFunction;

use Image;
use Input;
use File;
use Auth;

class ItemsStockNew extends Model
{
    protected $table = 'items_stock_new';

    /**
     * The attributes that are protected from mass assignable, others are mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id'];

    public static function save_data($request){
    	try {
    	    DB::transaction(function () use ($request) {
    	    	$data=$request->all();
    	    	$varadd=new ItemsStockNew;
                $varadd->id=myFunction::id('items_stock_new','id');
                $varadd->user_id=Auth::user()->id;
                $varadd->catalog=$data['catalog'];
                $varadd->item_id=$data['item_id'];
                $varadd->stock=$data['stock'];
                $varadd->notes=isset($data['notes']) ? $data['notes'] : null;
                $varadd->save();
    	    });
    	 }
    	catch(\Exception $e) {
            // dd($e);
    	    return false;
    	}
    	return true;
    }
    public static function update_data($request){
        try {
            DB::transaction(function () use ($request) {
                $data=$request->all();
                $array=[
                    // 'catalog'=>$data['catalog'],
                    'user_id'=>Auth::user()->id,
                    'stock'=>$data['stock'],
                    // 'item_id'=>$data['item_id'],
                    'notes'=>isset($data['notes']) ? $data['notes'] : null
                ];
                ItemsStockNew::where('id',$data['id'])->update($array);
            });
         }
        catch(\Exception $e) {
            return false;
        }
        return true;
    }
    public static function delete_data($addon){
    	try {
    	    DB::transaction(function () use ($addon) {
    	    	ItemsStockNew::where('id',$addon)->delete();
    	    });
    	 }
    	catch(\Exception $e) {
    	    return false;
    	}
    	return true;
    }
}
