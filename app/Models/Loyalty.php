<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Input;
use File;
use Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Http\Requests;
use App\Helper\myFunction;
use Illuminate\Support\Facades\Storage;

class Loyalty extends Model
{
    public static function save_data($request){
        try {
            DB::transaction(function () use ($request) {
                $id = myFunction::id('loyalties','id');
                $data=$request->all();

                $var=new Loyalty;
                $var->id=$id;
                $var->name=trim($data['name']);
                $var->description=trim($data['description']);
                $var->min_order=trim($data['min_order']);
                $var->max_order=trim($data['max_order']);
                $var->save();

                $subpath = 'users/'.Auth::user()->id.'/loyalties';

                if($request->hasFile('photo')){
                    $file = $request->file('photo');
                    $filename=Str::slug(trim($data['name']),"_").'_'.$id.'_1';
                    $extension = $file->extension();
                    $filenametostore = $filename.'.'.$extension;
                    Storage::disk('s3')->putFileAs($subpath, $file, $filenametostore);
                    $url = Storage::disk('s3')->url($subpath.'/'.$filenametostore);
                    $img_insert['photo'] = $url;
                    Loyalty::where('id',$id)->update($img_insert);
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
                $array=[
                        'name'=>trim($data['name']),
                        'description'=>trim($data['description']),
                        'min_order'=>trim($data['min_order']),
                        'max_order'=>trim($data['max_order'])];

                $subpath = 'users/'.Auth::user()->id.'/loyalties';

                if($request->hasFile('photo')){
                    $file = $request->file('photo');
                    $filename=Str::slug(trim($data['name']),"_").'_'.$id.'_1';
                    $extension = $file->extension();
                    $filenametostore = $filename.'.'.$extension;
                    Storage::disk('s3')->putFileAs($subpath, $file, $filenametostore);
                    $url = Storage::disk('s3')->url($subpath.'/'.$filenametostore);
                    $array['photo'] = $url;
                }

                Loyalty::where('id',$data['id'])->update($array);
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
                Loyalty::where('id',$id)->delete();
            });
         }
        catch(\Exception $e) {
            return false;
        }
        return true;
    }
}
