<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Http\Requests;
use App\Models\CatalogType;
use App\Models\CatalogPrice;
use App\Models\CatalogMetodePembayaran;
use Illuminate\Support\Facades\Storage;

use App\Helper\myFunction;

use Image;
use Input;
use File;
use Auth;
use Hash;
use App\Models\CatalogCallback;
use App\User;

class Catalog extends Model
{
    protected $table = 'catalog';

    public function catalog_type(){
        return $this->hasMany('App\Models\CatalogType', 'catalog_id');
	}

    public function food_court(){
        return $this->hasOne('App\FoodCourt', 'id', 'food_court_id');
	}

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function bank(){
        return $this->belongsTo('App\Models\NewBank', 'bank_id');
	}

    public function category()
    {
        return $this->belongsToMany('App\Models\Category', 'catalog_categories', 'catalog_id', 'category_id');
    }

    public function subcategory()
    {
        return $this->belongsToMany('App\Models\SubCategory', 'catalog_sub_categories', 'catalog_id', 'sub_category_id');
    }

    public function price_type()
    {
        return $this->belongsToMany('App\Models\PriceType', 'catalog_prices', 'catalog_id', 'price_type_id');
    }

    public function item()
    {
        return $this->belongsToMany('App\Models\Items', 'catalog_items', 'catalog_id' ,'item_id');
    }

