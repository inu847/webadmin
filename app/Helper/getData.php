<?php
namespace App\Helper;
use Illuminate\Support\Facades\DB;
use App\User;
use App\Models\Sliders;
use App\Models\Catalog;
use App\Models\CatalogPrice;
use App\Models\CatalogDetail;
use App\Models\Category;
use App\Models\SubCategory;
use App\Models\Items;
use App\Models\Invoice;
use App\Models\InvoiceDetail;
use App\Models\ItemsDetail;
use App\Models\InvoiceAddons;
use App\Models\InvoiceRecipe;
use App\Models\Feature;
use App\Models\ItemsStock;
use App\Models\Recipe;
use App\Models\Register;
use App\Models\MetodePembayaran;
use App\Models\CatalogMetodePembayaran;
use App\Models\ItemAddon;
use App\Models\MenuRoles;
use Carbon\Carbon;
use Auth;
use Session;

class getData{
    public static function getUser($id=null,$field=null){
        $query = User::where('id',$id)->first();
        return $query[$field];
    }
    public static function getCategory($id=null,$field=null){
        $query = Category::where('id',$id)->first();
        return $query[$field];
    }
    public static function getItem($id=null,$field=null){
        $query = Items::where('id',$id)->first();
        return $query[$field];
    }
    public static function getCatalogUsername($username=null,$field=null){
        $query = Catalog::where('catalog_username',$username)->first();
        return $query[$field];
    }
    public static function getCatalog($id=null,$field=null){
        if(Auth::user()->owner == 1){
            $catalog = Catalog::where('user_id',Auth::user()->id)->orderBy('id','asc')->get();
        }else{
            $catalog = Catalog::where('id',Auth::user()->catalog)->orderBy('id','asc')->get();
        }
        return $catalog;
    }
    public static function getSubCategory($id=null,$field=null){
        $query = SubCategory::where('id',$id)->first();
        return $query[$field];
    }
    public static function checkSliderCatalog($sliderid=null,$id=null){
        $query = Catalog::where('id',$id)->first();
        if(!empty($query)){
            $array = json_decode($query['sliders'],true);
            if(count((array)$array) > 0){
                foreach($array as $value){
                    if($value == $sliderid){
                        return true;
                        break;
                    }
                }
            }else{
                return false;
            }
        }else{
            return false;
        }       
    }
    public static function checkMetodeCatalog($metodeid=null,$id=null){
        $query = CatalogMetodePembayaran::where('metode_pembayaran_id',$metodeid)->where('catalog_id',$id)->first();
        if(!empty($query)){
            return true;
        }else{
            return false;
        }       
    }
    public static function checkMenuRole($menuid=null,$roleid=null){
        $query = MenuRoles::where('menu_id',$menuid)->where('role_id',$roleid)->first();
        if(!empty($query)){
            return true;
        }else{
            return false;
        }       
    }
    public static function checkCategoryMenuRole($menuid=null,$roleid=null){
        $query = MenuRoles::where('menu_category_id',$menuid)->where('role_id',$roleid)->first();
        if(!empty($query)){
            return true;
        }else{
            return false;
        }       
    }

    public static function checkPriceCatalog($catalog_id=null,$price_type_id=null){
        $query = CatalogPrice::where('catalog_id',$catalog_id)->where('price_type_id',$price_type_id)->first();
        if(!empty($query)){
            return true;
        }else{
            return false;
        }       
    }

    public static function countStockAddOns($catalog_id=null){
        try {
            $query = Catalog::find($catalog_id)->first();
            
            if(!empty($query)){
                return $query->stock_add_ons ?? 0;
            }else{
                return 0;
            }   
        } catch (\Throwable $th) {
            return 0;
        }
    }

