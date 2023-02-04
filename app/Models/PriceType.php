<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Http\Requests;
use App\Helper\myFunction;
use App\Models\CatalogPrice;
use Input;
use Auth;

class PriceType extends Model
{    
    /**
     * The attributes that are protected from mass assignable, others are mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id'];

    public function catalog()
    {
        return $this->belongsToMany('App\Models\Catalog', 'catalog_prices' ,'price_type_id', 'catalog_id');
    }

    public static function save_data($request){
        try {
            DB::transaction(function () use ($request) {
                $id = myFunction::id('price_types','id');
                $data=$request->all();
                $var=new PriceType;
                $var->id=$id;
                $var->user_id=Auth::user()->id;
                $var->price_name=trim($data['price_name']);
                $var->save();

                $catalogs = (!empty($data['catalogs']))?explode(',', $data['catalogs']):'';

                if(!empty($catalogs)){
                    // delete old
                    CatalogPrice::where('price_type_id', $id)->where('user_id', Auth::user()->id)->delete();
                    // insert new
                    $insert_data = [];
                    foreach ($catalogs as $key => $value) {
                        $insert_data[] = [
                            'user_id' => Auth::user()->id,
                            'catalog_id' => $value,
                            'price_type_id' => $id
                        ];
                    }

                    if($insert_data){
                        CatalogPrice::insert($insert_data);
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
                $array=['price_name'=>$data['price_name']];
                PriceType::where('id',$data['id'])->update($array);

                $catalogs = (!empty($data['catalogs']))?explode(',', $data['catalogs']):'';

                if(!empty($catalogs)){
                    // delete old
                    CatalogPrice::where('price_type_id', $data['id'])->where('user_id', Auth::user()->id)->delete();
                    // insert new
                    $insert_data = [];
                    foreach ($catalogs as $key => $value) {
                        $insert_data[] = [
                            'user_id' => Auth::user()->id,
                            'catalog_id' => $value,
                            'price_type_id' => $data['id']
                        ];
                    }

                    if($insert_data){
                        CatalogPrice::insert($insert_data);
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
                PriceType::where('id',$id)->delete();
            });
         }
        catch(\Exception $e) {
            return false;
        }
        return true;
    }
}
