<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Http\Requests;

use App\Models\ItemsDetail;
use App\Models\ItemsStock;
use App\Helper\myFunction;
use App\User;
use Illuminate\Support\Facades\Storage;

use Image;
use Input;
use File;
use Auth;
use Session;

class Items extends Model
{
    protected $table = 'items';

    public function catalog()
    {
        return $this->belongsToMany('App\Models\Catalog', 'catalog_items' ,'item_id', 'catalog_id');
    }

    public static function save_data($request){
    	try {
    	    DB::transaction(function () use ($request) {
    	        $id = myFunction::id('items','id');
                $data=$request->all();
                $var=new Items;
                $var->id=$id;
                $var->user_id=Auth::user()->id;
                $var->items_name=trim($data['items_name']);
                $var->items_slug=$id.'-'.Str::slug(trim($data['items_name']),"-");
                $var->items_price=trim($data['items_price']);
                $var->hpp=trim($data['hpp']);
                $var->item_sku=trim($data['item_sku']);
                $var->centered_stock=trim($data['centered_stock']);
                $var->items_discount=trim($data['items_discount']);
                $var->items_description=$data['items_description'];
                $var->hitung_stok=$data['hitung_stok'];
                $var->items_youtube=trim($data['items_youtube']);
                $var->items_color=$data['items_color'];
                $var->item_type=trim($data['item_type']);
                
                //$var->ready_stock=trim($data['ready_stock']);
                //$var->addons=(!empty($data['addons']))?json_encode(explode(',', $data['addons'])):'';
                $var->save();

                // $mainpath = myFunction::pathAsset();
                $subpath = 'users/'.Auth::user()->id.'/items';
                $thumbs = 'users/'.Auth::user()->id.'/items/thumbs';
                // $path = $mainpath.$subpath;
                // $thumbspath = $mainpath.$thumbs;
                // File::isDirectory($path) or File::makeDirectory($path, 0777, true, true);
                // File::isDirectory($thumbspath) or File::makeDirectory($thumbspath, 0777, true, true);

                $array = [
                    1 => 'one',
                    2 => 'two',
                    3 => 'three',
                    4 => 'four',
                ];
                $update_img = [];

                foreach ($array as $key => $value) {
                    if($request->hasFile('imagefile_'.$array[$key])){
                        $file = $request->file('imagefile_'.$array[$key]);
                        $filename=Str::slug(trim($data['items_name']),"_").'_'.$id.'_'.$key;
                        $extension = $file->extension();
                        $filenametostore = $filename.'.'.$extension;
                        Storage::disk('s3')->putFileAs($subpath, $file, $filenametostore);
                        $url = Storage::disk('s3')->url($subpath.'/'.$filenametostore);
                        $img_insert['item_image_'.$array[$key]] = $url;
                        if($key == 1){
                            $img_insert['item_image_primary'] = $url;
                        }
                        Items::where('id',$id)->update($img_insert);

                        //Thumb
                        $resizeImage  = Image::make($file)->resize(650, 650, function ($constraint) {
                            $constraint->aspectRatio();
                            $constraint->upsize();
                        })->stream();
                        Storage::disk('s3')->put($thumbs.'/'.$filenametostore, $resizeImage->__toString());

                        // // $imgfile_one = Image::make($_FILES['imagefile_one']['tmp_name']);

                        // $name_one=Str::slug(trim($data['items_name']),"_").'_'.$id.'_1';
                        // $extension_one = $request->file('imagefile_one')->extension();
                        // $imgfile_one->save($path.'/'.$name_one.'.'.$extension_one);

                        // //Thumb
                        // $imgfile_one_thumb = Image::make($_FILES['imagefile_one']['tmp_name']);
                        // $imgfile_one_thumb->fit(650, 650);
                        // $name_one_thumb=Str::slug(trim($data['items_name']),"_").'_'.$id.'_1';
                        // $extension_one_thumb = $request->file('imagefile_one')->extension();
                        // $imgfile_one_thumb->save($thumbspath.'/'.$name_one_thumb.'.'.$extension_one_thumb);
                        // //End

                        // $images_one=$thumbs.'/'.$name_one.'.'.$extension_one;

                        // $array_one=['item_image_one'=>$images_one,'item_image_primary'=>$images_one];
                        // Items::where('id',$id)->update($array_one);
                    }
                }

                /*
                    if($request->hasFile('imagefile_one')){
                        // $imgfile_one = Image::make($_FILES['imagefile_one']['tmp_name']);

                        $name_one=Str::slug(trim($data['items_name']),"_").'_'.$id.'_1';
                        $extension_one = $request->file('imagefile_one')->extension();
                        $imgfile_one->save($path.'/'.$name_one.'.'.$extension_one);

                        //Thumb
                        $imgfile_one_thumb = Image::make($_FILES['imagefile_one']['tmp_name']);
                        $imgfile_one_thumb->fit(650, 650);
                        $name_one_thumb=Str::slug(trim($data['items_name']),"_").'_'.$id.'_1';
                        $extension_one_thumb = $request->file('imagefile_one')->extension();
                        $imgfile_one_thumb->save($thumbspath.'/'.$name_one_thumb.'.'.$extension_one_thumb);
                        //End

                        $images_one=$thumbs.'/'.$name_one.'.'.$extension_one;

                        $array_one=['item_image_one'=>$images_one,'item_image_primary'=>$images_one];
                        Items::where('id',$id)->update($array_one);
                    }
                    if($request->hasFile('imagefile_two')){
                        $imgfile_two = Image::make($_FILES['imagefile_two']['tmp_name']);

                        $name_two=Str::slug(trim($data['items_name']),"_").'_'.$id.'_2';
                        $extension_two = $request->file('imagefile_two')->extension();
                        $imgfile_two->save($path.'/'.$name_two.'.'.$extension_two);

                        //Thumb
                        $imgfile_two_thumb = Image::make($_FILES['imagefile_two']['tmp_name']);
                        $imgfile_two_thumb->fit(650, 650);
                        $name_two_thumb=Str::slug(trim($data['items_name']),"_").'_'.$id.'_2';
                        $extension_two_thumb = $request->file('imagefile_two')->extension();
                        $imgfile_two_thumb->save($thumbspath.'/'.$name_two_thumb.'.'.$extension_two_thumb);
                        //End

                        $images_two=$thumbs.'/'.$name_two.'.'.$extension_two;

                        $array_two=['item_image_two'=>$images_two];
                        Items::where('id',$id)->update($array_two);
                    }
                    if($request->hasFile('imagefile_three')){
                        $imgfile_three = Image::make($_FILES['imagefile_three']['tmp_name']);

                        $name_three=Str::slug(trim($data['items_name']),"_").'_'.$id.'_3';
                        $extension_three = $request->file('imagefile_three')->extension();
                        $imgfile_three->save($path.'/'.$name_three.'.'.$extension_three);

                        //Thumb
                        $imgfile_three_thumb = Image::make($_FILES['imagefile_three']['tmp_name']);
                        $imgfile_three_thumb->fit(650, 650);
                        $name_three_thumb=Str::slug(trim($data['items_name']),"_").'_'.$id.'_3';
                        $extension_three_thumb = $request->file('imagefile_three')->extension();
                        $imgfile_three_thumb->save($thumbspath.'/'.$name_three_thumb.'.'.$extension_three_thumb);
                        //End

                        $images_three=$thumbs.'/'.$name_three.'.'.$extension_three;

                        $array_three=['item_image_three'=>$images_three];
                        Items::where('id',$id)->update($array_three);
                    }
                    if($request->hasFile('imagefile_four')){
                        $imgfile_four = Image::make($_FILES['imagefile_four']['tmp_name']);

                        $name_four=Str::slug(trim($data['items_name']),"_").'_'.$id.'_4';
                        $extension_four = $request->file('imagefile_four')->extension();
                        $imgfile_four->save($path.'/'.$name_four.'.'.$extension_four);

                        //Thumb
                        $imgfile_four_thumb = Image::make($_FILES['imagefile_four']['tmp_name']);
                        $imgfile_four_thumb->fit(650, 650);
                        $name_four_thumb=Str::slug(trim($data['items_name']),"_").'_'.$id.'_4';
                        $extension_four_thumb = $request->file('imagefile_four')->extension();
                        $imgfile_four_thumb->save($thumbspath.'/'.$name_four_thumb.'.'.$extension_four_thumb);
                        //End

                        $images_four=$thumbs.'/'.$name_four.'.'.$extension_four;

                        $array_four=['item_image_four'=>$images_four];
                        Items::where('id',$id)->update($array_four);
                    }
                */

                if($data['catalogs']){
                    $catalogs = explode(',', $data['catalogs']);
                    foreach ($catalogs as $key => $value) {
                        DB::table('catalog_items')->insert([
                            'user_id' => Auth::user()->id,
                            'item_id' => $id,
                            'catalog_id' => $value
                        ]);

                        // ItemsStock::create([
                        //     'user_id' => Auth::user()->id,
                        //     'catalog' => $value,
                        //     'item_id' => $id,
                        //     'stock' => trim($data['stock']),
                        // ]);
                    }
                }

                // if($data['prices']){
                //     $prices = explode(',', $data['prices']);
                //     foreach ($prices as $key => $value) {
                //         DB::table('items_prices')->insert([
                //             'user_id' => Auth::user()->id,
                //             'item_id' => $id,
                //             'price_type_id' => $value
                //         ]);
                //     }
                // }

    	    });
    	 }
    	catch(\Exception $e) {
    	    return false;
    	}
    	return true;
    }
    public static function save_material($request){
        try {
            DB::transaction(function () use ($request) {
                $id = myFunction::id('items','id');
                $data=$request->all();
                $var=new Items;
                $var->id=$id;
                $var->user_id=Auth::user()->id;
                $var->items_name=trim($data['items_name']);
                $var->items_slug=$id.'-'.Str::slug(trim($data['items_name']),"-");
                $var->ready_stock=trim($data['ready_stock']);
                $var->item_type=trim($data['item_type']);
                $var->item_unit=trim($data['item_unit']);

                if((Session::get('catalogsession')!='All')){
                    $var->catalog_id=Session::get('catalogsession');
                }

                $var->save();
            });
         }
        catch(\Exception $e) {
            return false;
        }
        return true;
    }
    public static function save_stock_addon($request){
        try {
            DB::transaction(function () use ($request) {
                $id = myFunction::id('items','id');
                $data=$request->all();
                $var=new Items;
                $var->id=$id;
                $var->user_id=Auth::user()->id;
                $var->items_name=trim($data['items_name']);
                $var->items_slug=$id.'-'.Str::slug(trim($data['items_name']),"-");
                $var->ready_stock=trim($data['ready_stock']);
                $var->item_type=trim($data['item_type']);
                $var->item_unit=trim($data['item_unit']);

                if((Session::get('catalogsession')!='All')){
                    $var->catalog_id=Session::get('catalogsession');
                }

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
                $query = Items::where('id',$data['id'])->first();
                $subpath = 'users/'.Auth::user()->id.'/items';
                $thumbs = 'users/'.Auth::user()->id.'/items/thumbs';

                $array = [
                    1 => 'one',
                    2 => 'two',
                    3 => 'three',
                    4 => 'four',
                ];
                $update_img = [];

                foreach ($array as $key => $value) {
                    if($request->hasFile('imagefile_'.$array[$key])){
                        $file = $request->file('imagefile_'.$array[$key]);

                        try {
                            Storage::disk('s3')->delete(str_replace('https://scaneat.s3.ap-southeast-1.amazonaws.com/', '', $data['item_image_'.$array[$key]]));
                        } catch (\Exception $e) {
                            //throw $th;
                        }
        
                        $filename=Str::slug(trim($data['items_name']),"_").'_'.$data['id'].'_'.$key;
                        $extension = $file->extension();
                        $filenametostore = $filename.'.'.$extension;
                        Storage::disk('s3')->putFileAs($subpath, $file, $filenametostore);
                        $url = Storage::disk('s3')->url($subpath.'/'.$filenametostore);
                        $update_img[$key]=$url;

                        if($key == 1){
                            $primary = $update_img[$key];
                        }
                        
                        // //Thumb
                        // if (Storage::disk('s3')->exists($thumbs.'/'.$filenametostore)){
                        //     Storage::disk('s3')->delete($thumbs.'/'.$filenametostore);
                        // }

                        // $resizeImage  = Image::make($file)->resize(650, 650, function ($constraint) {
                        //     $constraint->aspectRatio();
                        //     $constraint->upsize();
                        // })->stream();
                        // Storage::disk('s3')->put($thumbs.'/'.$filenametostore, $resizeImage->__toString());
                    }else{
                        if(!empty($data['item_image_'.$array[$key]])){
                            
                        }
                        else{
                            $update_img[$key]=$data['item_image_'.$array[$key]];
                            if($key == 1){
                                $primary = $update_img[$key];
                            }
                        }
                    }
                }

/*
                if($request->hasFile('imagefile_one')){
                    if(!empty($query['item_image_one']) and File::exists($path.'/'.basename(parse_url($query['item_image_one'])['path']))){
                        @unlink($path.'/'.basename(parse_url($data['item_image_one'])['path']));
                        @unlink($path.'/thumbs/'.basename(parse_url($query['item_image_one'])['path']));
                    }
                    else{
                        Storage::disk('s3')->delete(str_replace('https://scaneat.s3.ap-southeast-1.amazonaws.com/', '', $data['item_image_one']));
                    }

                    $name_one=Str::slug(trim($data['items_name']),"_").'_'.$data['id'].'_1';
                    $extension_one = $request->file('imagefile_one')->extension();
                    $filenametostore = $name_one.'.'.$extension_one;
                    Storage::disk('s3')->putFileAs($subpath, $request->file('imagefile_one'), $filenametostore);
                    $url = Storage::disk('s3')->url($subpath.'/'.$filenametostore);
                    $images_one=$url;
                }else{
                    if(!empty($data['item_image_one'])){
                        if(strpos($data['item_image_one'], 'amazonaws.com') !== false){
                            $images_one=$data['item_image_one'];
                        }
                        else{
                            $images_one='';
                        }
                    }else{
                        $images_one=$data['item_image_one'];
                    }
                }
                if($data['item_image_primary'] == $data['item_image_one']){
                    $primary = $images_one;
                }

                if($request->hasFile('imagefile_two')){
                    if(!empty($query['item_image_two']) and File::exists($path.'/'.basename(parse_url($query['item_image_two'])['path']))){
                        @unlink($path.'/'.basename(parse_url($data['item_image_two'])['path']));
                        @unlink($path.'/thumbs/'.basename(parse_url($query['item_image_two'])['path']));
                    }

                    $name_two=Str::slug(trim($data['items_name']),"_").'_'.$data['id'].'_2';
                    $extension_two = $request->file('imagefile_two')->extension();
                    $filenametostore = $name_one.'.'.$extension_one;
                    Storage::disk('s3')->putFileAs($subpath, $request->file('imagefile_one'), $filenametostore);
                    $url = Storage::disk('s3')->url($subpath.'/'.$filenametostore);
                    $images_one=$url;
                }else{
                    if(!empty($data['item_image_two'])){

                    }else{
                        $images_two=$data['item_image_two'];
                    }
                }
                if($data['item_image_primary'] == $data['item_image_two']){
                    $primary = $images_two;
                }

                if($request->hasFile('imagefile_three')){
                    if(!empty($query['item_image_three']) and File::exists($path.'/'.basename(parse_url($query['item_image_three'])['path']))){
                        @unlink($path.'/'.basename(parse_url($data['item_image_three'])['path']));
                        @unlink($path.'/thumbs/'.basename(parse_url($query['item_image_three'])['path']));
                    }

                    $imgfile_three = Image::make($_FILES['imagefile_three']['tmp_name']);

                    $name_three=Str::slug(trim($data['items_name']),"_").'_'.$data['id'].'_3';
                    $extension_three = $request->file('imagefile_three')->extension();
                    $imgfile_three->save($path.'/'.$name_three.'.'.$extension_three);

                    //Thumb
                    $imgfile_three_thumb = Image::make($_FILES['imagefile_three']['tmp_name']);
                    $imgfile_three_thumb->fit(650, 650);
                    $name_three_thumb=Str::slug(trim($data['items_name']),"_").'_'.$data['id'].'_3';
                    $extension_three_thumb = $request->file('imagefile_three')->extension();
                    $imgfile_three_thumb->save($thumbspath.'/'.$name_three_thumb.'.'.$extension_three_thumb);
                    //End

                    $images_three=$thumbs.'/'.$name_three.'.'.$extension_three;

                }else{
                    if(!empty($data['item_image_three'])){

                        $old = $path.'/'.basename(parse_url($data['item_image_three'])['path']);
                        $name_three=Str::slug(trim($data['items_name']),"_").'_'.$data['id'].'_3';
                        $ext=explode(".",basename(parse_url($data['item_image_three'])['path']));
                        $new = $path.'/'.$name_three.'.'.$ext[1];
                        @rename($old, $new);

                        $oldthumbs = $thumbspath.'/'.basename(parse_url($data['item_image_three'])['path']);
                        $name_three_thumbs=Str::slug(trim($data['items_name']),"_").'_'.$data['id'].'_3';
                        $ext_thumbs=explode(".",basename(parse_url($data['item_image_three'])['path']));
                        $new_thumbs = $thumbspath.'/'.$name_three_thumbs.'.'.$ext_thumbs[1];
                        @rename($oldthumbs, $new_thumbs);

                        $images_three=$thumbs.'/'.$name_three.'.'.$ext[1];
                    }else{
                        $images_three=$data['item_image_three'];
                    }
                }
                if($data['item_image_primary'] == $data['item_image_three']){
                    $primary = $images_three;
                }

                if($request->hasFile('imagefile_four')){
                    if(!empty($query['item_image_four']) and File::exists($path.'/'.basename(parse_url($query['item_image_four'])['path']))){
                        @unlink($path.'/'.basename(parse_url($data['item_image_four'])['path']));
                        @unlink($path.'/thumbs/'.basename(parse_url($query['item_image_four'])['path']));
                    }

                    $imgfile_four = Image::make($_FILES['imagefile_four']['tmp_name']);

                    $name_four=Str::slug(trim($data['items_name']),"_").'_'.$data['id'].'_4';
                    $extension_four = $request->file('imagefile_four')->extension();
                    $imgfile_four->save($path.'/'.$name_four.'.'.$extension_four);

                    //Thumb
                    $imgfile_four_thumb = Image::make($_FILES['imagefile_four']['tmp_name']);
                    $imgfile_four_thumb->fit(650, 650);
                    $name_four_thumb=Str::slug(trim($data['items_name']),"_").'_'.$data['id'].'_4';
                    $extension_four_thumb = $request->file('imagefile_four')->extension();
                    $imgfile_four_thumb->save($thumbspath.'/'.$name_four_thumb.'.'.$extension_four_thumb);
                    //End

                    $images_four=$thumbs.'/'.$name_four.'.'.$extension_four;

                }else{
                    if(!empty($data['item_image_four'])){

                        $old = $path.'/'.basename(parse_url($data['item_image_four'])['path']);
                        $name_four=Str::slug(trim($data['items_name']),"_").'_'.$data['id'].'_4';
                        $ext=explode(".",basename(parse_url($data['item_image_four'])['path']));
                        $new = $path.'/'.$name_four.'.'.$ext[1];
                        @rename($old, $new);

                        $oldthumbs = $thumbspath.'/'.basename(parse_url($data['item_image_four'])['path']);
                        $name_four_thumbs=Str::slug(trim($data['items_name']),"_").'_'.$data['id'].'_4';
                        $ext_thumbs=explode(".",basename(parse_url($data['item_image_four'])['path']));
                        $new_thumbs = $thumbspath.'/'.$name_four_thumbs.'.'.$ext_thumbs[1];
                        @rename($oldthumbs, $new_thumbs);

                        $images_four=$thumbs.'/'.$name_four.'.'.$ext[1];
                    }else{
                        $images_four=$data['item_image_four'];
                    }
                }
                if($data['item_image_primary'] == $data['item_image_four']){
                    $primary = $images_four;
                }
*/

                $array_one=['items_name'=>$data['items_name'],
                        'items_slug'=>$data['id'].'-'.Str::slug(trim($data['items_name']),"-"),
                        'items_color'=>$data['items_color'],
                        'items_description'=>$data['items_description'],
                        'hitung_stok'=>$data['hitung_stok'],
                        'items_price'=>$data['items_price'],
                        'hpp'=>$data['hpp'],
                        'item_sku'=>$data['item_sku'],
                        'centered_stock'=>$data['centered_stock'],
                        'items_discount'=>$data['items_discount'],
                        'items_youtube'=>$data['items_youtube'],
                        //'ready_stock'=>$data['ready_stock'],
                    ];

                foreach ($array as $key => $value) {
                    if(isset($update_img[$key])){
                        $array_one['item_image_'.$value] = $update_img[$key];
                    }
                }

                Items::where('id',$data['id'])->update($array_one);

                DB::table('catalog_items')->where([
                    'user_id' => Auth::user()->id,
                    'item_id' => $data['id'],
                ])->delete();

                DB::table('items_stock')->where([
                    'user_id' => Auth::user()->id,
                    'item_id' => $data['id'],
                ])->delete();
                
                if($data['catalogs']){
                    $catalogs = explode(',', $data['catalogs']);
                    foreach ($catalogs as $key => $value) {
                        DB::table('catalog_items')->insert([
                            'user_id' => Auth::user()->id,
                            'item_id' => $data['id'],
                            'catalog_id' => $value
                        ]);
                    }

                    ItemsStock::create([
                        'user_id' => Auth::user()->id,
                        'catalog' => $value,
                        'item_id' => $data['id'],
                        'stock' => trim($data['stock']),
                    ]);

                }

                // DB::table('items_prices')->where([
                //     'user_id' => Auth::user()->id,
                //     'item_id' => $data['id'],
                // ])->delete();

                // if($data['prices']){
                //     $prices = explode(',', $data['prices']);
                //     foreach ($prices as $key => $value) {
                //         DB::table('items_prices')->insert([
                //             'user_id' => Auth::user()->id,
                //             'item_id' => $data['id'],
                //             'price_type_id' => $value
                //         ]);
                //     }
                // }

            });
         }
        catch(\Exception $e) {
            return false;
        }
        return true;
    }
    public static function update_material($request){
        try {
            DB::transaction(function () use ($request) {
                $data=$request->all();

                $array_one=['items_name'=>$data['items_name'],
                        'items_slug'=>$data['id'].'-'.Str::slug(trim($data['items_name']),"-"),
                        'ready_stock'=>$data['ready_stock'],
                        'item_unit'=>$data['item_unit']
                    ];
                Items::where('id',$data['id'])->update($array_one);
                
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
            	$query = Items::where('id',$id)->first();

            	$mainpath = myFunction::pathAsset();
            	$subpath = '/users/'.Auth::user()->id.'/items';
            	$path = $mainpath.$subpath;

            	if(!empty($query['item_image_one']) and File::exists($path.'/'.basename(parse_url($query['item_image_one'])['path']))){
            	    @unlink($path.'/'.basename(parse_url($query['item_image_one'])['path']));
                    @unlink($path.'/thumbs/'.basename(parse_url($query['item_image_one'])['path']));
            	}
                if(!empty($query['item_image_two']) and File::exists($path.'/'.basename(parse_url($query['item_image_two'])['path']))){
                    @unlink($path.'/'.basename(parse_url($query['item_image_two'])['path']));
                    @unlink($path.'/thumbs/'.basename(parse_url($query['item_image_two'])['path']));
                }
                if(!empty($query['item_image_three']) and File::exists($path.'/'.basename(parse_url($query['item_image_three'])['path']))){
                    @unlink($path.'/'.basename(parse_url($query['item_image_three'])['path']));
                    @unlink($path.'/thumbs/'.basename(parse_url($query['item_image_three'])['path']));
                }
                if(!empty($query['item_image_four']) and File::exists($path.'/'.basename(parse_url($query['item_image_four'])['path']))){
                    @unlink($path.'/'.basename(parse_url($query['item_image_four'])['path']));
                    @unlink($path.'/thumbs/'.basename(parse_url($query['item_image_four'])['path']));
                }

                $array = [
                    1 => 'one',
                    2 => 'two',
                    3 => 'three',
                    4 => 'four',
                ];

                foreach ($array as $key => $value) {
                    try {
                        Storage::disk('s3')->delete(str_replace('https://scaneat.s3.ap-southeast-1.amazonaws.com/', '', $query['item_image_'].$value));
                    } catch (\Exception $e) {
                        //throw $th;
                    }

                    try {
                        Storage::disk('s3')->delete(str_replace('https://scaneat.s3.ap-southeast-1.amazonaws.com/', '', 'thumbs/'.$query['item_image_'].$value));
                    } catch (\Exception $e) {
                        //throw $th;
                    }
                }

                Items::where('id',$id)->delete();
            });
         }
        catch(\Exception $e) {
            return false;
        }
        return true;
    }
    public static function delete_data_material($id){
        try {
            DB::transaction(function () use ($id) {
                $query = ItemsStock::where('item_id',$id)->first();
                if(!empty($query)){
                    Items::where('id',$id)->update(['ready_stock'=>'N']);
                }else{
                    Items::where('id',$id)->delete();
                }
            });
         }
        catch(\Exception $e) {
            return false;
        }
        return true;
    }
    public static function primary_image($id,$image){
        try {
            DB::transaction(function () use ($id,$image) {
                $mainpath = myFunction::pathAsset();
                $subpath = '/users/'.Auth::user()->id.'/items';
                $path = $mainpath.$subpath;

                $query = Items::where('id',$id)->update(['item_image_primary'=>$subpath.'/thumbs/'.$image]);
            });
         }
        catch(\Exception $e) {
            return false;
        }
        return true;
    }
    public static function delete_image($id,$image,$position){
        try {
            DB::transaction(function () use ($id,$image,$position) {
                $query = Items::where('id',$id)->first();

                $mainpath = myFunction::pathAsset();
                $subpath = '/users/'.Auth::user()->id.'/items';
                $path = $mainpath.$subpath;

                if(!empty($query['item_image_'].$position) and File::exists($path.'/'.$image)){
                    @unlink($path.'/'.$image);
                    @unlink($path.'/thumbs/'.$image);
                }

                try {
                    Storage::disk('s3')->delete(str_replace('https://scaneat.s3.ap-southeast-1.amazonaws.com/', '', $query['item_image_'].$position));
                } catch (\Exception $e) {
                    //throw $th;
                }

                try {
                    Storage::disk('s3')->delete(str_replace('https://scaneat.s3.ap-southeast-1.amazonaws.com/', '', 'thumbs/'.$query['item_image_'].$position));
                } catch (\Exception $e) {
                    //throw $th;
                }

                $query = Items::where('id',$id)->update(['item_image_'.$position=>'']);
            });
         }
        catch(\Exception $e) {
            return false;
        }
        return true;
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
