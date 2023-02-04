<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Http\Requests;
use App\Helper\myFunction;
use Illuminate\Support\Facades\Storage;

use Image;
use Input;
use File;
use Auth;

class UserComplaint extends Model
{
    protected $table = 'user_services';

    public function customer(){
        return $this->belongsTo('App\Models\Customer', 'customer_id');
	}

    public static function update_data($request){
        try {
            DB::transaction(function () use ($request) {
                $data=$request->all();

                $query = UserComplaint::where('id',$data['id'])->first();

                // $mainpath = myFunction::pathAsset();
                $subpath = 'users/'.Auth::user()->id.'/items';
                $thumbs = 'users/'.Auth::user()->id.'/items/thumbs';
                // $path = $mainpath.$subpath;
                // $thumbspath = $mainpath.$thumbs;
                // File::isDirectory($path) or File::makeDirectory($path, 0777, true, true);
                // File::isDirectory($thumbspath) or File::makeDirectory($thumbspath, 0777, true, true);

                if($request->hasFile('image')){
                    // if(!empty($query['image']) and File::exists($path.'/'.basename(parse_url($query['image'])['path']))){
                    //     @unlink($path.'/'.basename(parse_url($data['image'])['path']));
                    //     @unlink($path.'/thumbs/'.basename(parse_url($query['image'])['path']));
                    // }

                    // $imgfile_one = Image::make($_FILES['image']['tmp_name']);

                    // $name_one=Str::slug(trim($data['handler']),"_").'_'.$data['id'].'_1';
                    // $extension_one = $request->file('image')->getClientOriginalExtension();
                    // $imgfile_one->save($path.'/'.$name_one.'.'.$extension_one);

                    // //Thumb
                    // $imgfile_one_thumb = Image::make($_FILES['image']['tmp_name']);
                    // $imgfile_one_thumb->fit(650, 650);
                    // $name_one_thumb=Str::slug(trim($data['handler']),"_").'_'.$data['id'].'_1';
                    // $extension_one_thumb = $request->file('image')->getClientOriginalExtension();
                    // $imgfile_one_thumb->save($thumbspath.'/'.$name_one_thumb.'.'.$extension_one_thumb);
                    // //End

                    // $images_one=$thumbs.'/'.$name_one.'.'.$extension_one;

                    $file = $request->file('image');
                    $filename=Str::slug(trim($data['handler']),"_").'_'.$data['id'].'_1';
                    $extension = $file->extension();
                    $filenametostore = $filename.'.'.$extension;
                    Storage::disk('s3')->putFileAs($subpath, $file, $filenametostore);

                    //Thumb
                    try {
                        Storage::disk('s3')->delete(str_replace('https://scaneat.s3.ap-southeast-1.amazonaws.com/', '', 'thumbs/'.$data['item_image']));
                    } catch (\Exception $e) {
                        //throw $th;
                    }
                    $resizeImage  = Image::make($file)->resize(650, 650, function ($constraint) {
                        $constraint->aspectRatio();
                        $constraint->upsize();
                    })->stream();
                    Storage::disk('s3')->put($thumbs.'/'.$filenametostore, $resizeImage->__toString());
                    $images_one = Storage::disk('s3')->url($thumbs.'/'.$filenametostore);

                }else{
                    if(!empty($data['item_image'])){

                        // $old = $path.'/'.basename(parse_url($data['item_image'])['path']);
                        // $name_one=Str::slug(trim($data['handler']),"_").'_'.$data['id'].'_1';
                        // $ext=explode(".",basename(parse_url($data['item_image'])['path']));
                        // $new = $path.'/'.$name_one.'.'.$ext[1];
                        // rename($old, $new);

                        // $oldthumbs = $thumbspath.'/'.basename(parse_url($data['item_image'])['path']);
                        // $name_one_thumbs=Str::slug(trim($data['handler']),"_").'_'.$data['id'].'_1';
                        // $ext_thumbs=explode(".",basename(parse_url($data['item_image'])['path']));
                        // $new_thumbs = $thumbspath.'/'.$name_one_thumbs.'.'.$ext_thumbs[1];
                        // rename($oldthumbs, $new_thumbs);

                        // $images_one=$thumbs.'/'.$name_one.'.'.$ext[1];

                        if(strpos($data['item_image'], 'amazonaws.com') !== false){
                            $images_one=$data['item_image'];
                        }
                        else{
                            $images_one='';
                        }
                    }else{
                        $images_one=$data['item_image'];
                    }
                }

                $handled_on = \Carbon\Carbon::createFromFormat('m-d-Y H:i', $data['handled_on']);
                
                $array_one=['handler'=>$data['handler'],
                        'job_result'=>$data['job_result'],
                        'handled_on'=>$handled_on,
                        'job_image'=>$images_one,
                    ];

                UserComplaint::where('id',$data['id'])->update($array_one);
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
            	$query = UserComplaint::where('id',$id)->first();

            	$mainpath = myFunction::pathAsset();
            	$subpath = '/users/'.Auth::user()->id.'/items';
            	$path = $mainpath.$subpath;

            	if(!empty($query['image']) and File::exists($path.'/'.basename(parse_url($query['image'])['path']))){
            	    @unlink($path.'/'.basename(parse_url($query['image'])['path']));
                    @unlink($path.'/thumbs/'.basename(parse_url($query['image'])['path']));
            	}

                try {
                    Storage::disk('s3')->delete(str_replace('https://scaneat.s3.ap-southeast-1.amazonaws.com/', '', $query['image']));
                } catch (\Exception $e) {
                    //throw $th;
                }

                try {
                    Storage::disk('s3')->delete(str_replace('https://scaneat.s3.ap-southeast-1.amazonaws.com/', '', 'thumbs/'.$query['image']));
                } catch (\Exception $e) {
                    //throw $th;
                }

                UserComplaint::where('id',$id)->delete();
            });
         }
        catch(\Exception $e) {
            return false;
        }
        return true;
    }
}
