<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Http\Requests;

use App\Helper\myFunction;

use App\Models\Invoice;
use App\Models\InvoiceDetail;
use App\Models\Items;
use App\Models\InvoiceRecipe;
use App\Models\Recipe;

use Image;
use Input;
use File;
use Auth;
use getData;

class InvoiceAddons extends Model
{
    protected $table = 'invoiceaddons';
    public static function update_data($request){
        try {
            DB::transaction(function () use ($request) {
                $data=$request->all();
                $groupdata = InvoiceAddons::where('row_group',$data['group'])->first();
                $detailinvoice = InvoiceDetail::where('id',$data['detailid'])->first();
                $invoice = Invoice::where('id',$detailinvoice['invoiceid'])->first();
                $invoiceid = $invoice['id'];
                $invoicenumber = $invoice['invoice_number'];

                
                if($data['group'] > 0){
                    $qtydetail = ($detailinvoice['qty']+$data['qty'])-$groupdata['addon_qty'];
                    InvoiceDetail::where('id',$data['detailid'])->update(['qty'=>$qtydetail]);
                    InvoiceAddons::where('row_group',$data['group'])->delete();
                }else{
                    $qtydetail = ($detailinvoice['qty']+$data['qty'])-$rest;
                    $rest = $detailinvoice['qty'] - getData::getInvoiceAddonsSum($data['detailid']);
                    InvoiceDetail::where('id',$data['detailid'])->update(['qty'=>$qtydetail]);
                }

                //Recipe Stock Items
                $detailtrans = InvoiceDetail::where('id',$data['detailid'])->first();
                $recipeitem = Recipe::where('parent_id',$detailtrans['item_id'])->get();
                foreach($recipeitem as $vrecipeitem){
                    InvoiceRecipe::where('reff_id',$data['detailid'])
                                ->where('item_id',$vrecipeitem['item_id'])
                                ->where('note_recipe','Main')
                                ->delete();
                    $recipe=new InvoiceRecipe;
                    $recipe->id=myFunction::id('invoicerecipe','id');
                    $recipe->reff_id=$data['detailid'];
                    $recipe->recipe_id=$vrecipeitem['id'];
                    $recipe->item_id=$vrecipeitem['item_id'];
                    $recipe->recipe_qty=$vrecipeitem['serving_size']*$qtydetail;
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
                                InvoiceRecipe::where('reff_id',$groupdata['id'])->where('item_id',$vrecipeitemsingle['item_id'])->delete();
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
                                InvoiceRecipe::where('reff_id',$groupdata['id'])->where('item_id',$vrecipeitemmultiple['item_id'])->delete();
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
                                                    ->where('invoicedetailid',$data['detailid'])
                                                    ->where('single_addon',$singleaddons)
                                                    ->where('multiple_addon',$multipleaddons)
                                                    ->first();   
                    if(empty($check)){
                        $multiple=new InvoiceAddons;
                        $multiple->id=$addonsid;
                        $multiple->invoiceid=$invoiceid;
                        $multiple->invoicedetailid=$data['detailid'];
                        $multiple->row_group=$grouprow;
                        $multiple->single_addon=$singleaddons;
                        $multiple->multiple_addon=$multipleaddons;
                        $multiple->addon_qty=$data['qty'];
                        $multiple->save();
                    }else{
                        InvoiceAddons::where('row_group',$check['row_group'])->update(['addon_qty'=>$check['addon_qty']+$data['qty']]);
                    }
                }

                //End


                $ch = curl_init(); 
                curl_setopt($ch, CURLOPT_URL, \URL::to('/cms/notif/'.$invoicenumber.'/None'));
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
                $output = curl_exec($ch); 
                curl_close($ch);

            });
         }
        catch(\Exception $e) {
            return false;
        }
        return true;
    }
    public static function delete_addon($detail,$group){
        try {
            DB::transaction(function () use ($detail,$group) {
                $groupdata = InvoiceAddons::where('row_group',$group)->first();
                $detailinvoice = InvoiceDetail::where('id',$detail)->first();
                $invoice = Invoice::where('id',$detailinvoice['invoiceid'])->first();
                $invoicenumber = $invoice['invoice_number'];

                InvoiceDetail::where('id',$detail)->update(['qty'=>$detailinvoice['qty']-$groupdata['addon_qty']]);
                InvoiceAddons::where('row_group',$group)->delete();
                InvoiceRecipe::where('reff_id',$groupdata['id'])->where('note_recipe','AddOn')->delete();

                $checkaddoons = InvoiceAddons::where('invoicedetailid',$detail)->get();
                if($checkaddoons->count() < 1){
                    InvoiceDetail::where('id',$detail)->delete();
                }

                //Recipe Stock Items
                InvoiceRecipe::where('reff_id',$detail)->where('note_recipe','Main')->delete();
                $recipeitem = Recipe::where('parent_id',$detailinvoice['item_id'])->get();
                foreach($recipeitem as $vrecipeitem){
                    $recipe=new InvoiceRecipe;
                    $recipe->id=myFunction::id('invoicerecipe','id');
                    $recipe->reff_id=$detail;
                    $recipe->recipe_id=$vrecipeitem['id'];
                    $recipe->item_id=$vrecipeitem['item_id'];
                    $recipe->recipe_qty=$vrecipeitem['serving_size']*($detailinvoice['qty']-$groupdata['addon_qty']);
                    $recipe->note_recipe="Main";
                    $recipe->save();
                }
                //End

                $ch = curl_init(); 
                curl_setopt($ch, CURLOPT_URL, \URL::to('/cms/notif/'.$invoicenumber.'/None'));
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
                $output = curl_exec($ch); 
                curl_close($ch);
            });
         }
        catch(\Exception $e) {
            return false;
        }
        return true;
    }
}
