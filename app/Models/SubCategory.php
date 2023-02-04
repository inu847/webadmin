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

class SubCategory extends Model
{
    protected $table = 'subcategory';

    public function catalog()
    {
        return $this->belongsToMany('App\Models\Catalog', 'catalog_sub_categories' ,'sub_category_id', 'catalog_id');
    }

    public static function save_data($request){
    	try {
    	    DB::transaction(function () use ($request) {
    	        $id = myFunction::id('subcategory','id');
                $data=$request->all();
                $var=new SubCategory;
                $var->id=$id;
                $var->user_id=Auth::user()->id;
                $var->subcategory_name=trim($data['subcategory_name']);
                $var->subcategory_slug=$id.'-'.Str::slug(trim($data['subcategory_name']),"-");
                $var->subcategory_color=$data['subcategory_color'];
                $var->save();

                if($request->hasFile('imagefile')){
                    // $mainpath = myFunction::pathAsset();
                    $subpath = 'users/'.Auth::user()->id.'/subcategory';
                    // $path = $mainpath.$subpath;

                    // File::isDirectory($path) or File::makeDirectory($path, 0777, true, true);

                    // $imgfile = Image::make($_FILES['imagefile']['tmp_name']);
                    // $imgfile->fit(350, 350);

                    // $name=Str::slug(trim($data['subcategory_name']),"_").'_'.time();
                    // $extension = $request->file('imagefile')->getClientOriginalExtension();
                    // $imgfile->save($path.'/'.$name.'.'.$extension);
                    // $images=$subpath.'/'.$name.'.'.$extension;

                    // $array=['subcategory_image'=>$images];
                    // SubCategory::where('id',$id)->update($array);

                    $file = $request->file('imagefile');
                    $filename=Str::slug(trim($data['subcategory_name']),"_").'_'.time();
                    $extension = $file->extension();
                    $filenametostore = $filename.'.'.$extension;

                    $resizeImage  = Image::make($file)->resize(350, 350, function ($constraint) {
                        $constraint->aspectRatio();
                        $constraint->upsize();
                    })->stream();
                    Storage::disk('s3')->put($subpath.'/'.$filenametostore, $resizeImage->__toString());
                    $url = Storage::disk('s3')->url($subpath.'/'.$filenametostore);
                    $img_insert['subcategory_image'] = $url;
                    SubCategory::where('id',$id)->update($img_insert);
                }

                if($data['catalogs']){
                    $catalogs = explode(',', $data['catalogs']);
                    foreach ($catalogs as $key => $value) {
                        DB::table('catalog_sub_categories')->insert([
                            'user_id' => Auth::user()->id,
                            'sub_category_id' => $id,
                            'catalog_id' => $value
                        ]);
                    }
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

                $query = SubCategory::where('id',$data['id'])->first();

                // $mainpath = myFunction::pathAsset();
                $subpath = 'users/'.Auth::user()->id.'/subcategory';
                // $path = $mainpath.$subpath;
                // File::isDirectory($path) or File::makeDirectory($path, 0777, true, true);

                if($request->hasFile('imagefile')){
                    // if(!empty($query['subcategory_image']) and File::exists($path.'/'.basename(parse_url($query['subcategory_image'])['path']))){
                    //     unlink($path.'/'.basename(parse_url($data['subcategory_image'])['path']));
                    // }
                    
                    // $imgfile = Image::make($_FILES['imagefile']['tmp_name']);
                    // $imgfile->fit(350, 350);
                    // $name=Str::slug(trim($data['subcategory_name']),"_").'_'.time();
                    // $extension = $request->file('imagefile')->getClientOriginalExtension();
                    // $imgfile->save($path.'/'.$name.'.'.$extension);
                    // $image=$subpath.'/'.$name.'.'.$extension;

                    $file = $request->file('imagefile');
                    $filename=Str::slug(trim($data['subcategory_name']),"_").'_'.time();
                    $extension = $file->extension();
                    $filenametostore = $filename.'.'.$extension;

                    $resizeImage  = Image::make($file)->resize(350, 350, function ($constraint) {
                        $constraint->aspectRatio();
                        $constraint->upsize();
                    })->stream();
                    Storage::disk('s3')->put($subpath.'/'.$filenametostore, $resizeImage->__toString());
                    $image = Storage::disk('s3')->url($subpath.'/'.$filenametostore);
                }else{
                    if(!empty($data['subcategory_image'])){
                        // $old = $path.'/'.basename(parse_url($data['subcategory_image'])['path']);
                        // $name=Str::slug(trim($data['subcategory_name']),"_").'_'.time();
                        // $ext=explode(".",basename(parse_url($data['subcategory_image'])['path']));
                        // $new = $path.'/'.$name.'.'.$ext[1];
                        // @rename($old, $new);
                        // $image=$subpath.'/'.$name.'.'.$ext[1];
                        if(strpos($data['subcategory_image'], 'amazonaws.com') !== false){
                            $image=$data['subcategory_image'];
                        }
                        else{
                            $image='';
                        }
                    }else{
                        $image=$data['subcategory_image'];
                    }
                }

                $array=['subcategory_name'=>$data['subcategory_name'],
                        'subcategory_slug'=>$data['id'].'-'.Str::slug(trim($data['subcategory_name']),"-"),
                        'subcategory_color'=>$data['subcategory_color'],
                    	'subcategory_image'=>$image];
                SubCategory::where('id',$data['id'])->update($array);

                DB::table('catalog_sub_categories')->where([
                    'user_id' => Auth::user()->id,
                    'sub_category_id' => $data['id'],
                ])->delete();
                
                if($data['catalogs']){
                    $catalogs = explode(',', $data['catalogs']);
                    foreach ($catalogs as $key => $value) {
                        DB::table('catalog_sub_categories')->insert([
                            'user_id' => Auth::user()->id,
                            'sub_category_id' => $data['id'],
                            'catalog_id' => $value
                        ]);
                    }
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
            	$query = SubCategory::where('id',$id)->first();

            	$mainpath = myFunction::pathAsset();
            	$subpath = '/users/'.Auth::user()->id.'/subcategory';
            	$path = $mainpath.$subpath;

            	if(!empty($query['subcategory_image']) and File::exists($path.'/'.basename(parse_url($query['subcategory_image'])['path']))){
            	    unlink($path.'/'.basename(parse_url($query['subcategory_image'])['path']));
            	}

                try {
                    Storage::disk('s3')->delete(str_replace('https://scaneat.s3.ap-southeast-1.amazonaws.com/', '', $query['subcategory_image']));
                } catch (\Exception $e) {
                    //throw $th;
                }

                SubCategory::where('id',$id)->delete();
            });
         }
        catch(\Exception $e) {
            return false;
        }
        return true;
    }
}
