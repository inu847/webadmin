<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Http\Requests;

use App\Helper\myFunction;

use Input;
use File;
use Auth;

class MainFeatures extends Model
{
    protected $table = 'mainfeatures';

    public static function save_data($request){
        try {
            DB::transaction(function () use ($request) {
                $id = myFunction::id('mainfeatures','id');
                $data=$request->all();

                $var=new MainFeatures;
                $var->id=$id;
                $var->user_id=Auth::user()->id;
                $var->feature_name=trim($data['feature_name']);
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
                $array=['feature_name'=>$data['feature_name']];
                MainFeatures::where('id',$data['id'])->update($array);
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
                MainFeatures::where('id',$id)->delete();
            });
         }
        catch(\Exception $e) {
            return false;
        }
        return true;
    }
}
