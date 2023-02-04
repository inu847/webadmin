<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use DB;
use myFunction;
use Auth;

use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;

class Addon extends Model
{
    protected $fillable = ['user_id', 'catalog_id', 'name', 'price', 'item_unit', 'image', 'description'];

    public function itemStock()
    {
        return $this->hasMany(ItemsStock::class);
    }

    public static function updateData($request){
        try {
            DB::transaction(function () use ($request) {

              $subpath = 'users/'.Auth::user()->id.'/addons';
              if($request->hasFile('image')){
                  $file = $request->file('image');
                  $filename = uniqid();
                  $extension = $file->extension();
                  $filenametostore = $filename.'.'.$extension;
                  Storage::disk('s3')->putFileAs($subpath, $file, $filenametostore);
                  $file_path = Storage::disk('s3')->url($subpath.'/'.$filenametostore);
                  $data['image'] = $file_path;
              }

              Addon::where('id',$request->id)->update(['name'=>$request->name,'item_unit'=>$request->item_unit,'price'=>$request->price,'description'=>$request->description]);
            });
         }
        catch(\Exception $e) {
            return false;
        }
        return true;
    }
}
