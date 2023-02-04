<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Http\Requests;

use App\Models\Invoice;
use App\Models\InvoiceRecipe;
use App\Models\InvoiceAddons;
use App\Models\Items;
use App\Models\Recipe;

use App\Helper\myFunction;
use Auth;
use Session;
use getData;

class InvoiceDetail extends Model
{
    protected $table = 'invoicedetail';

    public static function delete_data($id){
        try {
            DB::transaction(function () use ($id) {
                $detail = InvoiceDetail::where('id',$id)->first();

                $recipe = InvoiceRecipe::where('reff_id',$detail['parent_id'])
                                        ->where('note_recipe','Main')
                                        ->get();
                // Main Recipe
                foreach ($recipe as $vrecipe) {
                    InvoiceRecipe::where('id',$vrecipe['id'])->update(['checked'=>'Y']);
                }
                // End
                $addons = InvoiceAddons::where('invoicedetailid',$detail['parent_id'])
                                        ->get();

                // Recipe AddOns
                foreach ($addons as $vaddons) {
                    $addonrecipe = InvoiceRecipe::where('reff_id',$vaddons['id'])
                                        ->where('note_recipe','AddOn')
                                        ->get();
                    foreach($addonrecipe as $vaddonrecipe){
                        InvoiceRecipe::where('id',$vaddonrecipe['id'])->update(['checked'=>'Y']);
                    }
                }
                // End

                if(!empty($detail['parent_id'])){
                    InvoiceDetail::where('id',$detail['parent_id'])->update(['clone_data'=>'N']);
                }
                
                $addons = InvoiceAddons::where('invoicedetailid',$id)->get();
                foreach($addons as $vaddons){
                    InvoiceRecipe::where('reff_id',$vaddons['id'])->where('note_recipe','AddOn')->delete();
                }
                InvoiceDetail::where('id',$id)->delete();
                InvoiceRecipe::where('reff_id',$id)->where('note_recipe','Main')->delete();
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
                InvoiceDetail::where('id',$request->input('id'))->update([
                        'qty'=>$request->input('qty'),
                        'note'=>$request->input('note')
                    ]);
                //Recipe Stock Items
                $detailtrans = InvoiceDetail::where('id',$request->input('id'))->first();
                $recipeitem = Recipe::where('parent_id',$detailtrans['item_id'])->get();
                foreach($recipeitem as $vrecipeitem){
                    InvoiceRecipe::where('reff_id',$request->input('id'))->where('item_id',$vrecipeitem['item_id'])->where('note_recipe','Main')->delete();
                    $recipe=new InvoiceRecipe;
                    $recipe->id=myFunction::id('invoicerecipe','id');
                    $recipe->reff_id=$request->input('id');
                    $recipe->recipe_id=$vrecipeitem['id'];
                    $recipe->item_id=$vrecipeitem['item_id'];
                    $recipe->recipe_qty=$vrecipeitem['serving_size']*$request->input('qty');
                    $recipe->note_recipe="Main";
                    $recipe->save();
                }
                //End
            });
         }
        catch(\Exception $e) {
            return false;
        }
        return true;
    }
    public static function update_data_back_payment($request){
        try {
            DB::transaction(function () use ($request) {
                $invoice = Invoice::where('id', $request->input('id'))->first();

                $data['amount'] = $request->input('grand');
                if($invoice->payment_method == 2){
                    $data['qr_string'] = 1;
                }

                Invoice::where('id', $request->input('id'))->update($data);

                InvoiceDetail::where('invoiceid',$request->input('id'))
                                ->where('item_status','Order')
                                ->update([
                                    'item_status'=>'Completed'
                                ]);
            });
         }
        catch(\Exception $e) {
            return false;
        }
        return true;
    }
    public static function clone_data($item,$detailinvoice,$invoice){
        try {
            DB::transaction(function () use ($item,$detailinvoice,$invoice) {
                InvoiceDetail::where('id',$detailinvoice)
                                        ->where('item_id',$item)
                                        ->update(['clone_data'=>'Y']);
                $invoicedetail = InvoiceDetail::where('id',$detailinvoice)
                                        ->where('item_id',$item)
                                        ->first();
                $oldinvoice = Invoice::where('id',$invoicedetail['invoiceid'])->first();
                $addons = InvoiceAddons::where('invoicedetailid',$detailinvoice)
                                        ->get();
                $recipe = InvoiceRecipe::where('reff_id',$detailinvoice)
                                        ->where('note_recipe','Main')
                                        ->get();
                $getinvoice = Invoice::where('invoice_number',$invoice)->first();
                $item_data = Items::where('id',$item)->first();
                if(empty($getinvoice)){
                    $invoiceid = myFunction::id('invoice','id');
                    $var=new Invoice;
                    $var->id=$invoiceid;
                    $var->catalog_id=$oldinvoice['catalog_id'];
                    $var->invoice_number=$invoice;
                    $var->via='POS';
                    $var->status='Order';
                    $var->pending='Y';
                    $var->invoice_type='Clone';
                    $var->tax=$oldinvoice['tax'];
                    $var->clone_invoice=$invoicedetail['invoiceid'];
                    //$var->device_session=Session::get('device_session');
                    $var->save();
                }else{
                    $invoiceid = $getinvoice['id'];
                }

                // Detail
                $iddetail = myFunction::id('invoicedetail','id');
                $detail=new InvoiceDetail;
                $detail->id=$iddetail;
                $detail->invoiceid=$invoiceid;
                $detail->category=$invoicedetail['category'];
                $detail->item_id=$invoicedetail['item_id'];
                $detail->item=$invoicedetail['item'];
                $detail->price=$invoicedetail['price'];
                $detail->hpp=$invoicedetail['hpp'];
                // $detail->hpp=$item_data?$item_data->hpp:null;
                $detail->discount=$invoicedetail['discount'];
                $detail->qty=$invoicedetail['qty'];
                $detail->note=$invoicedetail['note'];
                $detail->item_status=$invoicedetail['item_status'];
                $detail->parent_id=$detailinvoice;
                $detail->save();
                // End

                // Main Recipe
                foreach ($recipe as $vrecipe) {
                    InvoiceRecipe::where('id',$vrecipe['id'])->update(['checked'=>'N']);
                    $recipe=new InvoiceRecipe;
                    $recipe->id=myFunction::id('invoicerecipe','id');
                    $recipe->reff_id=$iddetail;
                    $recipe->recipe_id=$vrecipe['recipe_id'];
                    $recipe->item_id=$vrecipe['item_id'];
                    $recipe->recipe_qty=$vrecipe['recipe_qty']*1;
                    $recipe->note_recipe="Main";
                    $recipe->checked="Y";
                    $recipe->save();
                }
                // End

                // AddOns
                foreach ($addons as $vaddons) {
                    $addonid = myFunction::id('invoiceaddons','id');
                    $addons=new InvoiceAddons;
                    $addons->id=$addonid;
                    $addons->invoiceid=$invoiceid;
                    $addons->invoicedetailid=$iddetail;
                    $addons->row_group=$vaddons['row_group'];
                    $addons->single_addon=$vaddons['single_addon'];
                    $addons->multiple_addon=$vaddons['multiple_addon'];
                    $addons->addon_qty=$vaddons['addon_qty'];
                    $addons->save();

                    $addonrecipe = InvoiceRecipe::where('reff_id',$vaddons['id'])
                                        ->where('note_recipe','AddOn')
                                        ->get();
                    foreach($addonrecipe as $vaddonrecipe){
                        InvoiceRecipe::where('id',$vaddonrecipe['id'])->update(['checked'=>'N']);
                        $recipeadd=new InvoiceRecipe;
                        $recipeadd->id=myFunction::id('invoicerecipe','id');
                        $recipeadd->reff_id=$addonid;
                        $recipeadd->recipe_id=$vaddonrecipe['recipe_id'];
                        $recipeadd->item_id=$vaddonrecipe['item_id'];
                        $recipeadd->recipe_qty=$vaddonrecipe['recipe_qty']*1;
                        $recipeadd->note_recipe="AddOn";
                        $recipeadd->checked="Y";
                        $recipeadd->save();
                    }
                }
                // End
            });
         }
        catch(\Exception $e) {
            return false;
        }
        return true;
    }

    public function item()
    {
        return $this->belongsTo(Items::class, 'item_id', 'id');
    }

    public function invoice()
    {
        return $this->belongsTo(Invoice::class, 'invoiceid', 'id');
    }
}
