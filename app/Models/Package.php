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

class Package extends Model
{
    protected $table = 'package';

    public static function save_data($request){
        try {
            DB::transaction(function () use ($request) {
                $id = myFunction::id('package','id');
                $data=$request->all();

                if($data['recommended'] == 'Y'){
                    Package::where('recommended','Y')->update(['recommended'=>'N']);
                }

                $var=new Package;
                $var->id=$id;
                $var->user_id=Auth::user()->id;
                $var->package_name=trim($data['package_name']);
                $var->package_slug=Str::slug(trim($data['package_name']),"-");
                $var->recommended=$data['recommended'];
                $var->description=$data['description'];
                $var->save();

                foreach(explode(',', $data['features']) as $vfeature){
                    $feature=new Feature;
                    $feature->id=myFunction::id('feature','id');
                    $feature->user_id=Auth::user()->id;
                    $feature->mainfeature_id=$vfeature;
                    $feature->package_id=$id;
                    $feature->save();
                }

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
                if($data['recommended'] == 'Y'){
                    Package::where('recommended','Y')->update(['recommended'=>'N']);
                }
                $array=['package_name'=>$data['package_name'],
                        'package_slug'=>Str::slug(trim($data['package_name']),"-"),
                        'recommended'=>$data['recommended'],
                        'description'=>$data['description']];
                Package::where('id',$data['id'])->update($array);

                Feature::where('package_id',$data['id'])->delete();
                foreach(explode(',', $data['features']) as $vfeature){
                    $feature=new Feature;
                    $feature->id=myFunction::id('feature','id');
                    $feature->user_id=Auth::user()->id;
                    $feature->mainfeature_id=$vfeature;
                    $feature->package_id=$data['id'];
                    $feature->save();
                }

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
                Package::where('id',$id)->delete();
            });
         }
        catch(\Exception $e) {
            return false;
        }
        return true;
    }
}
