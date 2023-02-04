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

class Service extends Model
{
    /**
     * The attributes that are protected from mass assignable, others are mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id'];

    public function detail()
    {
        return $this->hasMany(ServiceDetail::class, 'service_id');
    }

    public static function save_data($request){
    	try {
    	    DB::transaction(function () use ($request) {
    	        $id = myFunction::id('services','id');
                $data=$request->all();
                $var=new Service;
                $var->id=$id;
                $var->user_id=Auth::user()->id;
                $var->title=trim($data['title']);
                $var->description=$data['description'];
                $var->save();

                // $mainpath = myFunction::pathAsset();
                $subpath = 'users/'.Auth::user()->id.'/items';
                $thumbs = 'users/'.Auth::user()->id.'/items/thumbs';
                // $path = $mainpath.$subpath;
                // $thumbspath = $mainpath.$thumbs;
                // File::isDirectory($path) or File::makeDirectory($path, 0777, true, true);
                // File::isDirectory($thumbspath) or File::makeDirectory($thumbspath, 0777, true, true);

                if($request->hasFile('image')){
                    // $imgfile_one = Image::make($_FILES['image']['tmp_name']);

                    // $name_one=Str::slug(trim($data['title']),"_").'_'.$id.'_1';
                    // $extension_one = $request->file('image')->getClientOriginalExtension();
                    // $imgfile_one->save($path.'/'.$name_one.'.'.$extension_one);

                    // //Thumb
                    // $imgfile_one_thumb = Image::make($_FILES['image']['tmp_name']);
                    // $imgfile_one_thumb->fit(650, 650);
                    // $name_one_thumb=Str::slug(trim($data['title']),"_").'_'.$id.'_1';
                    // $extension_one_thumb = $request->file('image')->getClientOriginalExtension();
                    // $imgfile_one_thumb->save($thumbspath.'/'.$name_one_thumb.'.'.$extension_one_thumb);
                    // //End

                    // $images_one=$thumbs.'/'.$name_one.'.'.$extension_one;

                    // $array_one=['image'=>$images_one];
                    // Service::where('id',$id)->update($array_one);

                    $file = $request->file('image');
                    $filename=Str::slug(trim($data['title']),"_").'_'.$id.'_1';
                    $extension = $file->extension();
                    $filenametostore = $filename.'.'.$extension;
                    Storage::disk('s3')->putFileAs($subpath, $file, $filenametostore);
                    $url = Storage::disk('s3')->url($subpath.'/'.$filenametostore);
                    $img_insert['image'] = $url;
                    Service::where('id',$id)->update($img_insert);

                    //Thumb
                    $resizeImage  = Image::make($file)->resize(650, 650, function ($constraint) {
                        $constraint->aspectRatio();
                        $constraint->upsize();
                    })->stream();
                    Storage::disk('s3')->put($thumbs.'/'.$filenametostore, $resizeImage->__toString());
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

                $query = Service::where('id',$data['id'])->first();

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

                    // $name_one=Str::slug(trim($data['title']),"_").'_'.$data['id'].'_1';
                    // $extension_one = $request->file('image')->getClientOriginalExtension();
                    // $imgfile_one->save($path.'/'.$name_one.'.'.$extension_one);

                    // //Thumb
                    // $imgfile_one_thumb = Image::make($_FILES['image']['tmp_name']);
                    // $imgfile_one_thumb->fit(650, 650);
                    // $name_one_thumb=Str::slug(trim($data['title']),"_").'_'.$data['id'].'_1';
                    // $extension_one_thumb = $request->file('image')->getClientOriginalExtension();
                    // $imgfile_one_thumb->save($thumbspath.'/'.$name_one_thumb.'.'.$extension_one_thumb);
                    // //End

                    // $images_one=$thumbs.'/'.$name_one.'.'.$extension_one;

                    try {
                        Storage::disk('s3')->delete(str_replace('https://scaneat.s3.ap-southeast-1.amazonaws.com/', '', $data['item_image']));
                    } catch (\Exception $e) {
                        //throw $th;
                    }

                    $file = $request->file('image');
                    $filename=Str::slug(trim($data['title']),"_").'_'.$data['id'].'_1';
                    $extension = $file->extension();
                    $filenametostore = $filename.'.'.$extension;
                    Storage::disk('s3')->putFileAs($subpath, $file, $filenametostore);
                    $url = Storage::disk('s3')->url($subpath.'/'.$filenametostore);
                    $images_one=$url;

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
                }else{
                    if(!empty($data['item_image'])){

                        // $old = $path.'/'.basename(parse_url($data['item_image'])['path']);
                        // $name_one=Str::slug(trim($data['title']),"_").'_'.$data['id'].'_1';
                        // $ext=explode(".",basename(parse_url($data['item_image'])['path']));
                        // $new = $path.'/'.$name_one.'.'.$ext[1];
                        // rename($old, $new);

                        // $oldthumbs = $thumbspath.'/'.basename(parse_url($data['item_image'])['path']);
                        // $name_one_thumbs=Str::slug(trim($data['title']),"_").'_'.$data['id'].'_1';
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

                $array_one=['title'=>$data['title'],
                        'description'=>$data['description'],
                        'image'=>$images_one,
                    ];
                Service::where('id',$data['id'])->update($array_one);
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
            	$query = Service::where('id',$id)->first();

                // Storage::disk('s3')->delete(str_replace('https://scaneat.s3.ap-southeast-1.amazonaws.com/', '', $query['image']));
                // Storage::disk('s3')->delete(str_replace('https://scaneat.s3.ap-southeast-1.amazonaws.com/', '', 'thumbs/'.$query['image']));

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

                Service::where('id',$id)->delete();
            });
         }
        catch(\Exception $e) {
            return false;
        }
        return true;
    }
}