    public static function save_data($request){
    	try {
    	    DB::transaction(function () use ($request) {
    	        $id = myFunction::id('catalog','id');
                $data=$request->all();

                $var=new Catalog;
                $var->id=$id;
                $var->user_id=Auth::user()->id;
                $var->domain=trim($data['domain']);
                $var->catalog_username=Str::slug(trim($data['catalog_username']),"");
                $var->custom_domain=$data['custom_domain'];
                $var->catalog_title=$data['catalog_title'];
                $var->catalog_tagline=$data['catalog_tagline'];
                $var->catalog_key=$data['catalog_key'];
                // $var->layout=$data['layout'];
                $var->background_header_color=$data['background_color'];
                $var->sliders=(!empty($data['sliders']))?json_encode(explode(',', $data['sliders'])):'';
                $var->theme_color=$data['theme_color'];
                $var->show_detail=$data['show_detail'];
                $var->lat=$data['lat'];
                $var->long=$data['long'];
                $var->catalog_address=$data['catalog_address'];
                $var->distance=$data['distance']?$data['distance']:100;
                $var->show_catalog=$data['show_catalog'];
                $var->phone_contact=$data['phone_contact'];
                $var->email_contact=$data['email_contact'];
                $var->catalog_password=Hash::make($data['catalog_password']);
                $var->feature=$data['feature'];
                $var->customer_data=$data['customer_data'];
                $var->password_access=$data['password_access'];
                $var->bank_id=$data['bank_id'];
                $var->bank_account_number=$data['bank_account_number'];
                $var->bank_account_name=$data['bank_account_name'];
                if(!empty($data['stock_add_ons'])){
                  $var->stock_add_ons=$data['stock_add_ons'];
                }
                $var->food_court_id=isset($data['food_court_id']) ? $data['food_court_id'] : '';

                if($data['feature']=='Full'){
                    $var->checkout_type=isset($data['checkout_type']) ? $data['checkout_type'] : 'System';
                    $var->wa_show_cart=isset($data['wa_show_cart']) ? $data['wa_show_cart'] : '0';
                    $var->wa_show_item=isset($data['wa_show_item']) ? $data['wa_show_item'] : '0';
                    $var->wa_number=isset($data['wa_number']) ? $data['wa_number'] : '';
                    $var->steps=(!empty($data['steps']))?json_encode(explode(',', $data['steps'])):'';
                    $var->advance_payment=isset($data['advance_payment']) ? $data['advance_payment'] : 'Y';
                    $var->transfer_payment=isset($data['transfer_payment']) ? $data['transfer_payment'] : 'Y';
                    $var->bank_info=isset($data['bank_info']) ? $data['bank_info'] : '';
                    $var->payment_gateway=isset($data['payment_gateway']) ? $data['payment_gateway'] : 'N';
                    $var->tax=isset($data['tax']) ? $data['tax'] : '0';
                    $var->charge=isset($data['charge']) ? $data['charge'] : 0;
                    $var->client_key=isset($data['client_key']) ? $data['client_key'] : '';
                    $var->server_key=isset($data['server_key']) ? $data['server_key'] : '';
                    $var->payment_mehod=(!empty($data['payment_mehod']))?json_encode(explode(',', $data['payment_mehod'])):'';
                    $var->online_type=isset($data['online_type']) ? $data['online_type'] : 'xendit_live';

                    $temp_pay_opt = !empty($data['pay_opt']) ? explode(',', $data['pay_opt']) : [];

                    // advance_payment : Y : Prepaid
                    // advance_payment : N : Postpaid
                    if(isset($data['advance_payment']) && $data['feature'] =='Full' && $data['advance_payment'] != "Y"){
                        if($temp_pay_opt){
                            foreach ($temp_pay_opt as $key => $value) {
                                if($value < 3){
                                    unset($temp_pay_opt[$key]);
                                }
                            }
                        }
                    }

                    $var->payment_option = (!empty($temp_pay_opt))?json_encode($temp_pay_opt):'';
                }
                $var->save();

                if($request->hasFile('logo')){
                    // $mainpath = myFunction::pathAsset();
                    $subpath = 'users/'.Auth::user()->id.'/catalog';
                    // $path = $mainpath.$subpath;

                    // File::isDirectory($path) or File::makeDirectory($path, 0777, true, true);

                    // $logofile = Image::make($_FILES['logo']['tmp_name']);
                    // $logofile->resize(120, null, function ($constraint) {
                    //     $constraint->aspectRatio();
                    // });

                    // $name=Str::slug(trim($data['catalog_title']),"_").'_'.time();
                    // $extension = $request->file('logo')->extension();
                    // $logofile->save($path.'/'.$name.'.'.$extension);
                    // $images=$subpath.'/'.$name.'.'.$extension;

                    // $array=['catalog_logo'=>$images];
                    // Catalog::where('id',$id)->update($array);

                    ////////////////////////////

                    $file = $request->file('logo');
                    $filename=Str::slug(trim($data['catalog_title']),"_").'_'.time();
                    $extension = $file->extension();
                    $filenametostore = $filename.'.'.$extension;

                    $resizeImage  = Image::make($file)->resize(120, null, function ($constraint) {
                        $constraint->aspectRatio();
                        $constraint->upsize();
                    })->stream();
                    Storage::disk('s3')->put($subpath.'/'.$filenametostore, $resizeImage->__toString());
                    $url = Storage::disk('s3')->url($subpath.'/'.$filenametostore);
                    Catalog::where('id',$id)->update(['catalog_logo' => $url]);
                }

                if($request->hasFile('catalogbg')){
                    // $mainpath = myFunction::pathAsset();
                    $subpath = 'users/'.Auth::user()->id.'/catalog';
                    // $path = $mainpath.$subpath;

                    // File::isDirectory($path) or File::makeDirectory($path, 0777, true, true);

                    // $headerbg = Image::make($_FILES['catalogbg']['tmp_name']);
                    // $headerbg->resize(1920, null, function ($constraint) {
                    //     $constraint->aspectRatio();
                    // });

                    // $name='header_'.Str::slug(trim($data['catalog_title']),"_").'_'.time();
                    // $extension = $request->file('catalogbg')->extension();
                    // $headerbg->save($path.'/'.$name.'.'.$extension);
                    // $images=$subpath.'/'.$name.'.'.$extension;

                    // $array=['background_header_image'=>$images];
                    // Catalog::where('id',$id)->update($array);

                    ////////////////////////////

                    $file = $request->file('catalogbg');
                    $filename='header_'.Str::slug(trim($data['catalog_title']),"_").'_'.time();
                    $extension = $file->extension();
                    $filenametostore = $filename.'.'.$extension;

                    $resizeImage  = Image::make($file)->resize(1920, null, function ($constraint) {
                        $constraint->aspectRatio();
                        $constraint->upsize();
                    })->stream();
                    Storage::disk('s3')->put($subpath.'/'.$filenametostore, $resizeImage->__toString());
                    $url = Storage::disk('s3')->url($subpath.'/'.$filenametostore);
                    Catalog::where('id',$id)->update(['background_header_image' => $url]);
                }

                $temp_prices = !empty($data['prices']) ? explode(',', $data['prices']) : [];

                if($temp_prices){
                    $insert_data = [];
                    foreach ($temp_prices as $value) {
                        $insert_data[] = [
                            'user_id' => Auth::user()->id,
                            'catalog_id' => $id,
                            'price_type_id' => $value
                        ];
                    }

                    if($insert_data){
                        CatalogPrice::insert($insert_data);
                    }
                }

                $temp_metode = !empty($data['metode']) ? explode(',', $data['metode']) : [];

                if($temp_metode){
                    $insert_data = [];
                    foreach ($temp_metode as $value) {
                        $insert_data[] = [
                            'catalog_id' => $id,
                            'metode_pembayaran_id' => $value
                        ];
                    }

                    if($insert_data){
                        CatalogMetodePembayaran::insert($insert_data);
                    }
                }

                // if(!empty($data['payment_mehod']) && strpos($data['payment_mehod'], 'online') !== false){
                    // create sub account
                    $url = 'https://api.xendit.co/v2/accounts';

                    $xendit_data = DB::table('settings')->where('label', $data['online_type'])->first();
                    $apiKey = $xendit_data->value;
                    $apiKey = base64_encode($apiKey.":");
                    $curl = curl_init();
                    curl_setopt_array($curl, [
                      CURLOPT_URL => $url,
                      CURLOPT_RETURNTRANSFER => true,
                      CURLOPT_ENCODING => "",
                      CURLOPT_MAXREDIRS => 10,
                      CURLOPT_TIMEOUT => 30,
                      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                      CURLOPT_CUSTOMREQUEST => "POST",
                      CURLOPT_POSTFIELDS => "{\"email\":\"".$data['email_contact']."\",\"type\":\"OWNED\",\"public_profile\":{\"business_name\":\"".$data['catalog_title']."\"}}",
                      CURLOPT_HTTPHEADER => [
                        "Authorization: Basic $apiKey",
                        "Content-Type: application/json"
                      ],
                    ]);

                    $response = curl_exec($curl);
                    $err = curl_error($curl);

                    curl_close($curl);

                    if ($err) {
                    //   echo "cURL Error #:" . $err;
                    }
                    else {

                    // }

                    // $headers = [];
                    // $headers[] = 'Content-Type: application/json';

                    // $send_data = [
                    //     'email' => $data['email_contact'],
                    //     'type' => 'OWNED',
                    //     'public_profile' => [
                    //       'business_name' => $data['catalog_title']
                    //     ]
                    // ];

                    // $curl = curl_init();
                    // $payload = json_encode($send_data);
                    // curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
                    // curl_setopt($curl, CURLOPT_USERPWD, $apiKey.":");
                    // curl_setopt($curl, CURLOPT_URL, $url);
                    // curl_setopt($curl, CURLOPT_POST, true);
                    // curl_setopt($curl, CURLOPT_POSTFIELDS, $payload);
                    // curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
                    // $result = curl_exec($curl);
                    // $result = json_decode($result);

                    // {#1925
                    //     +"account_email": "nanangkoesharwanto@yahoo.com"
                    //     +"user_id": "62247fdebabaa8cf09090a38"
                    //     +"created": "2022-03-06T09:33:18.161Z"
                    //     +"status": "SUCCESSFUL"
                    //     +"type": "OWNED"
                    // }
                    // dd($result);
                    $result = json_decode($response);
                    // if(isset($result->status) && $result->status == "SUCCESSFUL"){
                        $array=['xendit_user_id'=>$result->id];
                        Catalog::where('id',$id)->update($array);

                        $xendit_user_id = $result->id;
                        $xendit_type = $data['online_type'];

                        // submit callback url
                        // $this->callbackUrl($result->user_id, $data['online_type']);
                        $methods = [
                            [
                                'method' => 'invoice',
                                'url' => 'https://scaneat.id/xendit/handling'
                            ],
                            [
                                'method' => 'fva_status',
                                'url' => 'https://scaneat.id/xendit/handling'
                            ],
                            [
                                'method' => 'fva_paid',
                                'url' => 'https://scaneat.id/xendit/handling'
                            ],
                            [
                                'method' => 'ro_fpc_paid',
                                'url' => 'https://scaneat.id/xendit/handling'
                            ],
                            [
                                'method' => 'payment_method',
                                'url' => 'https://scaneat.id/xendit/handling'
                            ],
                            [
                                'method' => 'direct_debit',
                                'url' => 'https://scaneat.id/xendit/handling'
                            ],
                            [
                                'method' => 'regional_ro_paid',
                                'url' => 'https://scaneat.id/xendit/handling'
                            ],
                            [
                                'method' => 'disbursement',
                                'url' => 'https://scaneat.id/xendit/disbursement_handling'
                            ],
                            [
                                'method' => 'ph_disbursement',
                                'url' => 'https://scaneat.id/xendit/disbursement_handling'
                            ],
                            [
                                'method' => 'batch_disbursement',
                                'url' => 'https://scaneat.id/xendit/disbursement_handling'
                            ],
                            [
                                'method' => 'ovo_paid',
                                'url' => 'https://scaneat.id/xendit/ewallet_handling'
                            ],
                            [
                                'method' => 'ewallet',
                                'url' => 'https://scaneat.id/xendit/ewallet_handling'
                            ],
                        ];

                        foreach ($methods as $key => $value) {
                            $callback = CatalogCallback::where('catalog_id', $id)->where('callback_type', $value['method'])->first();

                            if(!$callback){
                                $url = 'https://api.xendit.co/callback_urls/'.$value['method'];

                                $xendit_data = DB::table('settings')->where('label', $xendit_type)->first();
                                $apiKey = $xendit_data->value;

                                $headers = [];
                                $headers[] = 'Content-Type: application/json';
                                $headers[] = 'for-user-id: ' . $xendit_user_id;
                                $payload = [
                                    'url' => $value['url']
                                ];

                                $curl = curl_init();
                                $payload = json_encode($payload);
                                curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
                                curl_setopt($curl, CURLOPT_USERPWD, $apiKey.":");
                                curl_setopt($curl, CURLOPT_URL, $url);
                                curl_setopt($curl, CURLOPT_POST, true);
                                curl_setopt($curl, CURLOPT_POSTFIELDS, $payload);
                                curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
                                $result = curl_exec($curl);
                                $result = json_decode($result);

                                if(isset($result->status) && $result->status == "SUCCESSFUL"){
                                    CatalogCallback::create([
                                        'catalog_id' => $id,
                                        'callback_type' => $value['method'],
                                        'callback_response' => json_encode($result),
                                        'user_id' => Auth::user()->id
                                    ]);
                                }

                                // dd($result);
                                // "{"status":"SUCCESSFUL","user_id":"62247fdebabaa8cf09090a38","url":"https://scaneat.id/xendit/handling","environment":"LIVE","callback_token":"61865a0b12a4d957fe6c152d6e12fa7e07a8d5e5696255f8536fb9b6d2613862"}"
                            }
                        }
                    }
                // }
    	    });
        }
    	catch(\Exception $e) {
            // dd($e);
    	    return false;
    	}
    	return true;
    }
    public static function update_data_old($request){
    	// try {
    	//     DB::transaction(function () use ($request) {
    	        $data=$request->all();
    	        $query = Catalog::where('id',$data['id'])->first();
                $org_email = $query['email_contact'];
    	        // $mainpath = str_replace('public/scaneat.id', 'scaneat.id', myFunction::pathAsset());
                $subpath = 'users/'.Auth::user()->id.'/catalog';
                // $path = $mainpath.$subpath;
                // File::isDirectory($path) or File::makeDirectory($path, 0777, true, true);
    	        if($request->hasFile('logo')){
                    // if(!empty($query['catalog_logo']) and File::exists($path.'/'.basename(parse_url($query['catalog_logo'])['path']))){
                    //     @unlink($path.'/'.basename(parse_url($data['catalog_logo'])['path']));
                    // }

                    // $imgfile = Image::make($_FILES['logo']['tmp_name']);
                    // $imgfile->resize(120, null, function ($constraint) {
                    //     $constraint->aspectRatio();
                    // });
                    // $name=Str::slug(trim($data['catalog_title']),"_").'_'.time();
                    // $extension = $request->file('logo')->extension();
                    // $imgfile->save($path.'/'.$name.'.'.$extension);
                    // $logo=$subpath.'/'.$name.'.'.$extension;

                    try {
                        Storage::disk('s3')->delete(str_replace('https://scaneat.s3.ap-southeast-1.amazonaws.com/', '', $query['catalog_logo']));
                    } catch (\Exception $e) {
                        //throw $th;
                    }

                    $file = $request->file('logo');
                    $filename=Str::slug(trim($data['catalog_title']),"_").'_'.time();
                    $extension = $file->extension();
                    $filenametostore = $filename.'.'.$extension;

                    $resizeImage  = Image::make($file)->resize(120, null, function ($constraint) {
                        $constraint->aspectRatio();
                        $constraint->upsize();
                    })->stream();
                    Storage::disk('s3')->put($subpath.'/'.$filenametostore, $resizeImage->__toString());
                    $logo = Storage::disk('s3')->url($subpath.'/'.$filenametostore);
                }else{
                    if(!empty($data['catalog_logo'])){
                        // $old = $path.'/'.basename(parse_url($data['catalog_logo'])['path']);
                        // $name=Str::slug(trim($data['catalog_title']),"_").'_'.time();
                        // $ext=explode(".",basename(parse_url($data['catalog_logo'])['path']));
                        // $new = $path.'/'.$name.'.'.$ext[1];
                        // @rename($old, $new);
                        // $logo=$subpath.'/'.$name.'.'.$ext[1];
                        if(strpos($data['catalog_logo'], 'amazonaws.com') !== false){
                            $logo=$data['catalog_logo'];
                        }
                        else{
                            $logo='';
                        }
                    }else{
                        $logo=$data['catalog_logo'];
                    }

                    // if(!empty($data['item_image_'.$array[$key]])){
                    //     if(strpos($data['item_image_'.$array[$key]], 'amazonaws.com') !== false){
                    //         $update_img[$key]=$data['item_image_'.$array[$key]];
                    //     }
                    //     else{
                    //         $update_img[$key]='';
                    //     }
                    // }else{
                    //     $update_img[$key]=$data['item_image_'.$array[$key]];
                    // }

                }

                if($request->hasFile('catalogbg')){
                    // if(!empty($query['background_header_image']) and File::exists($path.'/'.basename(parse_url($query['background_header_image'])['path']))){
                    //     @unlink($path.'/'.basename(parse_url($data['background_header_image'])['path']));
                    // }

                    // $headerbg = Image::make($_FILES['catalogbg']['tmp_name']);
                    // $headerbg->resize(1920, null, function ($constraint) {
                    //     $constraint->aspectRatio();
                    // });
                    // $name='header_'.Str::slug(trim($data['catalog_title']),"_").'_'.time();
                    // $extension = $request->file('catalogbg')->extension();
                    // $headerbg->save($path.'/'.$name.'.'.$extension);
                    // $backgroundheader=$subpath.'/'.$name.'.'.$extension;

                    try {
                        Storage::disk('s3')->delete(str_replace('https://scaneat.s3.ap-southeast-1.amazonaws.com/', '', $query['background_header_image']));
                    } catch (\Exception $e) {
                        //throw $th;
                    }

                    $file = $request->file('catalogbg');
                    $filename='header_'.Str::slug(trim($data['catalog_title']),"_").'_'.time();
                    $extension = $file->extension();
                    $filenametostore = $filename.'.'.$extension;

                    $resizeImage  = Image::make($file)->resize(1920, null, function ($constraint) {
                        $constraint->aspectRatio();
                        $constraint->upsize();
                    })->stream();
                    Storage::disk('s3')->put($subpath.'/'.$filenametostore, $resizeImage->__toString());
                    $backgroundheader = Storage::disk('s3')->url($subpath.'/'.$filenametostore);
                }else{
                    if(!empty($data['background_header_image'])){
                        // $old = $path.'/'.basename(parse_url($data['background_header_image'])['path']);
                        // $name='header_'.Str::slug(trim($data['catalog_title']),"_").'_'.time();
                        // $ext=explode(".",basename(parse_url($data['background_header_image'])['path']));
                        // $new = $path.'/'.$name.'.'.$ext[1];
                        // @rename($old, $new);
                        // $backgroundheader=$subpath.'/'.$name.'.'.$ext[1];
                        if(strpos($data['background_header_image'], 'amazonaws.com') !== false){
                            $backgroundheader=$data['background_header_image'];
                        }
                        else{
                            $backgroundheader='';
                        }
                    }else{
                        $backgroundheader=$data['background_header_image'];
                    }
                }

                $temp_pay_opt = !empty($data['pay_opt']) ? explode(',', $data['pay_opt']) : [];
                $is_any_delivery_option = false;

                // advance_payment : Y : Prepaid
                // advance_payment : N : Postpaid
                if($data['feature']  =='Full' && $data['advance_payment'] != "Y"){
                    if($temp_pay_opt){
                        foreach ($temp_pay_opt as $key => $value) {
                            if($value < 3){
                                unset($temp_pay_opt[$key]);
                            }
                        }
                    }
                }

                if(!in_array(1, $temp_pay_opt)){
                    $data['delivery_option'] = null;
                }

    	        $array=	[
    	        			'catalog_type'=>$data['catalog_type'],
    	        			'domain'=>$data['domain'],
    	        			'catalog_username'=>Str::slug(trim($data['catalog_username']),""),
    	        			'custom_domain'=>$data['custom_domain'],
    	        			'catalog_title'=>$data['catalog_title'],
    	        			'catalog_logo'=>$logo,
                            'catalog_tagline'=>$data['catalog_tagline'],
                            'catalog_key'=>$data['catalog_key'],
    	        			'background_header_color'=>$data['background_color'],
    	        			'background_header_image'=>$backgroundheader,
    	        			'sliders'=>(!empty($data['sliders']))?json_encode(explode(',', $data['sliders'])):'',
    	        			'payment_option'=>(!empty($temp_pay_opt))?json_encode($temp_pay_opt):'',
    	        			// 'layout'=>$data['layout'],
                            'theme_color'=>$data['theme_color'],
                            'show_detail'=>$data['show_detail'],
                            'lat'=>$data['lat'],
                            'long'=>$data['long'],
                            'catalog_address'=>$data['catalog_address'],
                            'distance'=>$data['distance']?$data['distance']:100,
                            'show_catalog'=>$data['show_catalog'],
                            'phone_contact'=>$data['phone_contact'],
                            'email_contact'=>$data['email_contact'],
                            'feature'=>$data['feature'],
                            'customer_data'=>$data['customer_data'],
                            'password_access'=>$data['password_access'],

                            'bank_id'=>$data['bank_id'],
                            'bank_account_number'=>$data['bank_account_number'],
                            'bank_account_name'=>$data['bank_account_name'],
    	    			];

                if($data['catalog_password']){
                    $array['catalog_password'] = Hash::make($data['catalog_password']);
                }

    	        Catalog::where('id',$data['id'])->update($array);

                // ======================================
                $temp_prices = !empty($data['prices']) ? explode(',', $data['prices']) : [];

                if($temp_prices){
                    $insert_data = [];
                    foreach ($temp_prices as $value) {
                        $insert_data[] = [
                            'user_id' => Auth::user()->id,
                            'catalog_id' => $data['id'],
                            'price_type_id' => $value
                        ];
                    }

                    if($insert_data){
                        CatalogPrice::where([
                            'user_id' => Auth::user()->id,
                            'catalog_id' => $data['id'],
                        ])->delete();

                        CatalogPrice::insert($insert_data);
                    }
                }
                else{
                    CatalogPrice::where([
                        'user_id' => Auth::user()->id,
                        'catalog_id' => $data['id'],
                    ])->delete();
                }
                // ======================================

                $temp_metode = !empty($data['metode']) ? explode(',', $data['metode']) : [];

                if($temp_metode){
                    $insert_data = [];
                    foreach ($temp_metode as $value) {
                        $insert_data[] = [
                            'catalog_id' => $data['id'],
                            'metode_pembayaran_id' => $value
                        ];
                    }

                    if($insert_data){
                        CatalogMetodePembayaran::where([
                            'catalog_id' => $data['id'],
                        ])->delete();

                        CatalogMetodePembayaran::insert($insert_data);
                    }
                }
                else{
                    CatalogMetodePembayaran::where([
                        'catalog_id' => $data['id'],
                    ])->delete();
                }

                if($data['feature']=='Full'){
                    $arrayadvance= [
                        'checkout_type'=>$data['checkout_type'],
                        'wa_show_cart'=>$data['wa_show_cart'],
                        'wa_show_item'=>$data['wa_show_item'],
                        'wa_number'=>$data['wa_number'],
                        'steps'=>(!empty($data['steps']))?json_encode(explode(',', $data['steps'])):'',
                        'advance_payment'=>$data['advance_payment'],
                        'transfer_payment'=>$data['transfer_payment'],
                        'delivery_option'=>$data['delivery_option'],
                        'bank_info'=>$data['bank_info'],
                        'payment_gateway'=>$data['payment_gateway'],
                        'tax'=>$data['tax'],
                        'charge'=>$data['charge'],
                        'client_key'=>$data['client_key'],
                        'server_key'=>$data['server_key'],
                        'payment_mehod'=>(!empty($data['payment_mehod']))?json_encode(explode(',', $data['payment_mehod'])):'',
                        'online_type'=>$data['online_type'],
                    ];
                    Catalog::where('id',$data['id'])->update($arrayadvance);
                }

                // if(!empty($data['payment_mehod']) && strpos($data['payment_mehod'], 'online') !== false){
                    $xendit_data = DB::table('settings')->where('label', $data['online_type'])->first();
                    $apiKey = $xendit_data->value;

                    // $headers = [];
                    // $headers[] = 'Content-Type: application/json';

                    // $send_data = [
                    //     'email' => $data['email_contact'],
                    //     'type' => 'OWNED',
                    //     'business_profile' => [
                    //       'business_name' => $data['catalog_title']
                    //     ]
                    // ];

                    // if($org_email != $data['email_contact']){
                    //     $url = 'https://api.xendit.co/v2/accounts';
                    //     // $send_data['type'] = 'OWNED';
                    // }
                    // else{
                    //     if($query['xendit_user_id']){
                    //         $url = 'https://api.xendit.co/v2/accounts/'.$query['xendit_user_id'];
                    //     }
                    //     else{
                    //         $url = 'https://api.xendit.co/v2/accounts';
                    //     }
                    // }

                    $xendit_user_id = $query['xendit_user_id'];

                    if(!$query['xendit_user_id']){
                        $url = 'https://api.xendit.co/v2/accounts';
                        $apiKey = base64_encode($apiKey.":");

                        $curl = curl_init();
                        curl_setopt_array($curl, [
                        CURLOPT_URL => $url,
                        CURLOPT_RETURNTRANSFER => true,
                        CURLOPT_ENCODING => "",
                        CURLOPT_MAXREDIRS => 10,
                        CURLOPT_TIMEOUT => 30,
                        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                        CURLOPT_CUSTOMREQUEST => "POST",
                        CURLOPT_POSTFIELDS => "{\"email\":\"".$data['email_contact']."\",\"type\":\"OWNED\",\"public_profile\":{\"business_name\":\"".$data['catalog_title']."\"}}",
                        CURLOPT_HTTPHEADER => [
                            "Authorization: Basic $apiKey",
                            "Content-Type: application/json"
                        ],
                        ]);

                        $response = curl_exec($curl);
                        $err = curl_error($curl);

                        curl_close($curl);

                        // if ($err) {
                        // //   echo "cURL Error #:" . $err;
                        // // "{"name":"INVALID_CREDENTIALS","message":"Your credentials are not valid to make this request"}"

                        //     dd('test');
                        // }
                        // else {
                            $result = json_decode($response);
                            // dd($result);
                            if(isset($result->name) && strpos($result->name, 'INVALID') !== false){
                                return false;
                            }

                            if(!isset($result->error_code)){
                                if(isset($result->id)){
                                    $xendit_user_id = $result->id;
                                }
                            }
                        // }
                    }


                    // }

                    // $curl = curl_init();
                    // $payload = json_encode($send_data);
                    // curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
                    // curl_setopt($curl, CURLOPT_USERPWD, $apiKey.":");
                    // curl_setopt($curl, CURLOPT_URL, $url);
                    // curl_setopt($curl, CURLOPT_POST, true);
                    // curl_setopt($curl, CURLOPT_POSTFIELDS, $payload);
                    // curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
                    // $result = curl_exec($curl);
                    // $result = json_decode($result);

                    /*
                        "{"error_code":"BUSINESS_DUPLICATE_EMAIL_ERROR","message":"Business with that email already exists"}"

                        "{"id":"6224cc74e287d37ab6fd39ab","created":"2022-03-06T15:00:04.452Z","updated":"2022-03-06T15:00:06.457Z","email":"nanangcangkring1@yahoo.com","type":"OWNED","public_profile":{"business_name":"Kanigaran"},"country":"ID","status":"REGISTERED"}"
                    */


                    // if(isset($result->status) && $result->status == "SUCCESSFUL"){
                        // if(isset($result->error_code)){
                        //     $xendit_user_id = $result->id;
                        // }
                        // else{
                        //     $array=['xendit_user_id'=>$result->id];
                        //     Catalog::where('id',$data['id'])->update($array);
                        //     $xendit_user_id = $result->id;
                        // }

                        $xendit_type = $data['online_type'];

                        // submit callback url
                        // $this->callbackUrl($result->user_id, $data['online_type']);

                        if($xendit_user_id){

                            $methods = [
                                [
                                    'method' => 'invoice',
                                    'url' => 'https://scaneat.id/xendit/handling'
                                ],
                                [
                                    'method' => 'fva_status',
                                    'url' => 'https://scaneat.id/xendit/handling'
                                ],
                                [
                                    'method' => 'fva_paid',
                                    'url' => 'https://scaneat.id/xendit/handling'
                                ],
                                [
                                    'method' => 'ro_fpc_paid',
                                    'url' => 'https://scaneat.id/xendit/handling'
                                ],
                                [
                                    'method' => 'payment_method',
                                    'url' => 'https://scaneat.id/xendit/handling'
                                ],
                                [
                                    'method' => 'direct_debit',
                                    'url' => 'https://scaneat.id/xendit/handling'
                                ],
                                [
                                    'method' => 'regional_ro_paid',
                                    'url' => 'https://scaneat.id/xendit/handling'
                                ],
                                [
                                    'method' => 'disbursement',
                                    'url' => 'https://scaneat.id/xendit/disbursement_handling'
                                ],
                                [
                                    'method' => 'ph_disbursement',
                                    'url' => 'https://scaneat.id/xendit/disbursement_handling'
                                ],
                                [
                                    'method' => 'batch_disbursement',
                                    'url' => 'https://scaneat.id/xendit/disbursement_handling'
                                ],
                                [
                                    'method' => 'ovo_paid',
                                    'url' => 'https://scaneat.id/xendit/ewallet_handling'
                                ],
                                [
                                    'method' => 'ewallet',
                                    'url' => 'https://scaneat.id/xendit/ewallet_handling'
                                ],
                            ];

                            foreach ($methods as $key => $value) {
                                $url = 'https://api.xendit.co/callback_urls/'.$value['method'];

                                $xendit_data = DB::table('settings')->where('label', $xendit_type)->first();
                                $apiKey = $xendit_data->value;

                                $headers = [];
                                $headers[] = 'Content-Type: application/json';
                                $headers[] = 'for-user-id: ' . $xendit_user_id;
                                $payload = [
                                    'url' => $value['url']
                                ];

                                $curl = curl_init();
                                $payload = json_encode($payload);
                                curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
                                curl_setopt($curl, CURLOPT_USERPWD, $apiKey.":");
                                curl_setopt($curl, CURLOPT_URL, $url);
                                curl_setopt($curl, CURLOPT_POST, true);
                                curl_setopt($curl, CURLOPT_POSTFIELDS, $payload);
                                curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
                                $result = curl_exec($curl);
                                $result = json_decode($result);

                                if(isset($result->status) && $result->status == "SUCCESSFUL"){
                                    $callback = CatalogCallback::where('catalog_id', $data['id'])->where('callback_type', $value['method'])->first();

                                    if(!$callback){
                                        CatalogCallback::create([
                                            'catalog_id' => $data['id'],
                                            'callback_type' => $value['method'],
                                            'callback_response' => json_encode($result),
                                            'user_id' => Auth::user()->id
                                        ]);
                                    }
                                    else{
                                        $callback->callback_response = json_encode($result);
                                        $callback->save();
                                    }
                                }

                                // dd($result);
                                // "{"status":"SUCCESSFUL","user_id":"62247fdebabaa8cf09090a38","url":"https://scaneat.id/xendit/handling","environment":"LIVE","callback_token":"61865a0b12a4d957fe6c152d6e12fa7e07a8d5e5696255f8536fb9b6d2613862"}"
                            }
                        }
                    // }
                // }

                $catalog_list = (!empty($data['catalog_list']))?(explode(',', $data['catalog_list'])):'';

                if($data['catalog_type'] == 2){
                    if($catalog_list){

                        CatalogType::where('catalog_id', $data['id'])->delete();

                        $insert_type = [];
                        $order = 0;
                        foreach ($catalog_list as $key => $value) {
                            if($value > 0){
                                $insert_type[] = [
                                    'catalog_id' => $data['id'],
                                    'catalog_list_type_id' => $value,
                                    'order' => $order
                                    // 'label'
                                    // 'description'
                                    // 'image'
                                ];
                                $order++;
                            }
                        }

                        if($insert_type){
                            CatalogType::insert($insert_type);
                        }
                    }
                }
    	//     });
    	//  }
    	// catch(\Exception $e) {
        //     // dd($e);
    	//     return false;
    	// }
    	// return true;
    }
    public static function update_data($request){
        DB::beginTransaction();
    	try {
            $data=$request->all();

            $query = Catalog::where('id',$data['id'])->first();
            $subpath = 'users/'.Auth::user()->id.'/catalog';

            if($request->hasFile('logo')){
                try {
                    Storage::disk('s3')->delete(str_replace('https://scaneat.s3.ap-southeast-1.amazonaws.com/', '', $query['catalog_logo']));
                } catch (\Exception $e) {
                    //throw $th;
                }

                $file = $request->file('logo');
                $filename=Str::slug(trim($data['catalog_title']),"_").'_'.time();
                $extension = $file->extension();
                $filenametostore = $filename.'.'.$extension;

                $resizeImage  = Image::make($file)->resize(120, null, function ($constraint) {
                    $constraint->aspectRatio();
                    $constraint->upsize();
                })->stream();
                Storage::disk('s3')->put($subpath.'/'.$filenametostore, $resizeImage->__toString());
                $logo = Storage::disk('s3')->url($subpath.'/'.$filenametostore);
            }else{
                if(!empty($data['catalog_logo'])){
                    if(strpos($data['catalog_logo'], 'amazonaws.com') !== false){
                        $logo=$data['catalog_logo'];
                    }
                    else{
                        $logo='';
                    }
                }else{
                    $logo=$data['catalog_logo'];
                }
            }

            if($request->hasFile('catalogbg')){
                try {
                    Storage::disk('s3')->delete(str_replace('https://scaneat.s3.ap-southeast-1.amazonaws.com/', '', $query['background_header_image']));
                } catch (\Exception $e) {
                    //throw $th;
                }

                $file = $request->file('catalogbg');
                $filename='header_'.Str::slug(trim($data['catalog_title']),"_").'_'.time();
                $extension = $file->extension();
                $filenametostore = $filename.'.'.$extension;

                $resizeImage  = Image::make($file)->resize(1920, null, function ($constraint) {
                    $constraint->aspectRatio();
                    $constraint->upsize();
                })->stream();
                Storage::disk('s3')->put($subpath.'/'.$filenametostore, $resizeImage->__toString());
                $backgroundheader = Storage::disk('s3')->url($subpath.'/'.$filenametostore);
            }else{
                if(!empty($data['background_header_image'])){
                    if(strpos($data['background_header_image'], 'amazonaws.com') !== false){
                        $backgroundheader=$data['background_header_image'];
                    }
                    else{
                        $backgroundheader='';
                    }
                }else{
                    $backgroundheader=$data['background_header_image'];
                }
            }

            $temp_pay_opt = !empty($data['pay_opt']) ? explode(',', $data['pay_opt']) : [];

            // advance_payment : Y : Prepaid
            // advance_payment : N : Postpaid
            if($data['feature']  =='Full' && $data['advance_payment'] != "Y"){
                if($temp_pay_opt){
                    foreach ($temp_pay_opt as $key => $value) {
                        if($value < 3){
                            unset($temp_pay_opt[$key]);
                        }
                    }
                }
            }

            if(!in_array(1, $temp_pay_opt)){
                $data['delivery_option'] = null;
            }

            $array=	[
                    'catalog_type'=>$data['catalog_type'],
                    'domain'=>$data['domain'],
                    'catalog_username'=>Str::slug(trim($data['catalog_username']),""),
                    'custom_domain'=>$data['custom_domain'],
                    'catalog_title'=>$data['catalog_title'],
                    'catalog_logo'=>$logo,
                    'catalog_tagline'=>$data['catalog_tagline'],
                    'catalog_key'=>$data['catalog_key'],
                    'background_header_color'=>$data['background_color'],
                    'background_header_image'=>$backgroundheader,
                    'sliders'=>(!empty($data['sliders']))?json_encode(explode(',', $data['sliders'])):'',
                    'payment_option'=>(!empty($temp_pay_opt))?json_encode($temp_pay_opt):'',
                    // 'layout'=>$data['layout'],
                    'theme_color'=>$data['theme_color'],
                    'show_detail'=>$data['show_detail'],
                    'lat'=>$data['lat'],
                    'long'=>$data['long'],
                    'catalog_address'=>$data['catalog_address'],
                    'distance'=>$data['distance']?$data['distance']:100,
                    'show_catalog'=>$data['show_catalog'],
                    'phone_contact'=>$data['phone_contact'],
                    'email_contact'=>$data['email_contact'],
                    'feature'=>$data['feature'],
                    'customer_data'=>$data['customer_data'],
                    'password_access'=>$data['password_access'],
                    'bank_id'=>$data['bank_id'],
                    'bank_account_number'=>$data['bank_account_number'],
                    'bank_account_name'=>$data['bank_account_name'],
                    'food_court_id'=>$data['food_court_id'],
                    'set_table'=>$data['set_table'],
                    'license_agreement'=>$data['license_agreement'],
                    'stock_add_ons'=>(!empty($data['stock_add_ons']))?$data['stock_add_ons']:'',
                ];

            if($data['catalog_password']){
                $array['catalog_password'] = Hash::make($data['catalog_password']);
            }

            Catalog::where('id',$data['id'])->update($array);

            if($data['catalog_type'] == 2){
                if($request->belongsto_hotel){
                    Catalog::where('id', $request->belongsto_hotel)
                        ->update([
                            'hotel_id' => $data['id']
                        ]);
                }
            }
            else{
                Catalog::where('hotel_id', $data['id'])
                    ->update([
                        'hotel_id' => null
                    ]);
            }

            // ======================================
            // gofood, grabfood, grosir, etc.
            $temp_prices = !empty($data['prices']) ? explode(',', $data['prices']) : [];

            if($temp_prices){
                $insert_data = [];
                foreach ($temp_prices as $value) {
                    $insert_data[] = [
                        'user_id' => Auth::user()->id,
                        'catalog_id' => $data['id'],
                        'price_type_id' => $value
                    ];
                }

                if($insert_data){
                    CatalogPrice::where([
                        'user_id' => Auth::user()->id,
                        'catalog_id' => $data['id'],
                    ])->delete();

                    CatalogPrice::insert($insert_data);
                }
            }
            else{
                CatalogPrice::where([
                    'user_id' => Auth::user()->id,
                    'catalog_id' => $data['id'],
                ])->delete();
            }
            // ======================================

            $temp_metode = !empty($data['metode']) ? explode(',', $data['metode']) : [];

            if($temp_metode){
                $insert_data = [];
                foreach ($temp_metode as $value) {
                    $insert_data[] = [
                        'catalog_id' => $data['id'],
                        'metode_pembayaran_id' => $value
                    ];
                }

                if($insert_data){
                    CatalogMetodePembayaran::where([
                        'catalog_id' => $data['id'],
                    ])->delete();

                    CatalogMetodePembayaran::insert($insert_data);
                }
            }
            else{
                CatalogMetodePembayaran::where([
                    'catalog_id' => $data['id'],
                ])->delete();
            }

            if($data['feature']=='Full'){
                $arrayadvance= [
                    'checkout_type'=>$data['checkout_type'],
                    'wa_show_cart'=>$data['wa_show_cart'],
                    'wa_show_item'=>$data['wa_show_item'],
                    'wa_number'=>$data['wa_number'],
                    'steps'=>(!empty($data['steps']))?json_encode(explode(',', $data['steps'])):'',
                    'advance_payment'=>$data['advance_payment'],
                    'transfer_payment'=>$data['transfer_payment'],
                    'delivery_option'=>$data['delivery_option'],
                    'bank_info'=>$data['bank_info'],
                    'payment_gateway'=>$data['payment_gateway'],
                    'tax'=>$data['tax'],
                    'charge'=>$data['charge'],
                    'client_key'=>$data['client_key'],
                    'server_key'=>$data['server_key'],
                    'payment_mehod'=>(!empty($data['payment_mehod']))?json_encode(explode(',', $data['payment_mehod'])):'',
                    'online_type'=>$data['online_type'],
                ];
                Catalog::where('id',$data['id'])->update($arrayadvance);
            }

            $xendit_data = DB::table('settings')->where('label', $data['online_type'])->first();
            $apiKey = $xendit_data->value;
            $xendit_user_id = $query['xendit_user_id'];

            if(!$query['xendit_user_id']){
                $url = 'https://api.xendit.co/v2/accounts';
                $apiKey = base64_encode($apiKey.":");

                $curl = curl_init();
                curl_setopt_array($curl, [
                CURLOPT_URL => $url,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => "",
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => "POST",
                CURLOPT_POSTFIELDS => "{\"email\":\"".$data['email_contact']."\",\"type\":\"OWNED\",\"public_profile\":{\"business_name\":\"".$data['catalog_title']."\"}}",
                CURLOPT_HTTPHEADER => [
                    "Authorization: Basic $apiKey",
                    "Content-Type: application/json"
                ],
                ]);

                $response = curl_exec($curl);
                $err = curl_error($curl);
                curl_close($curl);
                $result = json_decode($response);

                if(isset($result->name) && strpos($result->name, 'INVALID') !== false){
                    DB::rollback();
                    return $result->message;
                }

                if(isset($result->error_code)){
                    DB::rollback();
                    return $result->message;
                }
                else{
                    if(isset($result->id)){
                        $array=['xendit_user_id'=>$result->id];
                        Catalog::where('id',$data['id'])->update($array);
                        $xendit_user_id = $result->id;
                    }
                }
            }

            /*
                "{"error_code":"BUSINESS_DUPLICATE_EMAIL_ERROR","message":"Business with that email already exists"}"
                "{"id":"6224cc74e287d37ab6fd39ab","created":"2022-03-06T15:00:04.452Z","updated":"2022-03-06T15:00:06.457Z","email":"nanangcangkring1@yahoo.com","type":"OWNED","public_profile":{"business_name":"Kanigaran"},"country":"ID","status":"REGISTERED"}"
            */

            $xendit_type = $data['online_type'];

            // submit callback url
            // $this->callbackUrl($result->user_id, $data['online_type']);

            if($xendit_user_id){
                $methods = [
                    [
                        'method' => 'invoice',
                        'url' => 'https://scaneat.id/xendit/handling'
                    ],
                    [
                        'method' => 'fva_status',
                        'url' => 'https://scaneat.id/xendit/handling'
                    ],
                    [
                        'method' => 'fva_paid',
                        'url' => 'https://scaneat.id/xendit/handling'
                    ],
                    [
                        'method' => 'ro_fpc_paid',
                        'url' => 'https://scaneat.id/xendit/handling'
                    ],
                    [
                        'method' => 'payment_method',
                        'url' => 'https://scaneat.id/xendit/handling'
                    ],
                    [
                        'method' => 'direct_debit',
                        'url' => 'https://scaneat.id/xendit/handling'
                    ],
                    [
                        'method' => 'regional_ro_paid',
                        'url' => 'https://scaneat.id/xendit/handling'
                    ],
                    [
                        'method' => 'disbursement',
                        'url' => 'https://scaneat.id/xendit/disbursement_handling'
                    ],
                    [
                        'method' => 'ph_disbursement',
                        'url' => 'https://scaneat.id/xendit/disbursement_handling'
                    ],
                    [
                        'method' => 'batch_disbursement',
                        'url' => 'https://scaneat.id/xendit/disbursement_handling'
                    ],
                    [
                        'method' => 'ovo_paid',
                        'url' => 'https://scaneat.id/xendit/ewallet_handling'
                    ],
                    [
                        'method' => 'ewallet',
                        'url' => 'https://scaneat.id/xendit/ewallet_handling'
                    ],
                ];

                foreach ($methods as $key => $value) {
                    $url = 'https://api.xendit.co/callback_urls/'.$value['method'];

                    $xendit_data = DB::table('settings')->where('label', $xendit_type)->first();
                    $apiKey = $xendit_data->value;

                    $headers = [];
                    $headers[] = 'Content-Type: application/json';
                    $headers[] = 'for-user-id: ' . $xendit_user_id;
                    $payload = [
                        'url' => $value['url']
                    ];

                    $curl = curl_init();
                    $payload = json_encode($payload);
                    curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
                    curl_setopt($curl, CURLOPT_USERPWD, $apiKey.":");
                    curl_setopt($curl, CURLOPT_URL, $url);
                    curl_setopt($curl, CURLOPT_POST, true);
                    curl_setopt($curl, CURLOPT_POSTFIELDS, $payload);
                    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
                    $result = curl_exec($curl);
                    $result = json_decode($result);

                    if(isset($result->status) && $result->status == "SUCCESSFUL"){
                        $callback = CatalogCallback::where('catalog_id', $data['id'])->where('callback_type', $value['method'])->first();

                        if(!$callback){
                            CatalogCallback::create([
                                'catalog_id' => $data['id'],
                                'callback_type' => $value['method'],
                                'callback_response' => json_encode($result),
                                'user_id' => Auth::user()->id
                            ]);
                        }
                        else{
                            $callback->callback_response = json_encode($result);
                            $callback->save();
                        }
                    }

                    // dd($result);
                    // "{"status":"SUCCESSFUL","user_id":"62247fdebabaa8cf09090a38","url":"https://scaneat.id/xendit/handling","environment":"LIVE","callback_token":"61865a0b12a4d957fe6c152d6e12fa7e07a8d5e5696255f8536fb9b6d2613862"}"
                }
            }

            $catalog_list = (!empty($data['catalog_list']))?(explode(',', $data['catalog_list'])):'';

            if($data['catalog_type'] == 2){
                if($catalog_list){

                    CatalogType::where('catalog_id', $data['id'])->delete();

                    $insert_type = [];
                    $order = 0;
                    foreach ($catalog_list as $key => $value) {
                        if($value > 0){
                            $insert_type[] = [
                                'catalog_id' => $data['id'],
                                'catalog_list_type_id' => $value,
                                'order' => $order
                                // 'label'
                                // 'description'
                                // 'image'
                            ];
                            $order++;
                        }
                    }

                    if($insert_type){
                        CatalogType::insert($insert_type);
                    }
                }
            }

            DB::commit();
            return 'success';
        }
    	catch(\Exception $e) {
            // dd($e);
            DB::rollback();
    	    return false;
    	}
    }
    public static function delete_data($id){
        try {
            DB::transaction(function () use ($id) {
            	$query = Catalog::where('id',$id)->first();

            	$mainpath = myFunction::pathAsset();
            	$subpath = '/users/'.Auth::user()->id.'/catalog';
            	$path = $mainpath.$subpath;

            	if(!empty($query['catalog_logo']) and File::exists($path.'/'.basename(parse_url($query['catalog_logo'])['path']))){
            	    @unlink($path.'/'.basename(parse_url($query['catalog_logo'])['path']));
            	}
            	if(!empty($query['background_header_image']) and File::exists($path.'/'.basename(parse_url($query['background_header_image'])['path']))){
            	    @unlink($path.'/'.basename(parse_url($query['background_header_image'])['path']));
            	}

                try {
                    Storage::disk('s3')->delete(str_replace('https://scaneat.s3.ap-southeast-1.amazonaws.com/', '', $query['catalog_logo']));
                } catch (\Exception $e) {
                    //throw $th;
                }

                try {
                    Storage::disk('s3')->delete(str_replace('https://scaneat.s3.ap-southeast-1.amazonaws.com/', '', $query['background_header_image']));
                } catch (\Exception $e) {
                    //throw $th;
                }

                Catalog::where('id',$id)->delete();
            });
         }
        catch(\Exception $e) {
            return false;
        }
        return true;
    }
    public function callbackUrl($xendit_user_id, $xendit_type)
    {
        // POST https://api.xendit.co/callback_urls/:type
        // type : invoice, fva_status, fva_paid, ro_fpc_paid,
        // ovo_paid, ewallet,
        // payment_method, direct_debit, regional_ro_paid,
        // disbursement, ph_disbursement, batch_disbursement
        // header : for-user-id

        $methods = [
            [
                'method' => 'invoice',
                'url' => 'https://scaneat.id/xendit/handling'
            ],
            [
                'method' => 'fva_status',
                'url' => 'https://scaneat.id/xendit/handling'
            ],
            [
                'method' => 'fva_paid',
                'url' => 'https://scaneat.id/xendit/handling'
            ],
            [
                'method' => 'ro_fpc_paid',
                'url' => 'https://scaneat.id/xendit/handling'
            ],
            [
                'method' => 'payment_method',
                'url' => 'https://scaneat.id/xendit/handling'
            ],
            [
                'method' => 'direct_debit',
                'url' => 'https://scaneat.id/xendit/handling'
            ],
            [
                'method' => 'regional_ro_paid',
                'url' => 'https://scaneat.id/xendit/handling'
            ],
            [
                'method' => 'disbursement',
                'url' => 'https://scaneat.id/xendit/disbursement_handling'
            ],
            [
                'method' => 'ph_disbursement',
                'url' => 'https://scaneat.id/xendit/disbursement_handling'
            ],
            [
                'method' => 'batch_disbursement',
                'url' => 'https://scaneat.id/xendit/disbursement_handling'
            ],
            [
                'method' => 'ovo_paid',
                'url' => 'https://scaneat.id/xendit/ewallet_handling'
            ],
            [
                'method' => 'ewallet',
                'url' => 'https://scaneat.id/xendit/ewallet_handling'
            ],
        ];

        // FIXED VIRTUAL ACCOUNTS
        // RETAIL OUTLETS (OTC)
        // INVOICES, PAYMENT REQUEST, PAYMENT METHOD, PAYLATER
        // https://scaneat.id/xendit/handling

        // REPORT, DISBURSEMENT
        // Balance and Transactions report
        // https://scaneat.id/xendit/disbursement_handling

        // E-WALLETS
        // https://scaneat.id/xendit/ewallet_handling


        foreach ($methods as $key => $value) {
            $url = 'https://api.xendit.co/callback_urls/'.$value['method'];

            $xendit_data = DB::table('settings')->where('label', $xendit_type)->first();
            $apiKey = $xendit_data->value;

            $headers = [];
            $headers[] = 'Content-Type: application/json';
            $headers[] = 'for-user-id: ' . $xendit_user_id;
            $data = [
                'url' => $value['url']
            ];

            $curl = curl_init();
            $payload = json_encode($data);
            curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($curl, CURLOPT_USERPWD, $apiKey.":");
            curl_setopt($curl, CURLOPT_URL, $url);
            curl_setopt($curl, CURLOPT_POST, true);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $payload);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            $result = curl_exec($curl);

            dd($result);
        }
    }
}