    public static function getPriceCatalogItem($catalog_id=null,$price_type_id=null,$item_id=null){
        $query = DB::table('items_prices')->where('user_id',Auth::user()->id)->where('catalog_id',$catalog_id)->where('price_type_id',$price_type_id)->where('item_id',$item_id)->first();
        if(!empty($query)){
            return $query->items_price;
        }else{
            return 0;
        }       
    }
    public static function checkPackageFeature($featureid=null,$packageid=null){
        $query = Feature::where('mainfeature_id',$featureid)->where('package_id',$packageid)->first();
        if(!empty($query)){
            return true;
        }else{
            return false;
        }       
    }
    public static function checkStepTransaction($steps=null,$id=null){
        $query = Catalog::where('id',$id)->first();
        if(!empty($query)){
            $array = json_decode($query['steps'],true);
            if(count((array)$array) > 0){
                foreach($array as $value){
                    if($value == $steps){
                        return true;
                        break;
                    }
                }
            }else{
                return false;
            }
        }else{
            return false;
        }
    }
    public static function checkPaymentOption($steps=null,$id=null){
        $query = Catalog::where('id',$id)->first();
        if(!empty($query)){
            $array = json_decode($query['payment_option'],true);
            if(count((array)$array) > 0){
                foreach($array as $value){
                    if($value == $steps){
                        return true;
                        break;
                    }
                }
            }else{
                return false;
            }
        }else{
            return false;
        }
    }
    public static function checkDeliveryOption($steps=null,$id=null){
        $query = Catalog::where('id',$id)->first();
        if(!empty($query)){
            $data = $query['delivery_option'];
            if($data == $steps){
                return true;
            }else{
                return false;
            }
        }else{
            return false;
        }
    }
    public static function checkOnlineTypeyOption($steps=null,$id=null){
        $query = Catalog::where('id',$id)->first();
        if(!empty($query)){
            $data = $query['online_type'];
            if($data == $steps){
                return true;
            }else{
                return false;
            }
        }else{
            return false;
        }
    }
    public static function checkPaymentMethodCatalog($steps=null,$id=null){
        $query = Catalog::where('id',$id)->first();
        if(!empty($query)){
            $array = json_decode($query['payment_mehod'],true);
            if(count((array)$array) > 0){
                foreach($array as $value){
                    if($value == $steps){
                        return true;
                        break;
                    }
                }
            }else{
                return false;
            }
        }else{
            return false;
        }
    }

