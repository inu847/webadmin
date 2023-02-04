<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Http\Requests;

use App\Helper\myFunction;

use Image;
use Input;
use File;
use Auth;

class Category extends Model
{
    
    /**
     * The attributes that are protected from mass assignable, others are mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id'];
    protected $table = 'category';

    public function catalog()
    {
        return $this->belongsToMany('App\Models\Catalog', 'catalog_categories' ,'category_id', 'catalog_id');
    }

    public static function save_data($request){
        try {
            DB::transaction(function () use ($request) {
                $id = myFunction::id('category','id');
                $data=$request->all();
                $var=new Category;
                $var->id=$id;
                $var->user_id=Auth::user()->id;
                $var->category_name=trim($data['category_name']);
                $var->category_slug=Auth::user()->id.'-'.Str::slug(trim($data['category_name']),"-");
                $var->category_color=$data['category_color'];
                $var->category_type=$data['category_type'];
                $var->save();
                
                if($data['catalogs']){
                    $catalogs = explode(',', $data['catalogs']);
                    foreach ($catalogs as $key => $value) {
                        DB::table('catalog_categories')->insert([
                            'user_id' => Auth::user()->id,
                            'category_id' => $id,
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

                // $currentpos = Category::where('category_order',$data['category_order'])->first();
                // Category::where('id',$currentpos['id'])->update(['category_order'=>$data['replace_position']]);

                $array=['category_name'=>$data['category_name'],
                        'category_slug'=>Auth::user()->id.'-'.Str::slug(trim($data['category_name']),"-"),
                        'category_color'=>$data['category_color']];
                Category::where('id',$data['id'])->update($array);

                DB::table('catalog_categories')->where([
                    'user_id' => Auth::user()->id,
                    'category_id' => $data['id'],
                ])->delete();
                
                if($data['catalogs']){
                    $catalogs = explode(',', $data['catalogs']);
                    foreach ($catalogs as $key => $value) {
                        DB::table('catalog_categories')->insert([
                            'user_id' => Auth::user()->id,
                            'category_id' => $data['id'],
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
                Category::where('id',$id)->delete();
            });
         }
        catch(\Exception $e) {
            return false;
        }
        return true;
    }
}
