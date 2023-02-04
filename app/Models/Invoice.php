<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Http\Requests;

use App\Models\InvoiceDetail;
use App\Models\InvoiceAddons;
use App\Models\InvoiceRecipe;
use App\Models\Items;
use App\Models\Recipe;
use Illuminate\Support\Facades\Storage;

use myFunction;
use getData;
use Auth;
use Session;
use Image;
use Input;
use File;
use Mail;

class Invoice extends Model
{
    protected $table = 'invoice';
    
    public function catalog(){
        return $this->belongsTo('App\Models\Catalog', 'catalog_id');
	}

    public function invoiceDetail()
    {
        return $this->hasMany(InvoiceDetail::class);
    }

    public static function save_data($request){
        try {
            DB::transaction(function () use ($request) {
                $data=$request->all();
                if($data['invoice_number'] != ''){
                    $invoicedata = $data['invoice_number'];
                }else{
                    if(!Session::has('cartInvoice')){
                        Session::put('cartInvoice',Session::get('device_session'));
                    }
                    $invoicedata = Session::get('cartInvoice');
                }
                $getinvoice = Invoice::where('invoice_number',$invoicedata)->first();
                if(empty($getinvoice)){
                    $invoiceid = myFunction::id('invoice','id');
                    $var=new Invoice;
                    $var->id=$invoiceid;
                    $var->catalog_id=trim($data['catalog']);
                    $var->invoice_number=$invoicedata;
                    $var->via=trim($data['via']);
                    $var->status='Order';
                    if(getData::getCatalogSession('advance_payment') == 'N'){
                        $var->pending='Y';
                    }
                    $var->device_session=Session::get('device_session');
                    $var->save();
                }else{
                    $invoiceid = $getinvoice['id'];
                    // check device_session
                    if($getinvoice->device_session != Session::get('device_session')){
                        Invoice::where('invoice_number',$invoicedata)->update([
                            'device_session' => Session::get('device_session')
                        ]);
                    }
                    $getinvoice = Invoice::where('invoice_number',$invoicedata)->first();
                }
                $getdetail = InvoiceDetail::where('invoiceid',$invoiceid)->where('item',$data['item'])->first();
                if(empty($getdetail)){
                    $iddetail = myFunction::id('invoicedetail','id');
                    $qtydetail = $data['qty'];
                    $detail=new InvoiceDetail;
                    $detail->id=$iddetail;
                    $detail->invoiceid=$invoiceid;
                    $detail->category=$data['category'];
                    $detail->item_id=$data['item_id'];
                    $detail->item=$data['item'];
                    $detail->price=$data['price'];
                    $detail->discount=$data['discount'];
                    $detail->qty=$qtydetail;
                    $detail->note=trim($data['note']);
                    $detail->item_status='Order';
                    $detail->save();
                }else{
                    $iddetail = $getdetail['id'];
                    $qtydetail = $getdetail['qty']+$data['qty'];
                    InvoiceDetail::where('id',$getdetail['id'])->update([
                                                                'qty'=>$qtydetail,
                                                                'note'=>(!empty($data['note']))?trim($data['note']):$getdetail['note']
                                                            ]);
                }

                //Recipe Stock Items
                $recipeitem = Recipe::where('parent_id',$data['item_id'])->get();
                foreach($recipeitem as $vrecipeitem){
                    $recipe=new InvoiceRecipe;
                    $recipe->id=myFunction::id('invoicerecipe','id');
                    $recipe->reff_id=$iddetail;
                    $recipe->recipe_id=$vrecipeitem['id'];
                    $recipe->item_id=$vrecipeitem['item_id'];
                    $recipe->recipe_qty=$vrecipeitem['serving_size']*$data['qty'];
                    $recipe->note_recipe="Main";
                    $recipe->save();
                }
                //End

                $grouprow=date('YmdHis');
                // Addons
                if(!empty($data['arraddonsmultiple']) || !empty($data['arraddonssingle'])){
                    $addonsid = myFunction::id('invoiceaddons','id');

                    $singleadd = [];
                    if(!empty($data['arraddonssingle'])){
                        foreach(explode(',', $data['arraddonssingle']) as $arraddonssingle){
                            //Recipe Stock Single Addons
                            $itemsingle = explode('-', $arraddonssingle)[1];
                            $recipeitemsingle = Recipe::where('parent_id',$itemsingle)->get();
                            foreach($recipeitemsingle as $vrecipeitemsingle){
                                $recipesingle=new InvoiceRecipe;
                                $recipesingle->id=myFunction::id('invoicerecipe','id');
                                $recipesingle->reff_id=$addonsid;
                                $recipesingle->recipe_id=$vrecipeitemsingle['id'];
                                $recipesingle->item_id=$vrecipeitemsingle['item_id'];
                                $recipesingle->recipe_qty=$vrecipeitemsingle['serving_size']*$data['qty'];
                                $recipesingle->note_recipe="AddOn";
                                $recipesingle->save();
                            }
                            //End
                            $singleadd[]=$arraddonssingle;
                        }
                        $singleaddons = implode(',',$singleadd);
                    }else{
                        $singleaddons = null;
                    }

                    $multipleadd = [];
                    if(!empty($data['arraddonsmultiple'])){
                        foreach(explode(',', $data['arraddonsmultiple']) as $arraddonsmultiple){
                            //Recipe Stock Multiple Addons
                            $itemmultiple = explode('-', $arraddonsmultiple)[1];
                            $recipeitemmultiple = Recipe::where('parent_id',$itemmultiple)->get();
                            foreach($recipeitemmultiple as $vrecipeitemmultiple){
                                $recipemultiple=new InvoiceRecipe;
                                $recipemultiple->id=myFunction::id('invoicerecipe','id');
                                $recipemultiple->reff_id=$addonsid;
                                $recipemultiple->recipe_id=$vrecipeitemmultiple['id'];
                                $recipemultiple->item_id=$vrecipeitemmultiple['item_id'];
                                $recipemultiple->recipe_qty=$vrecipeitemmultiple['serving_size']*$data['qty'];
                                $recipemultiple->note_recipe="AddOn";
                                $recipemultiple->save();
                            }
                            //End
                            $multipleadd[]=$arraddonsmultiple;
                        }
                        $multipleaddons = implode(',',$multipleadd);
                    }else{
                        $multipleaddons = null;
                    }

                    $check = InvoiceAddons::where('invoiceid',$invoiceid)
                                                    ->where('invoicedetailid',$iddetail)
                                                    ->where('single_addon',$singleaddons)
                                                    ->where('multiple_addon',$multipleaddons)
                                                    ->first();   
                    if(empty($check)){
                        $multiple=new InvoiceAddons;
                        $multiple->id=$addonsid;
                        $multiple->invoiceid=$invoiceid;
                        $multiple->invoicedetailid=$iddetail;
                        $multiple->row_group=$grouprow;
                        $multiple->single_addon=$singleaddons;
                        $multiple->multiple_addon=$multipleaddons;
                        $multiple->addon_qty=$data['qty'];
                        $multiple->save();
                    }else{
                        InvoiceAddons::where('row_group',$check['row_group'])->update(['addon_qty'=>$check['addon_qty']+$data['qty']]);
                    }
                }

                Invoice::reCountAmount($invoiceid);
                //End
            });
         }
        catch(\Exception $e) {
            return false;
        }
        return true;
    }
    public static function reCountAmount($id){
        $invoice = Invoice::where('id', $id)->first();
        $item = InvoiceDetail::where('invoiceid', $id)->groupBy('category')->get();

        $grand = 0;
        $itemgroup= [];
        $gettax = 0;
        
        if(count($item) > 0){
            foreach($item as $item){
                $total = 0;
                $totaladdons = 0;

                foreach(getData::getItemCart($invoice['invoice_number'],$item['category']) as $listitem){
                    $price = ($listitem['price']-$listitem['discount']) * $listitem['qty'];
                    $total = $total + $price;
                    $itemgroup[]= $listitem['item'].' x '.$listitem['qty'];
                   
                    if(getData::getInvoiceAddons($listitem['id'])->count() > 0){
                        foreach(getData::getInvoiceAddons($listitem['id']) as $addondata){
                            $priceaddons = $addondata['addon_qty']*getData::addonsPrice($addondata['single_addon'],$addondata['multiple_addon']);
                            $totaladdons = $totaladdons+$priceaddons;
                        }
                    }
                }

                $grand = $grand + $total + $totaladdons;
            }

            $gettax = 0;
            if(getData::getCatalogSession('tax') > 0){
                $gettax = ($grand*getData::getCatalogSession('tax')) /100;
            }

            $grand = $grand + $gettax;

            $invoice->amount = $grand;

            if($invoice->qr_string){
                $new_qr_string = str_replace($invoice->amount, $grand, $invoice->qr_string);
                $invoice->qr_string = $new_qr_string;
            }

            $invoice->save();
            // Invoice::where('id', $id)->update([
            //     'amount'=>$grand
            // ]);
        }
    }
    public static function save_data_last($request){
        try {
            DB::transaction(function () use ($request) {
                $data=$request->all();
                if($data['invoice_number'] != ''){
                    $invoicedata = $data['invoice_number'];
                }else{
                    if(!Session::has('cartInvoice')){
                        Session::put('cartInvoice',Session::get('device_session'));
                    }
                    $invoicedata = Session::get('cartInvoice');
                }
                $getinvoice = Invoice::where('invoice_number',$invoicedata)->first();
                if(empty($getinvoice)){
                    $invoiceid = myFunction::id('invoice','id');
                    $var=new Invoice;
                    $var->id=$invoiceid;
                    $var->catalog_id=trim($data['catalog']);
                    $var->invoice_number=$invoicedata;
                    $var->via=trim($data['via']);
                    $var->status='Order';
                    if(getData::getCatalogSession('advance_payment') == 'N'){
                        $var->pending='Y';
                        $var->invoice_type='Temporary';
                    }
                    $var->device_session=Session::get('device_session');
                    $var->save();
                }else{
                    $invoiceid = $getinvoice['id'];
                    // check device_session
                    if($getinvoice->device_session != Session::get('device_session')){
                        Invoice::where('invoice_number',$invoicedata)->update([
                            'device_session' => Session::get('device_session')
                        ]);
                    }
                    $getinvoice = Invoice::where('invoice_number',$invoicedata)->first();                    
                }

                for($i=0;$i<$data['qty'];$i++){
                    $iddetail = myFunction::id('invoicedetail','id');
                    $detail=new InvoiceDetail;
                    $detail->id=$iddetail;
                    $detail->invoiceid=$invoiceid;
                    $detail->category=$data['category'];
                    $detail->item_id=$data['item_id'];
                    $detail->item=$data['item'];
                    $detail->price=$data['price'];
                    $detail->discount=$data['discount'];
                    $detail->qty=1;
                    $detail->note=trim($data['note']);
                    $detail->item_status='Order';
                    $detail->save();

                    //Recipe Stock Items
                    $recipeitem = Recipe::where('parent_id',$data['item_id'])->get();
                    foreach($recipeitem as $vrecipeitem){
                        $recipe=new InvoiceRecipe;
                        $recipe->id=myFunction::id('invoicerecipe','id');
                        $recipe->reff_id=$iddetail;
                        $recipe->recipe_id=$vrecipeitem['id'];
                        $recipe->item_id=$vrecipeitem['item_id'];
                        $recipe->recipe_qty=$vrecipeitem['serving_size']*1;
                        $recipe->note_recipe="Main";
                        $recipe->save();
                    }
                    //End

                    $grouprow=date('YmdHis');
                    // Addons
                    if(!empty($data['arraddonsmultiple']) || !empty($data['arraddonssingle'])){
                        $addonsid = myFunction::id('invoiceaddons','id');

                        $singleadd = [];
                        if(!empty($data['arraddonssingle'])){
                            foreach(explode(',', $data['arraddonssingle']) as $arraddonssingle){
                                //Recipe Stock Single Addons
                                $itemsingle = explode('-', $arraddonssingle)[1];
                                $recipeitemsingle = Recipe::where('parent_id',$itemsingle)->get();
                                foreach($recipeitemsingle as $vrecipeitemsingle){
                                    $recipesingle=new InvoiceRecipe;
                                    $recipesingle->id=myFunction::id('invoicerecipe','id');
                                    $recipesingle->reff_id=$addonsid;
                                    $recipesingle->recipe_id=$vrecipeitemsingle['id'];
                                    $recipesingle->item_id=$vrecipeitemsingle['item_id'];
                                    $recipesingle->recipe_qty=$vrecipeitemsingle['serving_size']*1;
                                    $recipesingle->note_recipe="AddOn";
                                    $recipesingle->save();
                                }
                                //End
                                $singleadd[]=$arraddonssingle;
                            }
                            $singleaddons = implode(',',$singleadd);
                        }else{
                            $singleaddons = null;
                        }

                        $multipleadd = [];
                        if(!empty($data['arraddonsmultiple'])){
                            foreach(explode(',', $data['arraddonsmultiple']) as $arraddonsmultiple){
                                //Recipe Stock Multiple Addons
                                $itemmultiple = explode('-', $arraddonsmultiple)[1];
                                $recipeitemmultiple = Recipe::where('parent_id',$itemmultiple)->get();
                                foreach($recipeitemmultiple as $vrecipeitemmultiple){
                                    $recipemultiple=new InvoiceRecipe;
                                    $recipemultiple->id=myFunction::id('invoicerecipe','id');
                                    $recipemultiple->reff_id=$addonsid;
                                    $recipemultiple->recipe_id=$vrecipeitemmultiple['id'];
                                    $recipemultiple->item_id=$vrecipeitemmultiple['item_id'];
                                    $recipemultiple->recipe_qty=$vrecipeitemmultiple['serving_size']*1;
                                    $recipemultiple->note_recipe="AddOn";
                                    $recipemultiple->save();
                                }
                                //End
                                $multipleadd[]=$arraddonsmultiple;
                            }
                            $multipleaddons = implode(',',$multipleadd);
                        }else{
                            $multipleaddons = null;
                        }

                        $check = InvoiceAddons::where('invoiceid',$invoiceid)
                                                        ->where('invoicedetailid',$iddetail)
                                                        ->where('single_addon',$singleaddons)
                                                        ->where('multiple_addon',$multipleaddons)
                                                        ->first();   
                        if(empty($check)){
                            $multiple=new InvoiceAddons;
                            $multiple->id=$addonsid;
                            $multiple->invoiceid=$invoiceid;
                            $multiple->invoicedetailid=$iddetail;
                            $multiple->row_group=$grouprow.$i;
                            //$multiple->row_group=$grouprow;
                            $multiple->single_addon=$singleaddons;
                            $multiple->multiple_addon=$multipleaddons;
                            $multiple->addon_qty=1;
                            $multiple->save();
                        }else{
                            InvoiceAddons::where('row_group',$check['row_group'])->update(['addon_qty'=>$check['addon_qty']+1]);
                        }
                    }
                    //End
                }
                Invoice::reCountAmount($invoiceid);
            });
         }
        catch(\Exception $e) {
            return false;
        }
        return true;
    }
    public static function cancel_order($request){
        try {
            DB::transaction(function () use ($request) {
                $data=$request->all();
                $array=['status'=>'Cancel',
                        'note_order'=>$data['note_order']];
                Invoice::where('invoice_number',$data['invoice_number'])->update($array);
                $invoice = Invoice::where('invoice_number',$data['invoice_number'])->first();
                InvoiceDetail::where('invoiceid',$invoice['id'])->update(['item_status'=>'Cancel']);
                $inv = Invoice::where('invoice_number',$data['invoice_number'])->first();
                $ch = curl_init(); 
                curl_setopt($ch, CURLOPT_URL, myFunction::getMain().'/cart/notif');
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
                curl_setopt($ch, CURLOPT_POSTFIELDS, ['invoice'=>$data['invoice_number'],'status'=>'Cancel','note'=>$data['note_order']]);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
                $output = curl_exec($ch); 
                curl_close($ch);
                //Invoice::sendEmail($invoice['id']);
            });
         }
        catch(\Exception $e) {
            return false;
        }
        return true;
    }
    public static function delete_data(){
        try {
            DB::transaction(function () {
                $invoice = Invoice::where('invoice_number',Session::get('cartInvoice'))->first();
                $invdetail = InvoiceDetail::where('invoiceid',$invoice['id'])->get();
                foreach($invdetail as $vinvdetail){
                    $addons = InvoiceAddons::where('invoicedetailid',$vinvdetail['id'])->get();
                    foreach($addons as $vaddons){
                        InvoiceRecipe::where('reff_id',$vaddons['id'])->where('note_recipe','AddOn')->delete();
                    }
                    InvoiceDetail::where('id',$vinvdetail['id'])->delete();
                    InvoiceRecipe::where('reff_id',$vinvdetail['id'])->where('note_recipe','Main')->delete();
                }
                Invoice::where('invoice_number',Session::get('cartInvoice'))->delete();
            });
         }
        catch(\Exception $e) {
            return false;
        }
        return true;
    }
    public static function update_status($invoice,$status,$lunas){
        try {
            DB::transaction(function () use ($invoice,$status,$lunas) {
                $inv = Invoice::where('invoice_number',$invoice)->first();
                Invoice::where('invoice_number',$invoice)->update(['status'=>$status, 'lunas'=>$lunas]);
                InvoiceDetail::where('invoiceid',$inv['id'])->update(['item_status'=>$status]);
                if($status == 'Completed' and $inv['pending']=='N'){
                    $invoice = Invoice::where('invoice_number',$invoice)->first();
                    $invoicedetail = InvoiceDetail::where('invoiceid',$invoice['id'])->get();
                    foreach($invoicedetail as $value){
                        $item = Items::where('id',$value['item_id'])->first();
                        Items::where('id',$value['item_id'])->update(['sell'=>$item['sell']+$value['qty']]);

                        //Addons
                        $addons = InvoiceAddons::where('invoicedetailid',$value['id'])->get();
                        if($addons->count() > 0){
                            foreach($addons as $vaddons){
                                if(!empty($vaddons['single_addon'])){
                                    $single = explode(',', $vaddons['single_addon']);
                                    foreach ($single as $vsingle) {
                                        $getitemsingle = explode('-', $vsingle)[1];
                                        $itemsingle = Items::where('id',$getitemsingle)->first();
                                        Items::where('id',$getitemsingle)->update(['sell'=>$itemsingle['sell']+$vaddons['addon_qty']]);
                                    }
                                }
                                if(!empty($vaddons['multiple_addon'])){
                                    $multiple = explode(',', $vaddons['multiple_addon']);
                                    foreach ($multiple as $vmultiple) {
                                        $getitemmultiple = explode('-', $vmultiple)[1];
                                        $itemmultiple = Items::where('id',$getitemmultiple)->first();
                                        Items::where('id',$getitemmultiple)->update(['sell'=>$itemmultiple['sell']+$vaddons['addon_qty']]);
                                    }
                                }
                            }
                        }
                        //End Addons
                    }
                }
                $ch = curl_init(); 
                curl_setopt($ch, CURLOPT_URL, myFunction::getMain().'/cart/notif');
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
                curl_setopt($ch, CURLOPT_POSTFIELDS, ['invoice'=>$inv['invoice_number'],'status'=>$inv['status'],'note'=>'Order '.$inv['status']]);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
                $output = curl_exec($ch); 
                curl_close($ch);
                //Invoice::sendEmail($invoice['id']);
            });
         }
        catch(\Exception $e) {
            dd($e);
            return false;
        }
        return true;
    }
    public static function checkout_data($request){
        $invoice = false;

        try {
            DB::transaction(function () use ($request, &$invoice) {
                if($request->input('payment_method') == 2){
                    if($request->online){
                        $urlimage=$request->imagefile;
                    }
                    else{
                        // $mainpath = myFunction::pathAsset();
                        $subpath = 'confirmation';
                        // $path = $mainpath.$subpath;
                        // File::isDirectory($path) or File::makeDirectory($path, 0777, true, true);

                        // $imagefile = Image::make($_FILES['imagefile']['tmp_name']);
                        // $name=Session::get('cartInvoice');
                        // $extension = $request->file('imagefile')->getClientOriginalExtension();
                        // $imagefile->save($path.'/'.$name.'.'.$extension);
                        // $urlimage=myFunction::getProtocol().$subpath.'/'.$name.'.'.$extension;

                        if($request->hasFile('imagefile')){
                            $file = $request->file('imagefile');
                            $filename=Session::get('cartInvoice');
                            $extension = $file->extension();
                            $filenametostore = $filename.'.'.$extension;
                            Storage::disk('s3')->putFileAs($subpath, $file, $filenametostore);
                            $urlimage = Storage::disk('s3')->url($subpath.'/'.$filenametostore);
                        }
                    }
                }
                if($request->input('pendingstatus')=='N'){
                    $numinvoice = getData::generateInvoiceComplete();
                }else{
                    $numinvoice = getData::generateInvoice($request->input('pendingstatus'));
                }
                $queue_id = getData::generateQueueId();
                Invoice::where('invoice_number',Session::get('cartInvoice'))->update([
                    'invoice_number'=>$numinvoice,
                    'pending'=>($request->input('pendingstatus')=='Y')?'Y':'N',
                    'invoice_type'=>($request->input('pendingstatus')=='Y')?'Temporary':'Permanent',
                    'status'=>'Completed',
                    'tax'=>getData::getCatalogSession('tax'),
                    'position'=>($request->has('position'))?$request->input('position'):$request->input('result_position'),
                    'transfer_image'=>($request->input('payment_method') == 2)?$urlimage:'',
                    'payment_method'=>$request->input('payment_method'),
                    'amount'=>$request->input('amount'),
                    'invoice_type_id'=>$request->input('invoice_type_id'),
                    'email'=>$request->input('inv_email'),
                    'queue_id'=>$queue_id
                ]);

                // Invoice::where('device_session',Session::get('cartInvoice'))
                // ->where('invoice_number', '<>', $numinvoice)
                // ->update([
                //     'device_session'=>null
                // ]);
                
                $invoice = Invoice::where('invoice_number',$numinvoice)->first();
                InvoiceDetail::where('invoiceid',$invoice['id'])->update(['item_status'=>'Completed']);
                // $invoicedetail = InvoiceDetail::where('invoiceid',$invoice['id'])->get();
                // foreach($invoicedetail as $value){
                //     $item = Items::where('id',$value['item_id'])->first();
                //     Items::where('id',$value['item_id'])->update(['sell'=>$item['sell']+$value['qty']]);
                // }
                //Invoice::sendEmail($invoice['id']);
            });
        }
        catch(\Exception $e) {
            return false;
        }
        return $invoice;
    }
    public static function complete_pending($request){
        try {
            DB::transaction(function () use ($request) {
                $invoice = Invoice::where('id',$request->input('id'))->first();
                if($request->input('payment_method') == 2){
                    if($request->online){
                        $urlimage=$request->imagefile;
                    }
                    else{
                        // $mainpath = myFunction::pathAsset();
                        $subpath = 'confirmation';
                        // $path = $mainpath.$subpath;
                        // File::isDirectory($path) or File::makeDirectory($path, 0777, true, true);

                        // $imagefile = Image::make($_FILES['imagefile']['tmp_name']);
                        // $name=$invoice['invoice_number'];
                        // $extension = $request->file('imagefile')->getClientOriginalExtension();
                        // $imagefile->save($path.'/'.$name.'.'.$extension);
                        // $urlimage=myFunction::getProtocol().$subpath.'/'.$name.'.'.$extension;

                        if($request->hasFile('imagefile')){
                            $file = $request->file('imagefile');
                            $filename=$invoice['invoice_number'];
                            $extension = $file->extension();
                            $filenametostore = $filename.'.'.$extension;
                            Storage::disk('s3')->putFileAs($subpath, $file, $filenametostore);
                            $urlimage = Storage::disk('s3')->url($subpath.'/'.$filenametostore);
                        }
                    }
                }
                Invoice::where('id',$request->input('id'))->update(['invoice_number'=>getData::generateInvoiceComplete(),
                                                        'pending'=>'N',
                                                        'amount'=>$request->input('amount'),
                                                        'payment_method'=>$request->input('payment_method'),
                                                        'transfer_image'=>($request->input('payment_method') == 2)?$urlimage:'',
                                                        'status'=>'Completed',
                                                        'invoice_type'=>'Permanent',
                                                    ]);
                $invoicedetail = InvoiceDetail::where('invoiceid',$invoice['id'])->get();
                foreach($invoicedetail as $value){
                    $item = Items::where('id',$value['item_id'])->first();
                    Items::where('id',$value['item_id'])->update(['sell'=>$item['sell']+$value['qty']]);
                }
                $ch = curl_init(); 
                curl_setopt($ch, CURLOPT_URL, myFunction::getMain().'/cart/notif');
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
                curl_setopt($ch, CURLOPT_POSTFIELDS, ['invoice'=>$invoice['invoice_number'],'status'=>$invoice['status'],'note'=>'Order '.$invoice['status']]);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
                $output = curl_exec($ch); 
                curl_close($ch);
                //Invoice::sendEmail($invoice['id']);
            });
         }
        catch(\Exception $e) {
            return false;
        }
        return true;
    }
    public static function sendEmail($id){
        $invoice = Invoice::where('id',$id)->first();
        $item = InvoiceDetail::where('invoiceid', $invoice['id'])
            ->groupBy('category')
            ->orderBy('id')
            ->get();
        $disp = array(
            'email'=>$invoice['email'] ? $invoice['email'] : getData::getCatalogSession('email_contact'),
            // 'name'=>getData::getCatalogSession('catalog_title'),
            'name'=>'',
            'namapengirim'=>'Scaneat Support',
            'emailpengirim'=>'admin@scaneat.id',
            'subject'=>"Order Invoice ".$invoice['invoice_number']
        );
        $content = array(
            'invoice' => $invoice,
            'item' => $item
        );
        // Mail::send('pages.email.transaction', $content, function($message) use ($disp)
        // {
        //     $message->from($disp['emailpengirim'], $disp['namapengirim']);
        //     $message->to($disp['email'], $disp['name'])->subject($disp['subject']);
		// 	$message->cc(getData::getCatalogSession('email_contact'));
        // });

        $api_token='0ba0f6d61c459fde8afaedd923c8ae6f'; //silahkan copy dari api token mailketing
		$from_name=$disp['namapengirim']; //nama pengirim
		$from_email=$disp['emailpengirim']; //email pengirim
		$subject=$disp['subject']; //judul email
		// $content=view('pages.email.transaction', $content)->render(); //isi email format text / html
		$recipient=getData::getCatalogSession('email_contact'); //penerima email
		$params = [
		  'from_name' => $from_name,
		  'from_email' => $from_email,
		  'recipient' => $recipient,
		  'subject' => $subject,
		  'content' => view('pages.email.transaction', $content)->render(),
		  // 'attach1' => 'direct url file httxxx/xxx/xx.pdf',
		  // 'attach2' => 'direct url file httxxx/xxx/xx.pdf',
		  // 'attach2' => 'direct url file httxxx/xxx/xx.pdf',
		  'api_token' => $api_token
		  ];
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL,"https://api.mailketing.co.id/api/v1/send");
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params));
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$output = curl_exec ($ch); 
		// print_r($output);
		curl_close ($ch);

        $api_token='0ba0f6d61c459fde8afaedd923c8ae6f'; //silahkan copy dari api token mailketing
		$from_name=$disp['namapengirim']; //nama pengirim
		$from_email=$disp['emailpengirim']; //email pengirim
		$subject=$disp['subject']; //judul email
		// $content=view('pages.email.transaction', $content)->render(); //isi email format text / html
		$recipient=$disp['email']; //penerima email
		$params = [
		  'from_name' => $from_name,
		  'from_email' => $from_email,
		  'recipient' => $recipient,
		  'subject' => $subject,
		  'content' => view('pages.email.transaction', $content)->render(),
		  // 'attach1' => 'direct url file httxxx/xxx/xx.pdf',
		  // 'attach2' => 'direct url file httxxx/xxx/xx.pdf',
		  // 'attach2' => 'direct url file httxxx/xxx/xx.pdf',
		  'api_token' => $api_token
		  ];
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL,"https://api.mailketing.co.id/api/v1/send");
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params));
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$output = curl_exec ($ch); 
		// print_r($output);
		curl_close ($ch);
    }
}
