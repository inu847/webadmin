<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Http\Requests;

use App\Helper\myFunction;

use App\Models\Catalog;

use Image;
use Input;
use File;
use Auth;

class CatalogDetail extends Model
{
    protected $table = 'catalogdetail';
    /**
     * The attributes that are protected from mass assignable, others are mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id'];

    // public $appends = ['text','href','icon','target','title'];
    public $appends = ['href','icon','target'];

    // public function getTextAttribute()
    // {
    //     return '';
    // }
    public function getHrefAttribute()
    {
        return '';
    }
    public function getIconAttribute()
    {
        return '';
    }
    public function getTargetAttribute()
    {
        return '_self';
    }
    // public function getTitleAttribute()
    // {
    //     return '';
    // }
    public function sub()
    {
        return $this->hasMany('App\Models\CatalogDetail', 'parent_id', 'id')
			->select('catalogdetail.*',
				'category.category_name',
				'subcategory.subcategory_name',
				'items.items_name'
			)
			->selectRaw('CONCAT(category.category_name,"<br>",subcategory.subcategory_name,"<br>",items.items_name) as text')
			->selectRaw('CONCAT(category.category_name,"<br>",subcategory.subcategory_name,"<br>",items.items_name) as title')
			->leftJoin('category','catalogdetail.category_id','=','category.id')
			->leftJoin('subcategory','catalogdetail.subcategory_id','=','subcategory.id')
			->leftJoin('items','catalogdetail.item','=','items.id')
			->orderBy('urutan');
    }
    public function children()
    {
        return $this->hasMany('App\Models\CatalogDetail', 'parent_id', 'id')->with('sub')
			->select('catalogdetail.*',
				'category.category_name',
				'subcategory.subcategory_name',
				'items.items_name'
			)
			->selectRaw('CONCAT(category.category_name,"<br>",subcategory.subcategory_name,"<br>",items.items_name) as text')
			->selectRaw('CONCAT(category.category_name,"<br>",subcategory.subcategory_name,"<br>",items.items_name) as title')
			->leftJoin('category','catalogdetail.category_id','=','category.id')
			->leftJoin('subcategory','catalogdetail.subcategory_id','=','subcategory.id')
			->leftJoin('items','catalogdetail.item','=','items.id')
			->orderBy('urutan');
    }

    public static function save_data($request){
    	try {
    	    DB::transaction(function () use ($request) {
    	    	$data=$request->all();
    	    	
    	    	foreach(explode(',', $data['items']) as $item){
    	    		$checkitem=CatalogDetail::where('catalog_id',$data['catalog_id'])->where('category_id',$data['category_id'])->where('subcategory_id',$data['subcategory_id'])->where('item',$item)->count();
    	    		if($checkitem < 1){
    	    			$itemcount=CatalogDetail::where('catalog_id',$data['catalog_id'])->where('category_id',$data['category_id'])->where('subcategory_id',$data['subcategory_id'])->max('item_position');
	    	    		$positionitem = $itemcount+1;

	    	    		$id = myFunction::id('catalogdetail','id');
		    	    	$var=new CatalogDetail;
			            $var->id=$id;
			            $var->user_id=Auth::user()->id;
			            $var->catalog_id=$data['catalog_id'];
			            $var->category_id=$data['category_id'];
			            $var->category_position=$data['category_position'];
			            $var->subcategory_id=$data['subcategory_id'];
			            $var->subcategory_position=$data['subcategory_position'];
			            $var->item=$item;
			            $var->item_position=$positionitem;
			            $var->save();
			            
			            $catalogItem = CatalogItem::where('catalog_id', $data['catalog_id'])->where('item_id', $item)->first();
                        $catalogItem->status = 1;
                        $catalogItem->save();
        
    	    		}
    	    	}
    	    });
    	 }
    	catch(\Exception $e) {
    	    return false;
    	}
    	return true;
    }
    public static function change_position($request){
    	try {
    	    DB::transaction(function () use ($request) {
    	    	$data=$request->all();
    	    	if($data['status']=='Category'){
    	    		$oldposition = CatalogDetail::where('catalog_id',$data['catalog_id'])->where('category_position',$data['change_position'])->orderBy('id','desc')->first();
    	    		
    	    		CatalogDetail::where('catalog_id',$data['catalog_id'])
    	    						->where('category_id',$oldposition['category_id'])
    	    						->update(['category_position'=>$data['current']]);

    	    		CatalogDetail::where('catalog_id',$data['catalog_id'])
    	    						->where('category_id',$data['me'])
    	    						->update(['category_position'=>$data['change_position']]);
    	    	}
    	    	elseif($data['status']=='SubCategory'){
    	    		$query = CatalogDetail::where('catalog_id',$data['catalog_id'])->where('subcategory_id',$data['me'])->orderBy('id','desc')->first();
    	    		$oldposition = CatalogDetail::where('catalog_id',$data['catalog_id'])->where('category_id',$query['category_id'])->where('subcategory_position',$data['change_position'])->orderBy('id','desc')->first();
    	    		CatalogDetail::where('catalog_id',$data['catalog_id'])
    	    						->where('category_id',$oldposition['category_id'])
    	    						->where('subcategory_id',$oldposition['subcategory_id'])
    	    						->update(['subcategory_position'=>$data['current']]);
    	    		CatalogDetail::where('catalog_id',$data['catalog_id'])
    	    						->where('category_id',$oldposition['category_id'])
    	    						->where('subcategory_id',$data['me'])
    	    						->update(['subcategory_position'=>$data['change_position']]);
    	    	}
    	    	elseif($data['status']=='Item'){
    	    		$query = CatalogDetail::where('id',$data['me'])->first();
    	    		$oldposition = CatalogDetail::where('catalog_id',$data['catalog_id'])
    	    									->where('category_id',$query['category_id'])
    	    									->where('subcategory_id',$query['subcategory_id'])
    	    									->where('item_position',$data['change_position'])
    	    									->first();
    	    		CatalogDetail::where('id',$oldposition['id'])->update(['item_position'=>$data['current']]);
    	    		CatalogDetail::where('id',$data['me'])->update(['item_position'=>$data['change_position']]);
    	    	}
    	    });
    	 }
    	catch(\Exception $e) {
    	    return false;
    	}
    	return true;
    }
    public static function delete_element($catalog,$me,$type){
    	try {
    	    DB::transaction(function () use ($catalog,$me,$type) {
    	    	if($type == 'Category'){
    	    		CatalogDetail::where('catalog_id',$catalog)->where('category_id',$me)->delete();
    	    	}
    	    	elseif($type == 'SubCategory'){
    	    		CatalogDetail::where('catalog_id',$catalog)->where('subcategory_id',$me)->delete();
    	    	}
    	    	elseif($type == 'Item'){
    	    		CatalogDetail::where('id',$me)->delete();
    	    	}
    	    });
    	 }
    	catch(\Exception $e) {
    	    return false;
    	}
    	return true;
    }
    public static function available_element($catalog,$me,$available){
        try {
            DB::transaction(function () use ($catalog,$me,$available) {
                CatalogDetail::where('id',$me)->update(['available_item'=>$available]);
            });
         }
        catch(\Exception $e) {
            return false;
        }
        return true;
    }
}
