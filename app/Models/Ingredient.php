<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use DB;
use myFunction;
use Auth;

class Ingredient extends Model
{
    protected $fillable = ['user_id', 'catalog_id', 'name', 'price'];

    public function itemStock()
    {
        return $this->hasMany(ItemsStock::class);
    }

    public static function create($request){
        try {
            DB::transaction(function () use ($request) {
                $id = myFunction::id('ingredients','id');
                $var=new Ingredient;
                $var->id=$id;
                $var->user_id=Auth::user()->id;
                $var->catalog_id=$request['catalog_id'];
                $var->name=trim($request['name']);
                $var->price=trim($request['price']);
                $var->item_unit=trim($request['item_unit']);
                $var->save();
            });
         }
        catch(\Exception $e) {
            return false;
        }
        return true;
    }

    public static function updateData($request){
        try {
            DB::transaction(function () use ($request) {
              Ingredient::where('id',$request->id)->update(['name'=>$request->name,'item_unit'=>$request->item_unit,'price'=>$request->price]);
            });
         }
        catch(\Exception $e) {
            return false;
        }
        return true;
    }
}
