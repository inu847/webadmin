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

class ServiceDetail extends Model
{
    /**
     * The attributes that are protected from mass assignable, others are mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id'];

    public function service()
    {
        return $this->belongsTo(Service::class, 'service_id');
    }

    public static function save_data($request){
    	try {
    	    DB::transaction(function () use ($request) {
    	        // $id = myFunction::id('service_details','id');
                $data=$request->all();
                
                $var=new ServiceDetail;
                $var->user_id=Auth::user()->id;
                $var->service_id=trim($data['service_id']);
                $var->title=trim($data['title_detail']);
                $var->price=trim($data['price']);
                $var->description=$data['description_detail'];
                $var->save();

                $subpath = 'users/'.Auth::user()->id.'/items';
                $thumbs = 'users/'.Auth::user()->id.'/items/thumbs';

                if($request->hasFile('image_detail')){
                    $file = $request->file('image_detail');
                    $filename=Str::slug(trim($data['title_detail']),"_").'_'.$var->id.'_1';
                    $extension = $file->extension();
                    $filenametostore = $filename.'.'.$extension;
                    Storage::disk('s3')->putFileAs($subpath, $file, $filenametostore);
                    $url = Storage::disk('s3')->url($subpath.'/'.$filenametostore);
                    $img_insert['image'] = $url;
                    ServiceDetail::where('id',$var->id)->update($img_insert);

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

                $query = ServiceDetail::where('id',$data['id_detail'])->first();
                $subpath = 'users/'.Auth::user()->id.'/items';
                $thumbs = 'users/'.Auth::user()->id.'/items/thumbs';

                if($request->hasFile('image_detail')){
                    try {
                        Storage::disk('s3')->delete(str_replace('https://scaneat.s3.ap-southeast-1.amazonaws.com/', '', $data['item_image_detail']));
                    } catch (\Exception $e) {
                        //throw $th;
                    }
    
                    $file = $request->file('image_detail');
                    $filename=Str::slug(trim($data['title_detail']),"_").'_'.$data['id_detail'].'_1';
                    $extension = $file->extension();
                    $filenametostore = $filename.'.'.$extension;
                    Storage::disk('s3')->putFileAs($subpath, $file, $filenametostore);
                    $url = Storage::disk('s3')->url($subpath.'/'.$filenametostore);
                    $images_one=$url;

                    //Thumb
                    try {
                        Storage::disk('s3')->delete(str_replace('https://scaneat.s3.ap-southeast-1.amazonaws.com/', '', 'thumbs/'.$data['item_image_detail']));
                    } catch (\Exception $e) {
                        //throw $th;
                    }
    
                    $resizeImage  = Image::make($file)->resize(650, 650, function ($constraint) {
                        $constraint->aspectRatio();
                        $constraint->upsize();
                    })->stream();
                    Storage::disk('s3')->put($thumbs.'/'.$filenametostore, $resizeImage->__toString());
                }else{
                    if(!empty($data['item_image_detail'])){
                        // if(strpos($data['item_image_detail'], 'amazonaws.com') !== false){
                            $images_one=$data['item_image_detail'];
                        // }
                        // else{
                            // $images_one='';
                        // }
                    }else{
                        // $images_one=$data['item_image_detail'];
                        $images_one='';
                    }
                }

                // service_id
                // id_detail
                // title_detail
                // price
                // image
                // description_detail

                // `user_id` INT(11) NULL DEFAULT NULL,
                // `service_id` INT(11) NULL DEFAULT NULL,
                // `price` DOUBLE NULL DEFAULT NULL,
                // `title` VARCHAR(100) NULL DEFAULT NULL COLLATE 'utf8mb4_general_ci',
                // `description` TEXT NULL DEFAULT NULL COLLATE 'utf8mb4_general_ci',
                // `image` TEXT NULL DEFAULT NULL COLLATE 'latin1_swedish_ci',

                // $var->title=trim($data['title_detail']);
                // $var->price=trim($data['price']);
                // $var->description=$data['description_detail'];

                $array_one=[
                        'title'=>$data['title_detail'],
                        'description'=>$data['description_detail'],
                        'price'=>$data['price'],
                        'image'=>$images_one,
                    ];
                ServiceDetail::where('id',$data['id_detail'])->update($array_one);
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
            	$query = ServiceDetail::where('id',$id)->first();

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

                ServiceDetail::where('id',$id)->delete();
            });
         }
        catch(\Exception $e) {
            return false;
        }
        return true;
    }

}
