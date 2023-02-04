<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Http\Requests;
use Illuminate\Support\Facades\Storage;

use App\Helper\myFunction;

use Image;
use Input;
use File;
use Auth;

class Sliders extends Model
{
    protected $table = 'sliders';

    public static function save_data($request){
    	try {
    	    DB::transaction(function () use ($request) {
    	        $id = myFunction::id('sliders','id');
                $data=$request->all();
                $var=new Sliders;
                $var->id=$id;
                $var->user_id=Auth::user()->id;
                $var->sliders_title=trim($data['sliders_title']);
                $var->save();

                if($request->hasFile('imagefile')){
                    // $mainpath = myFunction::pathAsset();
                    $subpath = 'users/'.Auth::user()->id.'/sliders';
                    // $path = $mainpath.$subpath;

                    // File::isDirectory($path) or File::makeDirectory($path, 0777, true, true);

                    // $imgfile = Image::make($_FILES['imagefile']['tmp_name']);
                    // $imgfile->fit(1920, 1280);

                    // $name=Str::slug(trim($data['sliders_title']),"_").'_'.time();
                    // $extension = $request->file('imagefile')->getClientOriginalExtension();
                    // $imgfile->save($path.'/'.$name.'.'.$extension);
                    // $images=$subpath.'/'.$name.'.'.$extension;

                    // $array=['sliders_image'=>$images];
                    // Sliders::where('id',$id)->update($array);

                    $file = $request->file('imagefile');
                    $filename=Str::slug(trim($data['sliders_title']),"_").'_'.time();
                    $extension = $file->extension();
                    $filenametostore = $filename.'.'.$extension;

                    $resizeImage  = Image::make($file)->resize(1920, 1280, function ($constraint) {
                        $constraint->aspectRatio();
                        $constraint->upsize();
                    })->stream();
                    Storage::disk('s3')->put($subpath.'/'.$filenametostore, $resizeImage->__toString());
                    $url = Storage::disk('s3')->url($subpath.'/'.$filenametostore);
                    $img_insert['sliders_image'] = $url;
                    Sliders::where('id',$id)->update($img_insert);
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
                $query = Sliders::where('id',$data['id'])->first();

                // $mainpath = myFunction::pathAsset();
                $subpath = 'users/'.Auth::user()->id.'/sliders';
                // $path = $mainpath.$subpath;
                // File::isDirectory($path) or File::makeDirectory($path, 0777, true, true);

                if($request->hasFile('imagefile')){
                    // if(!empty($query['sliders_image']) and File::exists($path.'/'.basename(parse_url($query['sliders_image'])['path']))){
                    //     unlink($path.'/'.basename(parse_url($data['sliders_image'])['path']));
                    // }
                    
                    // $imgfile = Image::make($_FILES['imagefile']['tmp_name']);
                    // $imgfile->fit(1920, 1280);
                    // $name=Str::slug(trim($data['sliders_title']),"_").'_'.time();
                    // $extension = $request->file('imagefile')->getClientOriginalExtension();
                    // $imgfile->save($path.'/'.$name.'.'.$extension);
                    // $image=$subpath.'/'.$name.'.'.$extension;

                    $file = $request->file('imagefile');
                    $filename=Str::slug(trim($data['sliders_title']),"_").'_'.time();
                    $extension = $file->extension();
                    $filenametostore = $filename.'.'.$extension;

                    $resizeImage  = Image::make($file)->resize(1920, 1280, function ($constraint) {
                        $constraint->aspectRatio();
                        $constraint->upsize();
                    })->stream();
                    Storage::disk('s3')->put($subpath.'/'.$filenametostore, $resizeImage->__toString());
                    $image = Storage::disk('s3')->url($subpath.'/'.$filenametostore);
                }else{
                    if(!empty($data['sliders_image'])){
                        // $old = $path.'/'.basename(parse_url($data['sliders_image'])['path']);
                        // $name=Str::slug(trim($data['sliders_title']),"_").'_'.time();
                        // $ext=explode(".",basename(parse_url($data['sliders_image'])['path']));
                        // $new = $path.'/'.$name.'.'.$ext[1];
                        // rename($old, $new);
                        // $image=$subpath.'/'.$name.'.'.$ext[1];
                        if(strpos($data['sliders_image'], 'amazonaws.com') !== false){
                            $image=$data['sliders_image'];
                        }
                        else{
                            $image='';
                        }
                    }else{
                        $image=$data['sliders_image'];
                    }
                }

                $array=['sliders_title'=>$data['sliders_title'],
                    	'sliders_image'=>$image];
                Sliders::where('id',$data['id'])->update($array);
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
            	$query = Sliders::where('id',$id)->first();

            	$mainpath = myFunction::pathAsset();
            	$subpath = '/users/'.Auth::user()->id.'/sliders';
            	$path = $mainpath.$subpath;

            	if(!empty($query['sliders_image']) and File::exists($path.'/'.basename(parse_url($query['sliders_image'])['path']))){
            	    unlink($path.'/'.basename(parse_url($query['sliders_image'])['path']));
            	}

                try {
                    Storage::disk('s3')->delete(str_replace('https://scaneat.s3.ap-southeast-1.amazonaws.com/', '', $query['sliders_image']));
                } catch (\Exception $e) {
                    //throw $th;
                }

                Sliders::where('id',$id)->delete();
            });
         }
        catch(\Exception $e) {
            return false;
        }
        return true;
    }
}
