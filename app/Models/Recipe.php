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

class Recipe extends Model
{
    protected $table = 'recipe';

    public static function save_data($request){
    	try {
    	    DB::transaction(function () use ($request) {
    	    	$data=$request->all();
    	    	$varadd=new Recipe;
                $varadd->id=myFunction::id('recipe','id');
                $varadd->user_id=Auth::user()->id;
                $varadd->parent_id=$data['parent_id'];
                $varadd->item_id=$data['item_id'] ?? null;
                $varadd->ingredient_id=$data['ingredient_id'];
                $varadd->serving_size=$data['serving_size'];
                $varadd->save();
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
                if($request->has('ingredient_id')){
                  $array=['user_id'=>Auth::user()->id,'ingredient_id'=>$data['ingredient_id'],'serving_size'=>$data['serving_size']];
                }else{
                  $array=['user_id'=>Auth::user()->id,'item_id'=>$data['item_id'],'serving_size'=>$data['serving_size']];
                }

                Recipe::where('id',$data['id'])->update($array);
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
    	    	Recipe::where('id',$addon)->delete();
    	    });
    	 }
    	catch(\Exception $e) {
    	    return false;
    	}
    	return true;
    }

    public function ingredient()
    {
        return $this->belongsTo(Ingredient::class, 'ingredient_id', 'id');
    }
}
