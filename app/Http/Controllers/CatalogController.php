<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Catalog;
use App\Models\Category;
use App\Models\Sliders;
use App\Models\SubCategory;
use App\Models\Items;
use App\Models\CatalogDetail;
use App\Models\Register;
use App\Models\CatalogListType;
use App\Models\CatalogType;
use App\Models\Bank;
use App\Models\NewBank;
use App\Models\Withdrawal;
use App\Models\PriceType;
use App\Models\CatalogPrice;
use App\Models\MetodePembayaran;
use App\Models\MetodePembayaranGroup;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Dompdf\Dompdf;
use App\Helper\myFunction;
use App\Helper\getData;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;
use App\FoodCourt;
use App\FoodCourtCatalog;
use App\Models\CatalogItem;
use App\Models\Invoice;
use Str;
use App\User;

class CatalogController extends Controller
{
  public function __construct()
  {
    $this->middleware('auth');
  }
  public function index(Request $request)
  {
    $data['titlepage']='Catalog';
    $data['maintitle']='Manage Catalog';
    $data['total'] = Catalog::where('user_id',Auth::user()->id)->count();

    return view('pages.catalog.data',$data);
  }
  public function getData(Request $request)
  {
    $columns = ['catalog_username'];
    $keyword = trim($request->input('searchfield'));
    $query = Catalog::where('user_id',Auth::user()->id)
                    ->where(function($result) use ($keyword,$columns){
                        foreach($columns as $column)
                        {
                            if($keyword != ''){
                                $result->orWhere($column,'LIKE','%'.$keyword.'%');
                            }
                        }
                    })
                    ->orderBy('id','desc');
    $data['request'] = $request->all();
    $data['getData'] = $query->paginate(10);
    $data['pagination'] = $data['getData']->links();
    return view('pages.catalog.table',$data);
  }
  public function otp(Request $request)
  {
    // cek otp masih berlaku apa ndak, jika berlaku, maka bisa buka menu nya

    $data['catalog'] = Catalog::where('id', $request->id)->first();
    return view('pages.catalog.formotp',$data);
  }
  public function send_otp(Request $request)
  {
    $data['catalog'] = Catalog::where('id', $request->id)->first();

    // save otp to catalog table
    $data['catalog']->otp = rand(1000, 9999);
    $data['catalog']->otp_expired = Carbon::now()->addHour();
    $data['catalog']->save();

    // send email otp
    $disp = array(
        'email'=>$data['catalog']['email_contact'],
        'name'=>$data['catalog']['catalog_title'],
        'namapengirim'=>'ScanEat Support',
        'emailpengirim'=>'admin@scaneat.id',
        'subject'=>"ScanEat OTP Request"
    );

    $content = array(
        'customer' => [
          'name'=>$data['catalog']['catalog_title'],
          'email'=>$data['catalog']['email_contact'],
          'url'=>'https://'.$data['catalog']['catalog_username'].'.scaneat.id',
          'browser'=>$_SERVER['HTTP_USER_AGENT'],
          'ip'=>$_SERVER['HTTP_HOST'],
          'otp'=>$data['catalog']['otp'],
        ],
    );

    // Mail::send('pages.email.otp', $content, function($message) use ($disp)
    // {
    //   $message->from($disp['emailpengirim'], $disp['namapengirim']);
    //   $message->to($disp['email'], $disp['name'])->subject($disp['subject']);
    // });

    $api_token='0ba0f6d61c459fde8afaedd923c8ae6f'; //silahkan copy dari api token mailketing
    $from_name=$disp['namapengirim']; //nama pengirim
    $from_email=$disp['emailpengirim']; //email pengirim
    $subject=$disp['subject']; //judul email
    $content=view('pages.email.otp', $content)->render(); //isi email format text / html
    $recipient=$disp['email']; //penerima email
    $params = [
      'from_name' => $from_name,
      'from_email' => $from_email,
      'recipient' => $recipient,
      'subject' => $subject,
      'content' => $content,
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

    // back with prev view, with notif email sent
    $data['sent_otp'] = 1;

    return view('pages.catalog.formotp',$data);
  }
  public function process_otp(Request $request)
  {
    $data['catalog'] = Catalog::where('id', $request->id)->first();
    $data['otp_code'] = $request->otp_code;

    if($data['otp_code'] != $data['catalog']['otp']){
      // $data['sent_otp'] = 1;
      // $data['invalid_otp'] = 1;
      // return view('pages.catalog.formotp',$data);

      $status='error';
      $message='Invalid OTP Code.';
    }
    else{
      $to = \Carbon\Carbon::now('Asia/Jakarta');
      $from = \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $data['catalog']['otp_expired'], 'Asia/Jakarta');
      $diff_in_minutes = $to->diffInMinutes($from);

      if($diff_in_minutes < 60){
        $data['catalog']->otp_validation = 1;
        $data['catalog']->save();

        $status='success';
        $message='Your request was successful.';
      }
      else{
        $status='error';
        $message='OTP Code Expired.';
      }
    }

    $notif=['status'=>$status,'message'=>$message];
    return response()->json($notif);
  }
  public function create()
  {
    $data['package']=Register::select('register.*','package.*')
                              ->leftJoin('package','register.package_id','=','package.id')
                              ->where('register.user_id',Auth::user()->id)
                              ->first();
    $data['sliders'] = Sliders::where('user_id',Auth::user()->id)->orderBy('id','desc')->get();
    $data['bank'] = NewBank::orderBy('name')->pluck('name', 'id')->toArray();
    $data['prices'] = PriceType::where('user_id',Auth::user()->id)->orderBy('price_name','asc')->pluck('price_name', 'id')->toArray();
    $data['metode'] = MetodePembayaran::orderBy('name')->pluck('name', 'id')->toArray();

    return view('pages.catalog.formcreate',$data);
  }
  public function store(Request $request)
  {
    // if(count((array)$request->input('items')) > 0){

    // }else{
    // 	$status='error';
    // 	$message='Please select item(s).';
    // }
    $this->validate($request, [
      'domain' => 'required',
      'catalog_username' => 'required|min:3|unique:catalog,catalog_username',
      'catalog_title' => 'required|min:3',
      // 'logo' => 'required|max:1000|mimes:jpeg,jpg,png',
      'logo' => 'required|max:1000|mimes:png',
      // 'distance' => 'required|numeric',
      // 'phone_contact' => 'required',
      // 'email_contact' => 'required|unique:catalog,email_contact',
      // 'catalog_password' => 'required|min:5',
      // 'feature' => 'required',
    ]);
    // if($request->input('feature')=='Full'){
    //   $this->validate($request, [
    //     // 'wa_number' => 'required|min:10|numeric',
    //     // 'wa_show_item' => 'required',
    //     // 'wa_show_cart' => 'required',
    //     // 'checkout_type' => 'required',
    //     // 'tax' => 'required|numeric',
    //     // "payment_method.*"  => "required|string|min:3",
    //   ]);
    // }
    // if ($request->hasFile('catalogbg')) {
    //   $this->validate($request, [
    //     'catalogbg' => 'required|max:1000|mimes:jpeg,jpg,png'
    //   ]);
    // }

    if(Catalog::save_data($request)){
      $status='success';
      $message='Your request was successful.';
    }else{
      $status='error';
      $message='Oh snap! something went wrong.';
    }
    $notif=['status'=>$status,'message'=>$message];
    return response()->json($notif);
  }
  public function show($id)
  {
    $query=Catalog::with('bank')->where('id',$id)->first();

    if($query){
      if(!$query->catalog_key){
        $code = strtoupper(Str::random(7));

        while(true)
        {
            $check_code = Catalog::where('catalog_key', $code)->first();

            if($check_code){
                $code = strtoupper(Str::random(7));
                $check_code = Catalog::where('catalog_key', $code)->first();
            }
            else{
                break;
            }
        }

        $query->catalog_key = $code;
        $query->save();
      }
    }

    return $query;
  }
  public function edit($id)
  {
    $data['package']=Register::select('register.*','package.*')
                              ->leftJoin('package','register.package_id','=','package.id')
                              ->where('register.user_id',Auth::user()->id)
                              ->first();
    $data['getData'] = $this->show($id);
    $data['sliders'] = Sliders::where('user_id',Auth::user()->id)->orderBy('id','desc')->get();
    $data['catalog_list'] = CatalogListType::all();
    $data['catalog_type'] = CatalogType::where('catalog_id', $id)->orderBy('order')->pluck('catalog_list_type_id', 'order');
    $data['bank'] = NewBank::orderBy('name')->pluck('name', 'id')->toArray();
    $data['prices'] = PriceType::where('user_id',Auth::user()->id)->orderBy('price_name','asc')->pluck('price_name', 'id')->toArray();
    // $data['metode'] = MetodePembayaran::orderBy('name')->pluck('name', 'id')->toArray();
    $data['metode'] = MetodePembayaranGroup::with('metode')->where('active', 1)->orderBy('urutan')->get();
    $data['food_court'] = FoodCourt::where('user_id',Auth::user()->id)->where('status', 1)->orderBy('name')->pluck('name', 'id')->toArray();
    $data['catalogs'] = Catalog::where('user_id',Auth::user()->id)->orderBy('catalog_title')->get();
    $data['belongsto_hotel'] = Catalog::where('user_id',Auth::user()->id)->where('hotel_id',$data['getData']->id)->first();

    return view('pages.catalog.form',$data);
  }
  public function update(Request $request, $id)
  {
    $catalog = Catalog::where('id', $request->id)->first();

    $this->validate($request, [
      'domain' => 'required',
      'catalog_username' => 'required|min:3',
      'catalog_title' => 'required|min:3',
      // 'distance' => 'required|numeric',
      'phone_contact' => 'required',
      'email_contact' => 'required|unique:catalog,email_contact,'.$catalog->id,
      'catalog_password' => 'nullable|min:5',
      'feature' => 'required',
    ]);

    if($request->input('feature')=='Full'){
      $this->validate($request, [
        // 'wa_number' => 'required|min:10|numeric',
        // 'wa_show_item' => 'required',
        // 'wa_show_cart' => 'required',
        'checkout_type' => 'required',
        'tax' => 'required|numeric',
        'charge' => 'required|numeric',
        // "payment_method.*"  => "required|string|min:3",
      ]);
    }
    if ($request->hasFile('logo')) {
      $this->validate($request, [
        // 'logo' => 'required|max:1000|mimes:jpeg,jpg,png'
        'logo' => 'required|max:1000|mimes:png'
      ]);
    }
    if ($request->hasFile('catalogbg')) {
      $this->validate($request, [
        'catalogbg' => 'required|max:1000|mimes:jpeg,jpg,png'
      ]);
    }

    $update_data = Catalog::update_data($request);

    if(!$update_data){
      $status='error';
      $message='Oh snap! something went wrong.';
    }
    else{
      if($update_data == 'success'){
        $status='success';
        $message='Your request was successful.';
      }
      else{
        $status='error';
        $message=$update_data;
      }
    }

    $notif=['status'=>$status,'message'=>$message];
    return response()->json($notif);
  }
  public function destroy($id)
  {
    if(Catalog::delete_data($id)){
      $status='success';
      $message='Your request was successful.';
    }else{
      $status='error';
      $message='Oh snap! something went wrong.';
    }
    $notif=['status'=>$status,'message'=>$message];
    return response()->json($notif);
  }
  public function items(Request $request,$id=null)
  {
    $data['catalog']=$this->show($id);
    if($request->isMethod('post')){
      $data['detail'] = CatalogDetail::select('catalogdetail.*',
                                        'category.category_name',
                                        'subcategory.subcategory_name'
                                      )
                                      ->leftJoin('category','catalogdetail.category_id','=','category.id')
                                      ->leftJoin('subcategory','catalogdetail.subcategory_id','=','subcategory.id')
                                      ->where('catalogdetail.catalog_id',$id)
                                      ->orderBy('category_position')
                                      ->groupBy('category_id')
                                      ->get();
      return view('pages.catalog.tableitems',$data);
    }else{
      $data['titlepage']='Catalog Items';
      $data['maintitle']='Manage Catalog Items';

      $detail = CatalogDetail::select('catalogdetail.*',
                                        'category.category_name',
                                        'subcategory.subcategory_name'
                                      )
                                      ->leftJoin('category','catalogdetail.category_id','=','category.id')
                                      ->leftJoin('subcategory','catalogdetail.subcategory_id','=','subcategory.id')
                                      ->where('catalogdetail.catalog_id',$id)
                                      ->orderBy('category_position')
                                      ->groupBy('category_id')
                                      ->get();

      $tree = [];
      foreach($detail as $key => $vdetail){
          $tempmainchildren = [];
          $tempsub = [];

          if(getData::getCatalogSubCategory($id,$vdetail['category_id'])){
            foreach(getData::getCatalogSubCategory($id,$vdetail['category_id']) as $subcategory){
              if($subcategory['subcategory_id'] > 0){

                $tempsubitems = [];
                if(getData::getCatalogSubCategoryItems($id,$vdetail['category_id'],$subcategory['subcategory_id'])){
                  foreach(getData::getCatalogSubCategoryItems($id,$vdetail['category_id'],$subcategory['subcategory_id']) as $subcategoryitem){
                    $tempsubitems[] = [
                      "id" => $subcategoryitem['id'],
                      "title" => $subcategoryitem['items_name'],
                      "http" => "",
                      "item_id" => $subcategoryitem['item'],
                    ];
                  }
                }

                $pretempmainchildren = [
                  "id" => $subcategory['id'],
                  "title" => $subcategory['subcategory_name'],
                  "http" => "",
                  "subcategory_id" => $subcategory['subcategory_id'],
                  "category_id" => $vdetail['category_id'],
                ];

                if($tempsubitems){
                  $pretempmainchildren['children'] = $tempsubitems;
                }

                $tempmainchildren[] = $pretempmainchildren;
              }
              else{
                if(getData::getCatalogItems($id,$vdetail['category_id'],'0')){
                  foreach(getData::getCatalogItems($id,$vdetail['category_id'],'0') as $item){
                    $tempmainchildren[] = [
                      "id" => $item['id'],
                      "title" => $item['items_name'],
                      "http" => "",
                      "item_id" => $item['item'],
                    ];
                  }
                }
              }
            }
          }

          $tempsub = [
            "id" => $vdetail['id'],
            "title" => $vdetail['category_name'],
            "http" => "",
            "category_id" => $vdetail['category_id'],
          ];

          if($tempmainchildren){
            $tempsub['children'] = $tempmainchildren;
          }
          $tree[] = $tempsub;
      }

      $data['tree'] = json_encode($tree);
      return view('pages.catalog.items',$data);
    }
  }
  public function item_prices(Request $request,$id=null)
  {
    $data['catalog']=$this->show($id);
    if($request->isMethod('post')){
      $data['detail'] = CatalogDetail::select('catalogdetail.*',
                                        'category.category_name',
                                        'subcategory.subcategory_name'
                                      )
                                      ->leftJoin('category','catalogdetail.category_id','=','category.id')
                                      ->leftJoin('subcategory','catalogdetail.subcategory_id','=','subcategory.id')
                                      ->where('catalogdetail.catalog_id',$id)
                                      ->orderBy('category_position')
                                      ->groupBy('category_id')
                                      ->get();
      return view('pages.catalog.tableitems',$data);
    }else{
      $data['prices']=CatalogPrice::join('price_types', 'price_types.id', '=', 'catalog_prices.price_type_id')->where('catalog_prices.user_id', Auth::user()->id)->where('catalog_id',$id)->get();
      $data['titlepage']='Catalog Items';
      $data['maintitle']='Manage Catalog Items';
      return view('pages.catalog.data_item',$data);
    }
  }
  public function getDataPrice(Request $request)
  {
    $query = Items::where('user_id',Auth::user()->id)
                    ->where('item_type','Main')
                    ->orderBy('id','desc');

    // if($request->searchCatalog){
      $catalog = trim($request->input('searchCatalog'));
      $items = DB::table('catalog_items')->where([
          'user_id' => Auth::user()->id,
          'catalog_id' => $catalog
      ])->pluck('item_id')->toArray();

      if($items){
        $query = $query->whereIn('id', $items);
      }

      $data['catalog']=$this->show($catalog);
    // }
    $data['prices']=CatalogPrice::join('price_types', 'price_types.id', '=', 'catalog_prices.price_type_id')->where('catalog_prices.user_id', Auth::user()->id)->where('catalog_id',$catalog)->get();

    $data['request'] = $request->all();
    $data['getData'] = $query->paginate(10);
    $data['pagination'] = $data['getData']->links();
    return view('pages.catalog.price_table',$data);
  }
  public function manage_item_prices(Request $request,$id=null,$item=null)
  {
    $data['catalog']=$this->show($id);
    if($request->isMethod('post')){
      if($request->price_type_id){
        $price_type = $request->price_type_id;
        foreach ($price_type as $key => $value) {
          $data_type = [
            'user_id' => Auth::user()->id,
            'catalog_id' => $id,
            'item_id' => $item,
            'price_type_id' => $key
          ];

          $check = DB::table('items_prices')->where($data_type)->first();

          if($check){
            $check->update([
              'items_price' => $value,
            ]);
          }
          else{
            $data_type['items_price'] = $value;
            $insert_catalog_items = DB::table('items_prices')->insert($data_type);
          }
        }
      }
      $status='success';
      $message='';
      $notif=['status'=>$status,'message'=>$message];
      return response()->json($notif);
    }else{
      $data['prices']=CatalogPrice::join('price_types', 'price_types.id', '=', 'catalog_prices.price_type_id')->where('catalog_prices.user_id', Auth::user()->id)->where('catalog_id',$id)->get();
      $data['item'] = Items::where('id',$item)->first();
      $data['catalog_id'] = $id;
      return view('pages.catalog.form_item_price',$data);
    }
  }
  public function getSub($val, $value)
  {
      if($val->sub){
          $val->setRelation('children', $val->sub);
          foreach ($val->sub as $v) {
              if($v->sub){
                  $this->getSub($v, $val);
              }
          }
          $val->unsetRelation('sub');
      }

      return $val;
  }
  public function additems($id=null, Request $request)
  {
    if($request->isMethod('post')){
      $this->validate($request, [
        'category_id' => 'required',
      ]);
      $tree = [];
      if(CatalogDetail::save_data($request)){
        $status='success';
        $message='Your request was successful.';

        $detail = CatalogDetail::select('catalogdetail.*',
                                          'category.category_name',
                                          'subcategory.subcategory_name'
                                        )
                                        ->leftJoin('category','catalogdetail.category_id','=','category.id')
                                        ->leftJoin('subcategory','catalogdetail.subcategory_id','=','subcategory.id')
                                        ->where('catalogdetail.catalog_id',$id)
                                        ->orderBy('category_position')
                                        ->groupBy('category_id')
                                        ->get();

        $tree = [];
        foreach($detail as $key => $vdetail){
            $tempmainchildren = [];
            $tempsub = [];

            if(getData::getCatalogSubCategory($id,$vdetail['category_id'])){
              foreach(getData::getCatalogSubCategory($id,$vdetail['category_id']) as $subcategory){
                if($subcategory['subcategory_id'] > 0){

                  $tempsubitems = [];
                  if(getData::getCatalogSubCategoryItems($id,$vdetail['category_id'],$subcategory['subcategory_id'])){
                    foreach(getData::getCatalogSubCategoryItems($id,$vdetail['category_id'],$subcategory['subcategory_id']) as $subcategoryitem){
                      $tempsubitems[] = [
                        "id" => $subcategoryitem['id'],
                        "title" => $subcategoryitem['items_name'],
                        "http" => "",
                        "item_id" => $subcategoryitem['item'],
                      ];
                    }
                  }

                  $pretempmainchildren = [
                    "id" => $subcategory['id'],
                    "title" => $subcategory['subcategory_name'],
                    "http" => "",
                    "subcategory_id" => $subcategory['subcategory_id'],
                    "category_id" => $vdetail['category_id'],
                  ];

                  if($tempsubitems){
                    $pretempmainchildren['children'] = $tempsubitems;
                  }

                  $tempmainchildren[] = $pretempmainchildren;
                }
                else{
                  if(getData::getCatalogItems($id,$vdetail['category_id'],'0')){
                    foreach(getData::getCatalogItems($id,$vdetail['category_id'],'0') as $item){
                      $tempmainchildren[] = [
                        "id" => $item['id'],
                        "title" => $item['items_name'],
                        "http" => "",
                        "item_id" => $item['item'],
                      ];
                    }
                  }
                }
              }
            }

            $tempsub = [
              "id" => $vdetail['id'],
              "title" => $vdetail['category_name'],
              "http" => "",
              "category_id" => $vdetail['category_id'],
            ];

            if($tempmainchildren){
              $tempsub['children'] = $tempmainchildren;
            }
            $tree[] = $tempsub;
        }

      }else{
        $status='error';
        $message='Oh snap! something went wrong.';
      }

      $notif=['status'=>$status,'message'=>$message,'tree'=>json_encode($tree)];
      return response()->json($notif);
    }else{
      $data['catalogid']=$id;
      // $columns = ['category_name'];
      // $keyword = trim($request->input('searchfield'));
      // $data['category'] = Category::where('user_id',Auth::user()->id)
      //                   ->where('category_type','Main')
      //                   ->where(function($result) use ($keyword,$columns){
      //                       foreach($columns as $column)
      //                       {
      //                           if($keyword != ''){
      //                               $result->orWhere($column,'LIKE','%'.$keyword.'%');
      //                           }
      //                       }
      //                   })
      //                   ->orderBy('id','desc')->get();
      $categories=DB::table('catalog_categories')->where('user_id',Auth::user()->id)->where('catalog_id', $id)->pluck('category_id');
      $data['category'] = Category::where('user_id',Auth::user()->id)->where('category_type','Main')->whereIn('id', $categories)->orderBy('id','desc')->get();

      $sub_categories=DB::table('catalog_sub_categories')->where('user_id',Auth::user()->id)->where('catalog_id', $id)->pluck('sub_category_id');
      $data['subcategory'] = SubCategory::where('user_id',Auth::user()->id)->whereIn('id', $sub_categories)->orderBy('id','desc')->get();

      $items = Items::where('user_id',Auth::user()->id)->where('item_type','Main')->orderBy('id','desc')->get();
      $catalog_items = DB::table('catalog_items')->where('user_id',Auth::user()->id)->where('catalog_id', $id)->pluck('item_id');
      $data['items'] = $items->whereIn('id', $catalog_items);
      $data['all_items'] = $items->whereIn('id', $catalog_items)->pluck('items_name', 'id')->toArray();
      // $data['all_items'] = $items->pluck('items_name', 'id')->toArray();
      $data['tree'] = '';
      return view('pages.catalog.formitems',$data);
    }
  }
  public function addCatalogItem($id=null, Request $request)
  {
    $data['catalogid']=$id;
    $insert_catalog_items = false;
    $duplicate = false;

    if($request->items){
      $items = explode(',', $request->items);

      foreach ($items as $key => $value) {
        $check = DB::table('catalog_items')->where([
          'user_id' => Auth::user()->id,
          'catalog_id' => $id,
          'item_id' => $value
        ])->first();

        if($check){
          $duplicate = true;
        }
        else{
          $insert_catalog_items = DB::table('catalog_items')->insert([
            'user_id' => Auth::user()->id,
            'catalog_id' => $id,
            'item_id' => $value
          ]);
        }
      }
    }

    if($duplicate){
      $status='error';
      $message='Item is existed in Catalog Item.';
    }
    else{
      if($insert_catalog_items){
        $status='success';

        $items = Items::where('user_id',Auth::user()->id)->where('item_type','Main')->orderBy('id','desc')->get();
        $catalog_items = DB::table('catalog_items')->where('user_id',Auth::user()->id)->where('catalog_id', $id)->pluck('item_id');
        $data_items = $items->whereIn('id', $catalog_items);

        $return = '';
        foreach($data_items as $vitems){
          $return .= '<div class="col-md-3 mb-3">
                <img src="'.(strpos($vitems['item_image_primary'], 'amazonaws.com') !== false ? $vitems['item_image_primary'] : str_replace('//liatmenu.id', '//admin.scaneat.id/liatmenu.id', myFunction::getProtocol()).$vitems['item_image_primary'].'?'.time()).'" class="img-fluid" style="max-width:100px; max-height:100px;">
                <div class="custom-checkbox custom-control mt-2">
                    <input type="checkbox" id="item'.$vitems['id'].'" name="items[]" class="custom-control-input" value="'.$vitems['id'].'" />
                    <label class="custom-control-label" for="item'.$vitems['id'].'" style="font-size: 12px;">'.$vitems['items_name'].'</label>
                </div>
            </div>';
        }
        $message=$return;
      }
      else{
        $status='error';
        $message='Oh snap! something went wrong.';
      }
    }
    $notif=['status'=>$status,'message'=>$message];
    return response()->json($notif);
  }
  public function getPositionCategory($catalog=null,$category=null)
  {
    $getpostion = CatalogDetail::where('catalog_id',$catalog)->where('category_id',$category)->orderBy('category_id','desc')->first();
    if($getpostion){
      return $getpostion['category_position'];
    }else{
      $maxposition = CatalogDetail::where('catalog_id',$catalog)->max('category_position');
      return $maxposition+1;
    }
  }
  public function getPositionSubCategory($catalog=null,$category=null,$subcategory=null)
  {
    $getpostion = CatalogDetail::where('catalog_id',$catalog)->where('category_id',$category)->where('subcategory_id',$subcategory)->orderBy('subcategory_id','desc')->first();
    if($getpostion){
      return $getpostion['subcategory_position'];
    }else{
      $maxposition = CatalogDetail::where('catalog_id',$catalog)->where('category_id',$category)->max('subcategory_position');
      return $maxposition+1;
    }
  }
  public function changeStatus(Request $request,$catalog=null,$me=null,$current=null,$status=null)
  {
    if($request->isMethod('post')){
      if(CatalogDetail::change_position($request)){
        $status='success';
        $message='Your request was successful.';
      }else{
        $status='error';
        $message='Oh snap! something went wrong.';
      }
      $notif=['status'=>$status,'message'=>$message];
      return response()->json($notif);
    }else{
      $data['catalog']=$catalog;
      $data['status']=$status;
      $data['current']=$current;
      $data['me']=$me;
      if($status == 'Category'){
        $maxposition = CatalogDetail::where('catalog_id',$catalog)->max('category_position');
        $data['position']=$maxposition;
      }
      elseif($status == 'SubCategory'){
        $data['getData'] = CatalogDetail::where('catalog_id',$catalog)->where('subcategory_id',$me)->first();
        //$data['category'] = Category::where('user_id',Auth::user()->id)->orderBy('id','desc')->get();
        $maxposition = CatalogDetail::where('catalog_id',$catalog)->where('category_id',$data['getData']['category_id'])->max('subcategory_position');
        $data['position']=$maxposition;
      }
      elseif($status == 'Item'){
        $data['getData'] = CatalogDetail::where('id',$me)->first();
        $maxposition = CatalogDetail::where('catalog_id',$data['getData']['catalog_id'])->where('category_id',$data['getData']['category_id'])->where('subcategory_id',$data['getData']['subcategory_id'])->max('item_position');
        $data['position']=$maxposition;
      }
      return view('pages.catalog.position',$data);
    }
  }
  public function deleteElement($catalog=null,$me=null,$type=null)
  {
    if(CatalogDetail::delete_element($catalog,$me,$type)){
      $status='success';
      $message='Your request was successful.';
    }else{
      $status='error';
      $message='Oh snap! something went wrong.';
    }
    $notif=['status'=>$status,'message'=>$message];
    return response()->json($notif);
  }
  public function availableElement($catalog=null,$me=null,$available=null)
  {
    if(CatalogDetail::available_element($catalog,$me,$available)){
      $status='success';
      $message='Your request was successful.';
    }else{
      $status='error';
      $message='Oh snap! something went wrong.';
    }
    $notif=['status'=>$status,'message'=>$message];
    return response()->json($notif);
  }
  public function qrcode($id=null)
  {
    $data['catalog'] = Catalog::where('id',$id)->first();
    $view = view('pages.catalog.qrcode',$data);
    return $view;
    // instantiate and use the dompdf class
    $dompdf = new Dompdf();
    $dompdf->loadHtml($view->render());

    // (Optional) Setup the paper size and orientation
    $dompdf->setPaper('A4', 'landscape');

    // Render the HTML as PDF
    $dompdf->render();

    // Output the generated PDF to Browser
    $dompdf->stream();

    // $pdf = PDF::loadView('pages.catalog.qrcode', $data);
    // return $pdf->download('invoice.pdf');
  }
  public function qriscode($id=null)
  {
    $catalog = Catalog::where('id',$id)->first();

    if(!$catalog->qr_string){
      if($catalog->xendit_user_id){
        $xendit_api = DB::table('settings')->pluck('value', 'label')->toArray();
        $apiKey = $xendit_api[$catalog->online_type];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://api.xendit.co/qr_codes');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, "external_id=".(rand(100,999).'-'.$catalog->catalog_username)."&currency=IDR&type=STATIC&callback_url=https://scaneat.id/api/xendit/qris_handling&amount=1&for-user-id=".$catalog->xendit_user_id);
        curl_setopt($ch, CURLOPT_USERPWD, $apiKey.":");

        $headers = array();
        $headers[] = 'Content-Type: application/x-www-form-urlencoded';
        $headers[] = 'for-user-id: '.$catalog->xendit_user_id;
        // $headers[] = 'Content-Type: application/json';
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        $result = curl_exec($ch);

        if (curl_errno($ch)) {
            // echo 'Error:' . curl_error($ch);
            curl_close($ch);
        }
        else{
            curl_close($ch);
            $qr_string = '';
            if(Request::getHttpHost()=='localhost'){
              $qr_string = '00020101021226660014ID.LINKAJA.WWW011893600911002100622202152007271100622270303UME51450015ID.OR.GPNQR.WWW02150000000000000000303UME520454995802ID5906ScanEat6015Jakarta Selatan61051243062380115IjgNMhECLNVh5NO0715IjgNMhECLNVh5NO530336054061250006304B0EF';
            }else{
                $result = json_decode($result);
                if(isset($result->qr_string)){
                  $qr_string = $result->qr_string;
                }
            }

            $catalog->qr_string = $qr_string;
            $catalog->save();
        }
      }
    }

    $data['catalog'] = $catalog;
    $view = view('pages.catalog.qriscode',$data);
    return $view;
  }
	public function indexBalance(Request $request,$id=null)
  {
    $data['catalog']=$this->show($id);
    if($request->isMethod('post')){

      $status='error';
      $message='Invalid Payment ID';

      if((int) $request->total < 1){
        $status='error';
        $message='Nilai Withdrawal tidak boleh nol.';
      }
      else{
        if(!$data['catalog']->bank_id || !$data['catalog']->bank_account_number || !$data['catalog']->bank_account_name){
          $status='error';
          $message='Bank Data Required.';
        }
        else{
          if($data['catalog']->xendit_user_id){
            $xendit_api = DB::table('settings')->pluck('value', 'label')->toArray();
            $apiKey = $xendit_api[$data['catalog']->online_type];

            if(!$data['catalog']->disbursement_callback){
              $data['catalog'] = $this->setCallback($data['catalog'], $xendit_api);
            }

            // save in withdrawal table
            // get id as external id
            $create['catalog_id']=$id;
            $create['total']=$request->total;
            $create['description']=$request->description;
            $create['bank_code']=$data['catalog']->bank_id;
            $create['bank_account_number']=$data['catalog']->bank_account_number;
            $create['bank_account_name']=$data['catalog']->bank_account_name;
            $create['catalog_email']=$data['catalog']->email_contact;
            $create['catalog_email_cc']=Auth::user()->email;
            $create['catalog_email_bcc']='saktie.banua@gmail.com';
            $withdrawal=Withdrawal::create($create);

            $bank_code = $data['catalog']->bank['code'];
            $external_id = $withdrawal->id."_".rand(9,99);

            $params = [
              // 'for-user-id' => $data['catalog']->xendit_user_id,
              'external_id' => $external_id,
              'amount' => (int) $request->total,
              'bank_code' => $bank_code,
              'account_holder_name' => $data['catalog']->bank_account_name,
              'account_number' => $data['catalog']->bank_account_number,
              'description' => $request->description,
              'X-IDEMPOTENCY-KEY' => $external_id,
              'email_to' => [$data['catalog']->email_contact,Auth::user()->email,'saktie.banua@gmail.com']
              // 'email_cc' => [],
              // 'email_bcc' => []
            ];

            $headers = array();
            $headers[] = 'Content-Type: application/x-www-form-urlencoded';
            $headers[] = 'for-user-id: '.$data['catalog']->xendit_user_id;
            // $headers[] = 'Content-Type: application/json';

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, 'https://api.xendit.co/disbursements');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params));
            curl_setopt($ch, CURLOPT_USERPWD, $apiKey.":");
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            $result = curl_exec($ch);
            $result = json_decode($result);

            // {"status":"PENDING","user_id":"6113249bc45840408cd256f7","external_id":"2_33","amount":50000,"bank_code":"BCA","account_holder_name":"sasasasasas","disbursement_description":"Description","id":"6192f8b29f1be00018626186"}

            if(isset($result->error_code)){
              $delete=Withdrawal::where('id', $withdrawal->id)->delete();
              $status='error';
              // $message=$result->error_code;
              $message=$result->message;
            }
            else{
              $update=Withdrawal::where('id', $withdrawal->id)->update([
                'status' => $result->status,
                'xendit_data' => json_encode($result)
              ]);
              $status='success';
              $message='Your request was successful.';
            }
          }
        }
      }

      $data['titlepage']='Catalog Balance';
      $data['maintitle']='Manage Catalog Balance';
      $data['withdrawal']=Withdrawal::where('catalog_id', $id)->orderBy('updated_at')->orderBy('created_at')->get();
      $data['status']=$status;
      $data['message']=$message;

      return redirect('catalog/balance/'.$id);
      // return view('pages.catalog.balance', $data);
    }
    else{
      $data['titlepage']='Catalog Balance';
      $data['maintitle']='Manage Catalog Balance';
      $data['withdrawal']=Withdrawal::where('catalog_id', $id)->orderBy('updated_at')->orderBy('created_at')->get();
      $data['xendit_trx']=[];
      $catalog = $data['catalog'];
      $data['pending']=0;
      $data['pending_trx']=[];

      if($catalog && $catalog->xendit_user_id){
        $xendit_api = DB::table('settings')->pluck('value', 'label')->toArray();
        $apiKey = $xendit_api['xendit_live'];

        $url = 'https://api.xendit.co/balance?account_type=CASH';
        $headers = [];
        $headers[] = 'Content-Type: application/json';
        $headers[] = 'for-user-id: '.$catalog->xendit_user_id;

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl, CURLOPT_USERPWD, $apiKey.":");
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        $result = curl_exec($curl);
        $result = json_decode($result);

        if(isset($result->error_code)){
        }
        else{
          $balance = $result->balance;
          $catalog->balance = $balance;
          $catalog->save();
        }

        $pending = 0;
        $url = 'https://api.xendit.co/balance?account_type=HOLDING';
        $headers = [];
        $headers[] = 'Content-Type: application/json';
        $headers[] = 'for-user-id: '.$catalog->xendit_user_id;

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl, CURLOPT_USERPWD, $apiKey.":");
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        $result = curl_exec($curl);
        $result = json_decode($result);

        if(isset($result->error_code)){
        }
        else{
          $pending = $result->balance;
        }

        $data['pending']=$pending;

        $url = 'https://api.xendit.co/transactions?limit=50';
        $headers = [];
        $headers[] = 'Content-Type: application/json';
        $headers[] = 'for-user-id: '.$catalog->xendit_user_id;

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl, CURLOPT_USERPWD, $apiKey.":");
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        $result = curl_exec($curl);
        $result = json_decode($result);

        if(isset($result->error_code)){
        }
        else{
          $arr = [];
          foreach($result->data as $value){
            $type= $this->getLabel($value->type, $value->cashflow, $value->channel_category, $value->channel_code);

            if($value->fee->xendit_fee > 0){
              $temp = [];
              $tanggal = Carbon::parse($value->updated)->addHours(7);
              $temp['tanggal'] = $tanggal->format('Y-m-d H:i:s');
              $temp['add_two_days'] = $tanggal->addDays(2)->format('Y-m-d H:i:s');
              $temp['tanggal_label'] = $tanggal->format('d/m/Y H:i');
              $temp['tipe'] = $type . ' Fee';
              $temp['referensi'] = $value->reference_id;
              $temp['status'] = $value->status;
              $temp['jumlah'] = -1 * ($value->fee->xendit_fee + $value->fee->value_added_tax + $value->fee->xendit_withholding_tax + $value->fee->third_party_withholding_tax);
              $arr[] = $temp;
            }

            $temp = [];
            $tanggal = Carbon::parse($value->created)->addHours(7);
            $temp['tanggal'] = $tanggal->format('Y-m-d H:i:s');
            $temp['add_two_days'] = $tanggal->addDays(2)->format('Y-m-d H:i:s');
            $temp['tanggal_label'] = $tanggal->format('d/m/Y H:i');
            $temp['tipe'] = $type;
            $temp['referensi'] = $value->reference_id;

            $temp['status'] = $value->status;
            if(strpos(strtolower($type),'qr code') !== false || strpos(strtolower($type),'ewallet') !== false){
              $diff = $tanggal->diffInDays(Carbon::now(), false);
              if($diff < 0){
                $temp['status'] = 'PENDING';
              }
            }

            $temp['jumlah'] = $value->cashflow == "MONEY_IN" ? $value->amount : -1 * ($value->amount);
            $arr[] = $temp;
          }

          if($result->has_more){
            $url = 'https://api.xendit.co'.$result->links[0]->href;
            $headers = [];
            $headers[] = 'Content-Type: application/json';
            $headers[] = 'for-user-id: '.$catalog->xendit_user_id;

            $curl = curl_init();
            curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($curl, CURLOPT_USERPWD, $apiKey.":");
            curl_setopt($curl, CURLOPT_URL, $url);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            $result = curl_exec($curl);
            $result = json_decode($result);

            if(isset($result->error_code)){
            }
            else{
              foreach($result->data as $value){
                $type= $this->getLabel($value->type, $value->cashflow, $value->channel_category, $value->channel_code);

                if($value->fee->xendit_fee > 0){
                  $temp = [];
                  $tanggal = Carbon::parse($value->updated)->addHours(7);
                  $temp['tanggal'] = $tanggal->format('Y-m-d H:i:s');
                  $temp['add_two_days'] = $tanggal->addDays(2)->format('Y-m-d H:i:s');
                  $temp['tanggal_label'] = $tanggal->format('d/m/Y H:i');
                  $temp['tipe'] = $type . ' Fee';
                  $temp['referensi'] = $value->reference_id;
                  $temp['status'] = $value->status;
                  $temp['jumlah'] = -1 * ($value->fee->xendit_fee + $value->fee->value_added_tax + $value->fee->xendit_withholding_tax + $value->fee->third_party_withholding_tax);
                  $arr[] = $temp;
                }

                $temp = [];
                $tanggal = Carbon::parse($value->created)->addHours(7);
                $temp['tanggal'] = $tanggal->format('Y-m-d H:i:s');
                $temp['add_two_days'] = $tanggal->addDays(2)->format('Y-m-d H:i:s');
                $temp['tanggal_label'] = $tanggal->format('d/m/Y H:i');
                $temp['tipe'] = $type;
                $temp['referensi'] = $value->reference_id;

                $temp['status'] = $value->status;
                if(strpos(strtolower($type),'qr code') !== false || strpos(strtolower($type),'ewallet') !== false){
                  $diff = $tanggal->diffInDays(Carbon::now(), false);
                  if($diff < 0){
                    $temp['status'] = 'PENDING';
                  }
                }

                $temp['jumlah'] = $value->cashflow == "MONEY_IN" ? $value->amount : -1 * ($value->amount);
                $arr[] = $temp;
              }
            }
          }

          $collection = collect($arr);
          // $sorted = $collection->sortBy('tanggal');
          // $sorted->values()->all();
          // dd($sorted);

          $data['xendit_trx']=$collection;
          $data['pending_trx']=$collection->where('status', 'PENDING');
        }
      }

      $data['catalog']=$this->show($id);
      return view('pages.catalog.balance', $data);
    }
  }
  public function getLabel($type, $cashflow, $channel_category, $channel_code)
  {
    if($type == 'PAYMENT' && $channel_category == 'INVOICE' && $cashflow == 'MONEY_IN'){
      $label = 'Invoice (' . str_replace('_', ' ', $channel_code) . ')';
    }
    elseif($type == 'DISBURSEMENT' && $channel_category == 'BANK' && $cashflow == 'MONEY_OUT'){
      $label = 'Disbursement (' . str_replace('_', ' ', $channel_code) . ')';
    }
    elseif($type == 'PLATFORM_FEE' && $channel_category == 'XENPLATFORM' && $cashflow == 'MONEY_OUT'){
      $label = 'Platform Fee Out';
    }
    elseif($type == 'PAYMENT' && $channel_category == 'EWALLET' && $cashflow == 'MONEY_IN'){
      $label = 'eWallet Payment (' . str_replace('_', ' ', $channel_code) . ')';
    }
    else{
      $type = str_replace('_', ' ', $type);
      $type = ucwords($type);
      $channel_category = str_replace('_', ' ', $channel_category);
      $channel_category = ucwords($channel_category);
      $label = $channel_category . ' ' . $type;
    }

    return $label;
  }
  public function cekBalance(Request $request,$id=null)
  {
      $catalog = $this->show($id);
      $xendit_api = DB::table('settings')->pluck('value', 'label')->toArray();
      $apiKey = $xendit_api['xendit_live'];
      $status = false;
      $balance = 0;

      if($catalog && $catalog->xendit_user_id){
        if($request->single){
          $status='error';
          $message='Invalid withdrawal ID.';

          $withdrawal=Withdrawal::where('id', $request->single)->first();
          if($withdrawal){

            $url = 'https://api.xendit.co/disbursements/'.$request->uid;
            $headers = [];
            $headers[] = 'Content-Type: application/json';
            $headers[] = 'for-user-id: '.$catalog->xendit_user_id;

            $curl = curl_init();
            curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($curl, CURLOPT_USERPWD, $apiKey.":");
            curl_setopt($curl, CURLOPT_URL, $url);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            $result = curl_exec($curl);
            $result = json_decode($result);

            if(isset($result->error_code)){
              $status='error';
              $message=$result->error_code.' - '.$result->message;
              $withdrawal->status = $status;
              $withdrawal->save();

              $status = true;
              $message='Update Data Berhasil.';
            }
            else{
              $withdrawal->status = $result->status . (isset($result->failure_code) ? ' ' . $result->failure_code : '');

              $xendit_data = $withdrawal->xendit_data ? json_decode($withdrawal->xendit_data) : '';
              $xendit_data->failure_code = (isset($result->failure_code) ? ' ' . $result->failure_code : '');
              $xendit_data->status = $result->status;
              $withdrawal->xendit_data = json_encode($xendit_data);
              $withdrawal->save();

              // {#1700 ▼
              //   +"status": "FAILED"
              //   //+"user_id": "6113249bc45840408cd256f7"
              //   //+"external_id": "3_81"
              //   //+"amount": 8900
              //   //+"bank_code": "BCA"
              //   //+"account_holder_name": "sasasasasas"
              //   //+"disbursement_description": "morning withdrawal"
              //   +"failure_code": "INSUFFICIENT_BALANCE"
              //   //+"id": "6192fb78b5bdb30018bdbd5b"
              // }

              // {"status":"PENDING","user_id":"6113249bc45840408cd256f7","external_id":"4_60","amount":9900,"bank_code":"BCA","account_holder_name":"sasasasasas","disbursement_description":"morning withdrawal nov","id":"619432a328cbaf0018654831"}

              $status = true;
              $message='Update Data Berhasil.';
            }
          }

          $pending = 0;
          $url = 'https://api.xendit.co/balance?account_type=HOLDING';
          $headers = [];
          $headers[] = 'Content-Type: application/json';
          $headers[] = 'for-user-id: '.$catalog->xendit_user_id;

          $curl = curl_init();
          curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
          curl_setopt($curl, CURLOPT_USERPWD, $apiKey.":");
          curl_setopt($curl, CURLOPT_URL, $url);
          curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
          $result = curl_exec($curl);
          $result = json_decode($result);

          if(isset($result->error_code)){
          }
          else{
            $pending = $result->balance;
          }
          $data['pending']=$pending;

          $data['xendit_trx']=[];
          $url = 'https://api.xendit.co/transactions?limit=50';
          $headers = [];
          $headers[] = 'Content-Type: application/json';
          $headers[] = 'for-user-id: '.$catalog->xendit_user_id;

          $curl = curl_init();
          curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
          curl_setopt($curl, CURLOPT_USERPWD, $apiKey.":");
          curl_setopt($curl, CURLOPT_URL, $url);
          curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
          $result = curl_exec($curl);
          $result = json_decode($result);

          if(isset($result->error_code)){
          }
          else{
            $arr = [];
            foreach($result->data as $value){
              $type= $this->getLabel($value->type, $value->cashflow, $value->channel_category, $value->channel_code);

              if($value->fee->xendit_fee > 0){
                $temp = [];
                $tanggal = Carbon::parse($value->updated)->addHours(7);
                $temp['tanggal'] = $tanggal->format('Y-m-d H:i:s');
                $temp['add_two_days'] = $tanggal->addDays(2)->format('Y-m-d H:i:s');
                $temp['tanggal_label'] = $tanggal->format('d/m/Y H:i');
                $temp['tipe'] = $type . ' Fee';
                $temp['referensi'] = $value->reference_id;
                $temp['status'] = $value->status;
                $temp['jumlah'] = -1 * ($value->fee->xendit_fee + $value->fee->value_added_tax + $value->fee->xendit_withholding_tax + $value->fee->third_party_withholding_tax);
                $arr[] = $temp;
              }

              $temp = [];
              $tanggal = Carbon::parse($value->created)->addHours(7);
              $temp['tanggal'] = $tanggal->format('Y-m-d H:i:s');
              $temp['add_two_days'] = $tanggal->addDays(2)->format('Y-m-d H:i:s');
              $temp['tanggal_label'] = $tanggal->format('d/m/Y H:i');
              $temp['tipe'] = $type;
              $temp['referensi'] = $value->reference_id;

              $temp['status'] = $value->status;
              if(strpos(strtolower($type),'qr code') !== false || strpos(strtolower($type),'ewallet') !== false){
                $diff = $tanggal->diffInDays(Carbon::now(), false);
                if($diff < 0){
                  $temp['status'] = 'PENDING';
                }
              }

              $temp['jumlah'] = $value->cashflow == "MONEY_IN" ? $value->amount : -1 * ($value->amount);
              $arr[] = $temp;
            }

            if($result->has_more){
              $url = 'https://api.xendit.co'.$result->links[0]->href;
              $headers = [];
              $headers[] = 'Content-Type: application/json';
              $headers[] = 'for-user-id: '.$catalog->xendit_user_id;

              $curl = curl_init();
              curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
              curl_setopt($curl, CURLOPT_USERPWD, $apiKey.":");
              curl_setopt($curl, CURLOPT_URL, $url);
              curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
              $result = curl_exec($curl);
              $result = json_decode($result);

              if(isset($result->error_code)){
              }
              else{
                foreach($result->data as $value){
                  $type= $this->getLabel($value->type, $value->cashflow, $value->channel_category, $value->channel_code);

                  if($value->fee->xendit_fee > 0){
                    $temp = [];
                    $tanggal = Carbon::parse($value->updated)->addHours(7);
                    $temp['tanggal'] = $tanggal->format('Y-m-d H:i:s');
                    $temp['add_two_days'] = $tanggal->addDays(2)->format('Y-m-d H:i:s');
                    $temp['tanggal_label'] = $tanggal->format('d/m/Y H:i');
                    $temp['tipe'] = $type . ' Fee';
                    $temp['referensi'] = $value->reference_id;
                    $temp['status'] = $value->status;
                    $temp['jumlah'] = -1 * ($value->fee->xendit_fee + $value->fee->value_added_tax + $value->fee->xendit_withholding_tax + $value->fee->third_party_withholding_tax);
                    $arr[] = $temp;
                  }

                  $temp = [];
                  $tanggal = Carbon::parse($value->created)->addHours(7);
                  $temp['tanggal'] = $tanggal->format('Y-m-d H:i:s');
                  $temp['add_two_days'] = $tanggal->addDays(2)->format('Y-m-d H:i:s');
                  $temp['tanggal_label'] = $tanggal->format('d/m/Y H:i');
                  $temp['tipe'] = $type;
                  $temp['referensi'] = $value->reference_id;

                  $temp['status'] = $value->status;
                  if(strpos(strtolower($type),'qr code') !== false || strpos(strtolower($type),'ewallet') !== false){
                    $diff = $tanggal->diffInDays(Carbon::now(), false);
                    if($diff < 0){
                      $temp['status'] = 'PENDING';
                    }
                  }

                  $temp['jumlah'] = $value->cashflow == "MONEY_IN" ? $value->amount : -1 * ($value->amount);
                  $arr[] = $temp;
                }
              }
            }

            $collection = collect($arr);
            $data['xendit_trx']=$collection;
            $data['pending_trx']=$collection->where('status', 'PENDING');
          }

          $data['catalog']=$catalog;
          $data['titlepage']='Catalog Balance';
          $data['maintitle']='Manage Catalog Balance';
          $data['withdrawal']=Withdrawal::where('catalog_id', $id)->orderBy('updated_at')->orderBy('created_at')->get();
          $data['status']=$status;
          $data['message']=$message;

          return view('pages.catalog.balance', $data);
        }
        else{
          $url = 'https://api.xendit.co/balance?account_type=CASH';
          $headers = [];
          $headers[] = 'Content-Type: application/json';
          $headers[] = 'for-user-id: '.$catalog->xendit_user_id;

          $curl = curl_init();
          curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
          curl_setopt($curl, CURLOPT_USERPWD, $apiKey.":");
          curl_setopt($curl, CURLOPT_URL, $url);
          curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
          $result = curl_exec($curl);
          $result = json_decode($result);

          if(isset($result->error_code)){
          }
          else{
            $balance = $result->balance;
            $catalog->balance = $balance;
            $catalog->save();
            $status = true;
          }
        }
      }

      $notif=['status'=>$status,'balance'=>number_format($balance)];
      return response()->json($notif);
  }
  public function setCallback($catalog, $xendit_api)
  {
      $apiKey = $xendit_api['xendit_live'];
      $status = false;

      if($catalog && $catalog->xendit_user_id){
          $status='error';
          $message='Invalid Process.';

          $url = 'https://api.xendit.co/callback_urls/disbursement';
          $headers = [];
          $headers[] = 'Content-Type: application/json';
          $headers[] = 'for-user-id: '.$catalog->xendit_user_id;

          $params = [
            'url' => 'https://scaneat.id/api/xendit/disbursement_handling',
          ];
          $payload = json_encode($params);

          $ch = curl_init();
          curl_setopt($ch, CURLOPT_URL, $url);
          curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
          curl_setopt($ch, CURLOPT_POST, 1);
          curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
          curl_setopt($ch, CURLOPT_USERPWD, $apiKey.":");
          curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
          $result = curl_exec($ch);
          $result = json_decode($result);

          if(isset($result->error_code)){
          }
          else{
            $catalog->disbursement_callback = 'https://scaneat.id/api/xendit/disbursement_handling';
            $catalog->save();
            $status = $catalog;
          }
      }

      return $status;
  }
  public function saveDetail(Request $request)
  {
    $categories = json_decode($request->str);
    $id = $request->id;

    $datas = $request->all();
    // return $datas;
    if ($datas['str'] == "[]") {
      $catalogDetail = CatalogDetail::where('catalog_id', $id)->get();
      foreach ($catalogDetail as $detail) {
        $catalogItem = CatalogItem::where('catalog_id', $id)->where('item_id', $detail->item)->first();
        $catalogItem->status = 0;
        $catalogItem->save();

        $detail->delete();
      }
    }

    // CatalogDetail::where('catalog_id',$id)->delete();

    $this->ids = [];
    $this->no = [];
    $this->subcats = [];
    $this->temp_ids = [];

    if($categories){
        // update category_id for all items
        foreach ($categories as $key => $value) {
          $parent_id = $value->id;
          if(isset($value->children)){
            $this->childrenDataTemp($value->children, $parent_id, $id, $value->id);
          }
        }

        if($this->temp_ids){
          foreach ($this->temp_ids as $tkey => $tvalue) {
            $temp_cat_id = CatalogDetail::find($tkey);
            CatalogDetail::whereIn('id',$tvalue)->update([
              'category_id' => $temp_cat_id->category_id,
            ]);
          }
        }

        foreach ($categories as $key => $value) {
            $new_data = [
                  'parent_id' => null,
                  'urutan' => $key+1
            ];

            if(isset($value->id)){
                $new = CatalogDetail::whereId($value->id)->update($new_data);
                $parent_id = $value->id;
            }
            else{
                $new = CatalogDetail::create($new_data);
                $parent_id = $new->id;
            }

            if(isset($value->category_id) && !empty($value->category_id)){
              CatalogDetail::where('catalog_id', $id)->where('category_id', $value->category_id)->update([
                'category_position' => $key+1
              ]);
            }

            if(isset($value->children)){
              foreach ($value->children as $ckey => $cvalue) {
                $new_data = [
                    'parent_id' => $cvalue->id,
                    'urutan' => $ckey+1,
                    'category_id' => $value->category_id,
                    'subcategory_id' => $cvalue->subcategory_id
                ];
                // return $new_data;
                if(isset($cvalue->id)){
                    $new = CatalogDetail::whereId($cvalue->id)->update($new_data);
                    $parent_id = $value->id;
                    $this->ids[$cvalue->id] = $cvalue->id;
                }
                else{
                    $new_data['item'] = $value->id;
                    $new_data['category_position'] = $key+1;
                    $new_data['user_id'] = Auth::user()->id;
                    $new_data['catalog_id'] = $id;

                    $new = CatalogDetail::create($new_data);
                    $parent_id = $new->id;
                }

                if(isset($cvalue->category_id) && !empty($cvalue->category_id)){
                  CatalogDetail::where('catalog_id', $id)->where('category_id', $value->category_id)->where('subcategory_id', $cvalue->subcategory_id)->update([
                    'subcategory_position' => $ckey+1
                  ]);
                }

                if(isset($cvalue->children)){
                  foreach ($cvalue->children as $ikey => $ivalue) {
                    $new_data = [
                          'parent_id' => $cvalue->id,
                          'urutan' => $ikey+1,
                          'category_id' => $value->category_id,
                          'subcategory_id' => $cvalue->subcategory_id
                    ];
                    if(isset($ivalue->id)){
                        $new = CatalogDetail::whereId($ivalue->id)->update($new_data);
                        // $parent_id = $cvalue->id;
                        $this->ids[$ivalue->id] = $ivalue->id;
                    }
                    else{
                        $new_data['item'] = $ivalue->id;
                        $new_data['category_position'] = $key+1;
                        $new_data['subcategory_position'] = $ckey+1;
                        $new_data['user_id'] = Auth::user()->id;
                        $new_data['catalog_id'] = $id;

                        $new = CatalogDetail::create($new_data);
                        // $parent_id = $new->id;
                        $this->ids[$new->id] = $new->id;
                    }

                    CatalogDetail::where('catalog_id', $id)
                      ->where('category_id', $value->category_id)
                      ->where('subcategory_id', $cvalue->subcategory_id)
                      ->where('item', $ivalue->item_id)
                      ->update([
                        'item_position' => $ikey+1
                      ]);

                    if(isset($ivalue->children)){
                      // // $this->subcats = [];
                      // $this->childrenData($value->children, $parent_id, $id);
                    }
                  }
                }
              }
            }
        }
    }

    if(count($this->ids)){
      $catalogDetail = CatalogDetail::where('catalog_id',$id)->whereNotIn('id', $this->ids)->get();
      foreach ($catalogDetail as $key => $value) {
        $catalogItem = CatalogItem::where('catalog_id', $id)->where('item_id', $value->item)->first();
        $catalogItem->status = 0;
        $catalogItem->save();

        $value->delete();
      }
    }

    $detail = CatalogDetail::select('catalogdetail.*',
                                      'category.category_name',
                                      'subcategory.subcategory_name'
                                    )
                                    ->leftJoin('category','catalogdetail.category_id','=','category.id')
                                    ->leftJoin('subcategory','catalogdetail.subcategory_id','=','subcategory.id')
                                    ->where('catalogdetail.catalog_id',$id)
                                    ->orderBy('category_position')
                                    ->groupBy('category_id')
                                    ->get();

    $tree = [];
    foreach($detail as $key => $vdetail){
        $tempmainchildren = [];
        $tempsub = [];

        if(getData::getCatalogSubCategory($id,$vdetail['category_id'])){
          foreach(getData::getCatalogSubCategory($id,$vdetail['category_id']) as $subcategory){
            if($subcategory['subcategory_id'] > 0){

              $tempsubitems = [];
              if(getData::getCatalogSubCategoryItems($id,$vdetail['category_id'],$subcategory['subcategory_id'])){
                foreach(getData::getCatalogSubCategoryItems($id,$vdetail['category_id'],$subcategory['subcategory_id']) as $subcategoryitem){
                  $tempsubitems[] = [
                    "id" => $subcategoryitem['id'],
                    "title" => $subcategoryitem['items_name'],
                    "http" => "",
                    "item_id" => $subcategoryitem['item'],
                  ];
                }
              }

              $pretempmainchildren = [
                "id" => $subcategory['id'],
                "title" => $subcategory['subcategory_name'],
                "http" => "",
                "subcategory_id" => $subcategory['subcategory_id'],
                "category_id" => $vdetail['category_id'],
              ];

              if($tempsubitems){
                $pretempmainchildren['children'] = $tempsubitems;
              }

              $tempmainchildren[] = $pretempmainchildren;
            }
            else{
              if(getData::getCatalogItems($id,$vdetail['category_id'],'0')){
                foreach(getData::getCatalogItems($id,$vdetail['category_id'],'0') as $item){
                  $tempmainchildren[] = [
                    "id" => $item['id'],
                    "title" => $item['items_name'],
                    "http" => "",
                    "item_id" => $item['item'],
                  ];
                }
              }
            }
          }
        }

        $tempsub = [
          "id" => $vdetail['id'],
          "title" => $vdetail['category_name'],
          "http" => "",
          "category_id" => $vdetail['category_id'],
        ];

        if($tempmainchildren){
          $tempsub['children'] = $tempmainchildren;
        }
        $tree[] = $tempsub;
    }

    $data['tree'] = json_encode($tree);
    return response()->json($data);
  }
  public function childrenData($data = [], $parent_id = null, $id = null)
  {
      if($data && $parent_id){
          foreach ($data as $key => $value) {
              $new_data = [
                      'parent_id' => $parent_id,
                      'urutan' => $key+1
              ];

              if(isset($value->id)){
                  CatalogDetail::whereId($value->id)->update($new_data);
                  $new_parent_id = $value->id;
              }
              else{
                  $new = CatalogDetail::create($new_data);
                  $new_parent_id = $new->id;
              }

              if(isset($value->subcategory_id) && !empty($value->subcategory_id)){
                if(!isset($this->no[$value->category_id])){
                  $this->no[$value->category_id] = 0;
                }
                $this->no[$value->category_id]++;

                $check_sub = CatalogDetail::where('catalog_id', $id)
                ->where('category_id', $value->category_id)
                ->where('subcategory_id', $value->subcategory_id)
                ->update([
                    'subcategory_position' => $this->no[$value->category_id]
                  ]);

                $this->subcats[] = $value->subcategory_id;
              }
              else{
                $this->ids[$new_parent_id] = $new_parent_id;
              }

              if(isset($value->children)){
                  $this->childrenData($value->children, $new_parent_id, $id);
              }
          }
      }

      return 1;
  }
  public function childrenDataTemp($data = [], $parent_id = null, $id = null, $cat_id = null)
  {
      if($data && $parent_id){
          foreach ($data as $key => $value) {
              $this->temp_ids[$cat_id][$value->id] = $value->id;

              $new_parent_id = $parent_id;
              if(isset($value->id)){
                  $new_parent_id = $value->id;
              }

              if(isset($value->children)){
                  $this->childrenDataTemp($value->children, $new_parent_id, $id, $cat_id);
              }
          }
      }

      return 1;
  }

  // list supported xendit bank for disbursements
  public function xenditBank(Request $request)
  {
      $xendit_api = DB::table('settings')->pluck('value', 'label')->toArray();
      $apiKey = $xendit_api['xendit_live'];

      $url = 'https://api.xendit.co/available_disbursements_banks';
      $headers = [];
      $headers[] = 'Content-Type: application/json';

      $curl = curl_init();
      curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
      curl_setopt($curl, CURLOPT_USERPWD, $apiKey.":");
      curl_setopt($curl, CURLOPT_URL, $url);
      curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
      $result = curl_exec($curl);
      $result = json_decode($result);

      if(isset($result->error_code)){
        $status = false;
      }
      else{
        $status = true;
        foreach ($result as $key => $value) {
          if($value->can_disburse){
            NewBank::updateOrCreate(
                ['code' => $value->code],
                ['name' => $value->name]
            );
          }
        }
      }

      $notif=['status'=>$status];
      return response()->json($notif);
  }

  public function monitoringMerchant()
  {
    $data['titlepage']='Manage & Monitoring Account';
    $data['maintitle']='Manage & Monitoring Account';
    $data['searchMonth'] = request()->searchMonth ? request()->searchMonth : date('m');
    $data['searchYear'] = request()->searchYear ? request()->searchYear : date('Y');
    $data['searchfield'] = request()->searchfield ?? '';
    // $data['catalogs']=Catalog::where('user_id', $id)->get();
    return view('pages.monitoringMerchant.index', $data);
  }

  public function monitoringMerchantData()
  {
    // get all user owner
    $columns = ['username','name','email','phone'];
    $keyword = trim(request()->searchfield);

    $query = User::with(['catalog', 'member', 'affiliate'])
      // ->where('level','Member')
      ->where('owner',1)
      // ->whereNull('parent_id')
      ->where(function($result) use ($keyword,$columns) {
          foreach($columns as $column)
          {
              if($keyword != ''){
                  $result->orWhere($column,'LIKE','%'.$keyword.'%');
              }
          }
      })
      ->orderBy('name')->paginate(10);

    // get all user transaction
    foreach ($query as $key => $value) {
      // get all user catalogs
      $catalogs = Catalog::where('user_id', $value->id)->get();
      $value->catalogs = $catalogs->pluck('catalog_title', 'id')->toArray();

      // get all invoice based on user catalogs
      $invoice = Invoice::select(['amount', 'payment_method'])->whereIn('catalog_id', $catalogs->pluck('id')->toArray());

      if(request()->searchMonth && request()->searchYear){
        if(request()->searchMonth != 'all'){
          $invoice->whereMonth('created_at', request()->searchMonth);
        }
        $invoice->whereYear('created_at', request()->searchYear);
      }

      $invoice = $invoice->get();

      // transaksiTunai
      $total_tunai = $invoice->where('payment_method', 1)->sum('amount');
      $value->total_tunai = $total_tunai;

      // transaksiOnline
      $total_online = $invoice->filter(function ($item){
            return $item->payment_method != 1 || is_null($item->payment_method);
        })->sum('amount');
      $value->total_online = $total_online;

      $grand_total = $total_tunai + $total_online;
      $value->grand_total = $grand_total;

      $one_percent = (1/100) * $grand_total;
      $value->affiliate_income = ($value->affiliate_percent/100) * $one_percent;
    }

    $data['request'] = request()->all();
    $data['getData'] = $query;
    $data['pagination'] = $data['getData']->links();
    return view('pages.monitoringMerchant.table',$data);
  }

  public function monitoringMerchantDetail(Request $request, $id)
  {
    $catalogs = Catalog::where('user_id', $id)->get();

    $invoice = Invoice::select(['catalog_id', 'amount', 'payment_method'])->selectRaw('SUM(amount) as amount')->whereIn('catalog_id', $catalogs->pluck('id')->toArray());

    if(request()->searchMonth && request()->searchYear){
      if(request()->searchMonth != 'all'){
        $invoice->whereMonth('created_at', request()->searchMonth);
      }
      $invoice->whereYear('created_at', request()->searchYear);
    }

    $data['invoice'] = $invoice->groupBy('catalog_id')->get();

    foreach ($catalogs as $val) {
      $exsited_catalog = null;
      foreach ($data['invoice'] as $key => $value) {
        if($value->catalog_id == $val->id){
            $exsited_catalog = $value->catalog_id;

            $where = [
              'catalog_id' => $value->catalog_id
            ];

            $transaksiTunai = Invoice::where($where)->where('payment_method', 1);
            if(request()->searchMonth && request()->searchYear){
              if(request()->searchMonth != 'all'){
                $transaksiTunai->whereMonth('created_at', request()->searchMonth);
              }
              $transaksiTunai->whereYear('created_at', request()->searchYear);
            }
            $transaksiTunai = $transaksiTunai->sum('amount');

            $data['invoice'][$key]['transaksiTunai'] = $transaksiTunai;


            $transaksiOnline = Invoice::where($where)->where(function($query){
                $query->whereNull('payment_method',);
                $query->orWhere('payment_method', '<>', 1);
              });
            if(request()->searchMonth && request()->searchYear){
              $transaksiOnline->whereMonth('created_at', request()->searchMonth);
              $transaksiOnline->whereYear('created_at', request()->searchYear);
            }
            $transaksiOnline = $transaksiOnline->sum('amount');

            $data['invoice'][$key]['transaksiOnline'] = $transaksiOnline;
        }
      }

      if(!$exsited_catalog){
        $object = new \stdClass();
        $object->catalog_id = $val->id;
        $object->amount = 0;
        $object->payment_method = null;
        $object->transaksiTunai = 0;
        $object->transaksiOnline = 0;

        $catalog_data = new \stdClass();
        $catalog_data->catalog_title = $val->catalog_title;
        $catalog_data->email_contact = $val->email_contact;
        $object->catalog = $catalog_data;

        $data['invoice'][] = $object;
      }
    }

    // dd($data);

    return view('pages.monitoringMerchant.table_detail', $data);
  }

  public function monitoringMerchantImport(Request $request)
  {
    $status='error';
    $message='Error Importing Data.';
    $link = '';

    $catalog = Catalog::where('id', $request->catalogs)->first();

    if ($catalog) {
      if($catalog->xendit_user_id){

        $xendit_api = DB::table('settings')->pluck('value', 'label')->toArray();
        $apiKey = $xendit_api[$catalog->online_type];

        $params = [
          // 'type' => 'TRANSACTIONS', // 'BALANCE_HISTORY', //
          'type' => $request->import_type,
          'status' => 'COMPLETED',
          'filter' => [
            'from' => date('c', strtotime($request->date_from . ' 00:00:00')),
            'to' => date('c', strtotime($request->date_to . ' 23:59:59')),
          ],
        ];

        $headers = array();
        $headers[] = 'for-user-id: '.$catalog->xendit_user_id;
        $headers[] = 'Content-Type: application/json';

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://api.xendit.co/reports');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($params));
        curl_setopt($ch, CURLOPT_USERPWD, $apiKey.":");
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        $result = curl_exec($ch);
        $result = json_decode($result);

        sleep(3);

        if(isset($result->id)){
          $url = 'https://api.xendit.co/reports/'.$result->id;
          $headers = [];
          $headers[] = 'for-user-id: '.$catalog->xendit_user_id;
          $headers[] = 'Content-Type: application/json';

          $curl = curl_init();
          curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
          curl_setopt($curl, CURLOPT_USERPWD, $apiKey.":");
          curl_setopt($curl, CURLOPT_URL, $url);
          curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
          $res = curl_exec($curl);
          $res = json_decode($res);

          if(isset($result->error_code)){
            $status='error';
            // $message=$result->error_code;
            $message=$result->message;
          }
          else{
            $link = $res->url;
            $status='success';
            $message='Your request was successful.';
          }
        }
      }
    }

    $notif=[];
    return response()->json([
      'status'=>$status,
      'message'=>$message,
      'link'=>$link,
    ]);
  }
}