    public static function getCatalogSubCategory($catalog=null,$category=null){
        $query = CatalogDetail::select('catalogdetail.*',
                                                'category.category_name',
                                                'subcategory.subcategory_name'
                                        )
                                        ->leftJoin('category','catalogdetail.category_id','=','category.id')
                                        ->leftJoin('subcategory','catalogdetail.subcategory_id','=','subcategory.id')
                                        ->where('catalogdetail.catalog_id',$catalog)
                                        ->where('category_id',$category)
                                        ->orderBy('subcategory_position')
                                        ->groupBy('subcategory_id')
                                        ->get();
        return $query;
    }
    public static function getCatalogItems($catalog=null,$category=null,$subcategory=null){
        $query = CatalogDetail::select('catalogdetail.*',
                                                'category.category_name',
                                                'subcategory.subcategory_name',
                                                'items.items_name'
                                        )
                                        ->leftJoin('category','catalogdetail.category_id','=','category.id')
                                        ->leftJoin('subcategory','catalogdetail.subcategory_id','=','subcategory.id')
                                        ->leftJoin('items','catalogdetail.item','=','items.id')
                                        ->where('catalogdetail.catalog_id',$catalog)
                                        ->where('category_id',$category)
                                        ->where('subcategory_id',$subcategory)
                                        ->orderBy('item_position')
                                        ->get();
        return $query;
    }
    public static function getCatalogSubCategoryItems($catalog=null,$category=null,$subcategory=null){
        $query = CatalogDetail::select('catalogdetail.*',
                                                'category.category_name',
                                                'subcategory.subcategory_name',
                                                'items.items_name'
                                        )
                                        ->leftJoin('category','catalogdetail.category_id','=','category.id')
                                        ->leftJoin('subcategory','catalogdetail.subcategory_id','=','subcategory.id')
                                        ->leftJoin('items','catalogdetail.item','=','items.id')
                                        ->where('catalogdetail.catalog_id',$catalog)
                                        ->where('category_id',$category)
                                        ->where('subcategory_id',$subcategory)
                                        ->orderBy('item_position')
                                        ->get();
        return $query;
    }
    public static function getItemByName($name,$field){
        $query = Items::where('items_name',$name)->first();
        return $query[$field];
    }
    public static function getTotalItems($catalog,$subcategory){
        $query = CatalogDetail::where('catalog_id',$catalog)->where('subcategory_id',$subcategory)->count();
        return $query;
    }
    public static function getItemCart($invoice=null,$category=null){
        $invoice = Invoice::where('invoice_number',$invoice)->first();
        $query = InvoiceDetail::where('category',$category)->where('invoiceid',$invoice['id'])->where('clone_data','N')->orderBy('id')->get();
        return $query;
    }
    public static function getTotalInvoice($invoice=null){
        $total = InvoiceDetail::where('invoiceid',$invoice)->sum(\DB::raw('(price - discount)*qty'));
        return $total;
    }
    public static function haveCatalog(){
        $query = Catalog::where('user_id',Auth::user()->id)->count();
        if($query > 0){
            return "True";
        }else{
            $alt = User::where('id',Auth::user()->id)->first();
            if($alt['catalog'] > 0){
                return "True";
            }else{
                return "False";
            }
        }
    }
    public static function getCatalogSession($field){
        $query = Catalog::where('id',Session::get('catalogsession'))->first();
        if(!empty($query)){
            return $query[$field];
        }
    }
    public static function getTotal($inv){
        $invoice = Invoice::where('id',$inv)->first();
        $grand = 0;
        $query = InvoiceDetail::where('invoiceid',$inv)->groupBy('category')->where('clone_data','N')->get();
        foreach($query as $item){
            $total = 0;
            $totaladdons = 0;
            foreach(getData::getItemCart($invoice['invoice_number'],$item['category']) as $listitem){
                $price = ($listitem['price']-$listitem['discount']) * $listitem['qty'];
                $total = $total + $price;
                if(getData::getInvoiceAddons($listitem['id'])->count() > 0){
                    foreach(getData::getInvoiceAddons($listitem['id']) as $addondata){
                        $priceaddons = $addondata['addon_qty']*getData::addonsPrice($addondata['single_addon'],$addondata['multiple_addon']);
                        $totaladdons = $totaladdons+$priceaddons;
                    }
                }
            }
            $grand = $grand +  $total + $totaladdons;
        }
        return $grand;
    }
    public static function getCountTransactionDay($date){
        if(Session::get('catalogsession') == 'All'){
            $invoice = Invoice::select('invoice.*','catalog.catalog_title','catalog.catalog_logo','catalog.catalog_username','catalog.domain')
                        ->leftJoin('catalog','invoice.catalog_id','=','catalog.id')
                        ->where('catalog.user_id',Auth::user()->id)
                        ->where('invoice.created_at','>=',explode(" ",$date)[0].' 00:00:00')
                        ->where('invoice.created_at','<=',explode(" ",$date)[0].' 23:59:59')
                        ->where('invoice.invoice_type','Permanent')
                        ->where('invoice.status','Completed')
                        ->count();
        }else{
            $catalog = Catalog::where('id',Session::get('catalogsession'))->orderBy('id','desc')->first();
            $invoice = Invoice::where('catalog_id',$catalog['id'])
                        ->where('created_at','>=',explode(" ",$date)[0].' 00:00:00')
                        ->where('created_at','<=',explode(" ",$date)[0].' 23:59:59')
                        ->where('invoice.invoice_type','Permanent')
                        ->where('invoice.status','Completed')
                        ->count();
        }
        return $invoice;
    }
    public static function getCountItemDay($item,$date){
        $user_id = Auth::user()->id;
        if(Auth::user()->parent_id){
            $user_id = Auth::user()->parent_id;
        }

        if(Session::get('catalogsession') == 'All'){
            $catalog = Catalog::where('user_id', $user_id)->pluck('id');
        }else{
            $catalog = Catalog::where('id', Session::get('catalogsession'))->orderBy('id','desc')->pluck('id');
        }

        // $catalog = Catalog::where('id',Session::get('catalogsession'))->orderBy('id','desc')->first();
        $item = InvoiceDetail::select('invoicedetail.*','invoice.invoice_number')
                                ->join('invoice','invoicedetail.invoiceid','invoice.id')
                                ->join('items','invoicedetail.item_id','items.id')
                                ->whereIn('invoice.catalog_id', $catalog)
                                ->where('invoice.status','Completed')
                                ->where('invoice.invoice_type','Permanent')
                                ->where('item_id',$item)
                                ->where('invoice.created_at','>=',explode(" ",$date)[0].' 00:00:00')
                                ->where('invoice.created_at','<=',explode(" ",$date)[0].' 23:59:59')
                                ->sum('qty');
        return $item;
    }
    public static function getCountItemMonth($item,$month,$year){
        $user_id = Auth::user()->id;
        if(Auth::user()->parent_id){
            $user_id = Auth::user()->parent_id;
        }

        if(Session::get('catalogsession') == 'All'){
            $catalog = Catalog::where('user_id', $user_id)->pluck('id');
        }else{
            $catalog = Catalog::where('id', Session::get('catalogsession'))->orderBy('id','desc')->pluck('id');
        }

        // $catalog = Catalog::where('id',Session::get('catalogsession'))->orderBy('id','desc')->first();
        $item = InvoiceDetail::select('invoicedetail.*','invoice.invoice_number')
                                ->join('invoice','invoicedetail.invoiceid','invoice.id')
                                ->join('items','invoicedetail.item_id','items.id')
                                ->whereIn('invoice.catalog_id', $catalog)
                                ->where('invoice.status','Completed')
                                ->where('invoice.invoice_type','Permanent')
                                ->where('item_id',$item)
                                ->whereMonth('invoice.created_at', '=', $month)
                                ->whereYear('invoice.created_at', '=', $year)
                                ->sum('qty');
        return $item;
    }
    public static function getTotalItemDay($item,$date){
        $catalog = Catalog::where('id',Session::get('catalogsession'))->orderBy('id','desc')->first();
        $item = InvoiceDetail::select('invoicedetail.*','invoice.invoice_number')
                                ->leftJoin('invoice','invoicedetail.invoiceid','invoice.id')
                                ->where('invoice.catalog_id',$catalog['id'])
                                ->where('item_id',$item)
                                ->where('invoice.created_at','>=',explode(" ",$date)[0].' 00:00:00')
                                ->where('invoice.created_at','<=',explode(" ",$date)[0].' 23:59:59')
                                ->get();
        $total = 0;
        foreach($item as $listitem){
            $total = $total + ($listitem['price']-$listitem['discount']) * $listitem['qty'];
        }
        return $total;
    }
    public static function getTotalItemMonth($item,$month,$year){
        $catalog = Catalog::where('id',Session::get('catalogsession'))->orderBy('id','desc')->first();
        $item = InvoiceDetail::select('invoicedetail.*','invoice.invoice_number')
                                ->leftJoin('invoice','invoicedetail.invoiceid','invoice.id')
                                ->where('invoice.catalog_id',$catalog['id'])
                                ->where('item_id',$item)
                                ->whereMonth('invoicedetail.created_at', '=', $month)
                                ->whereYear('invoicedetail.created_at', '=', $year)
                                ->get();
        $total = 0;
        foreach($item as $listitem){
            $total = $total + ($listitem['price']-$listitem['discount']) * $listitem['qty'];
        }
        return $total;
    }
    public static function getCountTransactionMonth($month,$year){
        if(Session::get('catalogsession') == 'All'){
            $invoice = Invoice::select('invoice.*','catalog.catalog_title','catalog.catalog_logo','catalog.catalog_username','catalog.domain')
                        ->leftJoin('catalog','invoice.catalog_id','=','catalog.id')
                        ->where('catalog.user_id',Auth::user()->id)
                        ->where('invoice.invoice_type','Permanent')
                        // ->where('invoice.status','<>','Order')
                        ->where('invoice.status','Completed')
                        ->whereMonth('invoice.created_at', '=', $month)
                        ->whereYear('invoice.created_at', '=', $year)
                        ->count();
        }else{
            $catalog = Catalog::where('id',Session::get('catalogsession'))->orderBy('id','desc')->first();
            $invoice = Invoice::where('catalog_id',$catalog['id'])
                        ->where('invoice.invoice_type','Permanent')
                        // ->where('invoice.status','<>','Order')
                        ->where('invoice.status','Completed')
                        ->whereMonth('created_at', '=', $month)
                        ->whereYear('created_at', '=', $year)
                        ->count();
        }
        return $invoice;
    }
    public static function getTotalTransactionDay($date){
        if(Session::get('catalogsession') == 'All'){
            $invoice =Invoice::select('invoice.*','catalog.catalog_title','catalog.catalog_logo','catalog.catalog_username','catalog.domain')
                        ->leftJoin('catalog','invoice.catalog_id','=','catalog.id')
                        ->where('catalog.user_id',Auth::user()->id)
                        ->where('invoice.invoice_type','Permanent')
                        ->where('invoice.status','Completed')
                        ->where('invoice.created_at','>=',explode(" ",$date)[0].' 00:00:00')
                        ->where('invoice.created_at','<=',explode(" ",$date)[0].' 23:59:59')
                        ->get();
        }else{
            $catalog = Catalog::where('id',Session::get('catalogsession'))->orderBy('id','desc')->first();
            $invoice = Invoice::where('catalog_id',$catalog['id'])
                        ->where('invoice.invoice_type','Permanent')
                        ->where('invoice.status','Completed')
                        ->where('created_at','>=',explode(" ",$date)[0].' 00:00:00')
                        ->where('created_at','<=',explode(" ",$date)[0].' 23:59:59')
                        ->get();
        }
        $total = 0;
        foreach($invoice as $value){
            $total = $total + getData::getTotal($value['id']);
        }
        return $total;
    }
    public static function getTotalTransactionMonth($month,$year){
        if(Session::get('catalogsession') == 'All'){
            $invoice = Invoice::select('invoice.*','catalog.catalog_title','catalog.catalog_logo','catalog.catalog_username','catalog.domain')
                        ->leftJoin('catalog','invoice.catalog_id','=','catalog.id')
                        ->where('catalog.user_id',Auth::user()->id)
                        ->where('invoice.invoice_type','Permanent')
                        ->where('invoice.status','Completed')
                        ->whereMonth('invoice.created_at', '=', $month)
                        ->whereYear('invoice.created_at', '=', $year)
                        ->get();
        }else{
            $catalog = Catalog::where('id',Session::get('catalogsession'))->orderBy('id','desc')->first();
            $invoice = Invoice::where('catalog_id',$catalog['id'])
                        ->where('invoice.invoice_type','Permanent')
                        ->where('invoice.status','Completed')
                        ->whereMonth('created_at', '=', $month)
                        ->whereYear('created_at', '=', $year)
                        ->get();
        }
        $total = 0;
        foreach($invoice as $value){
            $total = $total + getData::getTotal($value['id']);
        }
        return $total;
    }
    public static function generateInvoice($pending='N'){
        $query = Invoice::select('invoice.*','catalog.*')
                        ->leftJoin('catalog','invoice.catalog_id','catalog.id')
                        ->where('catalog_id',Session::get('catalogsession'))
                        ->where('status','<>','Order')
                        ->where('invoice_type','Temporary')
                        ->count();
        // if(getData::getCatalogSession('advance_payment') == 'Y'){
        //     $code='INV';
        // }else{
        //     $code='ORD';
        // }
        if($pending == 'N'){
            $code='INV';
        }else{
            $code='ORD';
        }
        if (!empty($query)) {
           $invoice = $code.Session::get('catalogsession').'-'.str_pad($query + 1, 8, "0", STR_PAD_LEFT);
        } else {
           $invoice = $code.Session::get('catalogsession').'-'.str_repeat(0,7).'1';
        }
        return $invoice;
    }

