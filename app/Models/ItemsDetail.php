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

class ItemsDetail extends Model
{
    protected $table = 'items_detail';

    public static function save_data($request){
    	try {
    	    DB::transaction(function () use ($request) {
    	    	$data=$request->all();
    	    	
    	    	if(!empty($data['addons'])){
    	    	    foreach(explode(',',$data['addons']) as $addons){
    	    	        $checkitem=ItemsDetail::where('item_id',$data['item_id'])
    	    	        					->where('category_id',$data['category_id'])
    	    	        					->where('addon',$addons)
    	    	        					->count();
    	    	        if($checkitem < 1){
    	    	            $varadd=new ItemsDetail;
    	    	            $varadd->id=myFunction::id('items_detail','id');
    	    	            $varadd->user_id=Auth::user()->id;
    	    	            $varadd->item_id=$data['item_id'];
    	    	            $varadd->category_id=$data['category_id'];
    	    	            $varadd->addon=$addons;
    	    	            $varadd->check_type=$data['check_type'];
    	    	            $varadd->save();
    	    	        }
    	    	    }
    	    	}

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
    	    	ItemsDetail::where('id',$addon)->delete();
    	    });
    	 }
    	catch(\Exception $e) {
    	    return false;
    	}
    	return true;
    }
}
