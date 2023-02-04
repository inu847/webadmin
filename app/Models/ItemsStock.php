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
use Illuminate\Support\Facades\Session;

class ItemsStock extends Model
{
    protected $table = 'items_stock';

    /**
     * The attributes that are protected from mass assignable, others are mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id'];

    public static function save_data($request){
    	try {
    	    DB::transaction(function () use ($request) {
    	    	$data=$request->all();

                $catalog_id = Session::get('catalogsession');
                
                $itemStock = null;
                if (isset($data['ingredient_id'])) {
                    $itemStock = ItemsStock::where('ingredient_id', $data['ingredient_id'])->where('catalog', $catalog_id)->where('user_id', Auth::user()->id)->first();
                }

                if ($itemStock) {
                    // UPDATE STOCK
                    $itemStock->id=myFunction::id('items_stock','id');
                    $itemStock->stock= $itemStock->stock + $data['stock'];
                    $itemStock->notes=isset($data['notes']) ? $data['notes'] : null;
                    $itemStock->save();

                    // HISTORY STOCK
                    $data['catalog_id'] = $catalog_id;
                    $data['user_id'] = Auth::user()->id;
                    $data['notes'] = isset($data['notes']) ? $data['notes'] : null;
                    ItemIn::create($data);
                }else {
                    $varadd=new ItemsStock;
                    $varadd->id=myFunction::id('items_stock','id');
                    $varadd->user_id=Auth::user()->id;
                    $varadd->catalog=$data['catalog'];
                    $varadd->item_id=$data['item_id'] ?? null;
                    $varadd->ingredient_id=($data['ingredient_id'] ?? null);
                    $varadd->stock=$data['stock'];
                    $varadd->notes=isset($data['notes']) ? $data['notes'] : null;
                    $varadd->save();

                    // HISTORY STOCK
                    $data['catalog_id'] = $catalog_id;
                    $data['user_id'] = Auth::user()->id;
                    $data['notes'] = isset($data['notes']) ? $data['notes'] : null;
                    ItemIn::create($data);
                }
    	    });
    	 }
    	catch(\Exception $e) {
            // dd($e);
    	    return false;
    	}
    	return true;
    }
    public static function update_data($request){
        try {
            DB::transaction(function () use ($request) {
                $data=$request->all();
                $array=[
                    // 'catalog'=>$data['catalog'],
                    'user_id'=>Auth::user()->id,
                    'stock'=>$data['stock'],
                    // 'item_id'=>$data['item_id'],
                    'notes'=>isset($data['notes']) ? $data['notes'] : null
                ];
                ItemsStock::where('id',$data['id'])->update($array);
            });
         }
        catch(\Exception $e) {
            return false;
        }
        return true;
    }
    public static function delete_data($addon){
    	try {
    	    DB::transaction(function () use ($addon) {
    	    	ItemsStock::where('id',$addon)->delete();
    	    });
    	 }
    	catch(\Exception $e) {
    	    return false;
    	}
    	return true;
    }

    public function ingredient()
    {
        return $this->belongsTo(Ingredient::class, 'ingredient_id', 'id');
    }

    public function addons()
    {
        return $this->belongsTo(Addon::class, 'addons_id', 'id');
    }
}