    public static function generateQueueId(){
        $query = Invoice::select('invoice.*','catalog.*')
                        ->leftJoin('catalog','invoice.catalog_id','catalog.id')
                        ->where('catalog_id',Session::get('catalogsession'))
                        ->whereDate('invoice.created_at', Carbon::today())
                        ->whereNotNull('queue_id')
                        ->count();

        $alphabet = range('A', 'Z');
        // echo $alphabet[3]; // returns D
        $code=$alphabet[date('j')];
                
        if (!empty($query)) {
           $queue_id = $code.'-'.str_pad($query + 1, 8, "0", STR_PAD_LEFT);
        } else {
           $queue_id = $code.'-'.str_repeat(0,7).'1';
        }
        return $queue_id;
    }

    public static function generateInvoiceComplete(){
        $query = Invoice::select('invoice.*','catalog.*')
                        ->leftJoin('catalog','invoice.catalog_id','catalog.id')
                        ->where('catalog_id',Session::get('catalogsession'))
                        ->where('status','<>','Order')
                        ->where('invoice_type','Permanent')
                        ->count();
        $code='INV';
        if (!empty($query)) {
           $invoice = $code.Session::get('catalogsession').'-'.str_pad($query + 1, 8, "0", STR_PAD_LEFT);
        } else {
           $invoice = $code.Session::get('catalogsession').'-'.str_repeat(0,7).'1';
        }
        return $invoice;
    }
    public static function getItemAddons($item=null,$category=null){
        // $query = ItemsDetail::select('items_detail.*',
        //                                         'category.category_name',
        //                                         'items.items_name',
        //                                         'items.items_price'
        //                                 )
        //                                 ->leftJoin('category','items_detail.category_id','=','category.id')
        //                                 ->leftJoin('items','items_detail.addon','=','items.id')
        //                                 ->where('item_id',$item)
        //                                 ->where('category_id',$category)
        //                                 ->get();
        $query = ItemAddon::where('user_id', Auth::user()->id)->where('item_id',$item)->orderBy('id', 'desc')->get();
        return $query;
    }
    public static function getInvoiceAddons($detail=null){
        $query = InvoiceAddons::where('invoicedetailid',$detail)
                                ->groupBy('row_group')
                                ->orderBy('id','desc')
                                ->get();
        return $query;
    }
    public static function getInvoiceAddonsSum($detail=null){
        $query = InvoiceAddons::where('invoicedetailid',$detail)
                                ->sum('addon_qty');
        return $query;
    }
    public static function getInvoiceAddonsQty($group=null){
        $query = InvoiceAddons::where('row_group',$group)->groupBy('row_group')->first();
        return $query['qty_group'];
    }
    public static function getInvoiceAddonsPrice($group=null){
        $query = InvoiceAddons::where('row_group',$group)->get();
        $price = 0;
        foreach ($query as $key => $value) {
            $price = $price + ($value['addon_qty']*$value['addon_price']);
        }
        return $price;
    }
    public static function addonsPrice($single,$multiple){
        
        $pricesingle=0;
        if(!empty($single)){
            $arraysingle=explode(',', $single);
            foreach($arraysingle as $valsingle){
                $pricesingle=$pricesingle+explode('-', $valsingle)[2];
            }
        }

        $pricemultiple=0;
        if(!empty($multiple)){
            $arraymultiple=explode(',', $multiple);
            foreach($arraymultiple as $valmultiple){
                $pricemultiple=$pricemultiple+explode('-', $valmultiple)[2];
            }
        }
        
        return $pricesingle+$pricemultiple;
    }
    public static function decodeAddons($param){
        if(!empty($param)){
            $array=explode(',', $param);
            $data=[];
            foreach($array as $val){
                $category = explode('-', $val)[0];
                $addon = explode('-', $val)[1];
                $price = explode('-', $val)[2];
                $data[]=getData::getCategory($category,'category_name').' : '.getData::getItem($addon,'items_name');
            }
            return implode(', ', $data);
        }
    }
    public static function getAddons($item){
        $query = ItemsDetail::select('items_detail.*',
                        'category.category_name',
                        'items.items_name',
                        'items.items_price')
                        ->leftJoin('items','items_detail.addon','=','items.id')
                        ->leftJoin('category','items_detail.category_id','=','category.id')
                        ->where('items.item_type','Add')
                        ->where('items.ready_stock','Y')
                        ->where('item_id',$item)
                        ->groupBy('category_id')
                        ->get();
        return $query;
    }
    public static function checkAddSingle($group,$category,$addon,$price){
        $maingroup = InvoiceAddons::where('row_group',$group)->first();
        $single = $category.'-'.$addon.'-'.$price;
        foreach(explode(',', $maingroup['single_addon']) as $val){
            if($val == $single){
                return true;
            }
        }
    }
    public static function checkAddMultiple($group,$category,$addon,$price){
        $maingroup = InvoiceAddons::where('row_group',$group)->first();
        $multiple = $category.'-'.$addon.'-'.$price;
        foreach(explode(',', $maingroup['multiple_addon']) as $val){
            if($val == $multiple){
                return true;
            }
        }
    }
    public static function getChildParent($parent=0,$res) {
        $query = User::where('parent_id',$parent)->get();
        if(count($query) > 0)
        {
            //$hasil .= "<tr>";
        }
        foreach($query as $h)
        {
            $res = $h['name'];
            $res = getData::getChildParent($h['id'],$res);
        }
        if(count($query) > 0)
        {
            //$hasil .= "</tr>";
        }
        return $res;
    }
    public static function countStock($item,$catalog){
        $query = ItemsStock::where('item_id',$item)
                                ->sum('stock');
        // if((Session::get('catalogsession')=='All')){
        //     $query = ItemsStock::where('item_id',$item)
        //                         ->sum('stock');
        // }
        // else{
        //     $query = ItemsStock::where('catalog',$catalog)
        //                         ->where('item_id',$item)
        //                         ->sum('stock');
        // }
        return $query;
    }
    public static function countStockBranch($item){
        $query = ItemsStock::where('catalog','>',0)
                            ->where('item_id',$item)
                            ->sum('stock');
        return $query;
    }
    public static function countStockBranchUsed($item){
        $query = InvoiceRecipe::where('item_id',$item)
                            ->where('checked','Y')
                            ->sum('recipe_qty');
        return $query;
    }
    public static function countAllVoucher($code){
        $query = Register::where('voucher_code',$code)->get();
        return $query->count();
    }
    public static function buttonClone($id,$item){
        $query = InvoiceDetail::where('id',$id)->where('item_id',$item)->first();
        return $query;
    }
    public static function checkCloneDetail($id){
        $query = InvoiceDetail::where('invoiceid',$id)->where('clone_data','N')->count();
        return $query;
    }

    public static function item($id)
    {
        try {
            $data = Items::findOrFail($id);
    
            return $data;
        } catch (\Throwable $th) {
            return null;
        }
    }
    
    public static function catalog($id)
    {
        try {
            $data = Catalog::findOrFail($id);
    
            return $data;
        } catch (\Throwable $th) {
            return null;
        }
    }

    public static function checkLicenseAggrement($value=null,$id=null){
        $query = Catalog::where('id',$id)->where('license_agreement', $value)->first();
        if ($query) {
            return 1;
        }else{
            return 0;
        }
    }
}