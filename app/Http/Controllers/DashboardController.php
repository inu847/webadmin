<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Helper\myFunction;

use App\Events\NotifEvent;
use App\Models\Invoice;
use App\Models\Catalog;
use App\Models\CatalogDetail;
use App\Models\CatalogItem;
use App\Models\Register;
use App\Models\Items;
use App\Models\InvoiceDetail;
use App\User;

use Carbon\Carbon;
use Carbon\CarbonPeriod;

use Auth;
use Session;
use getData;
use Mail;

class DashboardController extends Controller
{
	public function __construct()
	{
		$this->middleware('auth');
	}
	
    public function index(){
		$data['titlepage']='Dashboard';
		$data['maintitle']='Welcome';

		if (Session::get('catalogsession') == 'All') {
			$catalog_id = getData::getCatalog()->pluck('id');
			$catalog_item = CatalogItem::whereIn('catalog_id', $catalog_id)->get();
			$item_id = array();
			foreach ($catalog_item as $key => $value) {
				$item_id[$key] = $value->item_id;
			}
			$invoiceDetail = InvoiceDetail::whereIn('item_id', $item_id)->get();
			
			$data['daily_sales'] = InvoiceDetail::whereDate('created_at', now())->whereIn('item_id', $item_id)->selectRaw("SUM(price) as amount")->selectRaw("item_id as item_id")->whereIn('item_id', $item_id)->groupBy('item_id')->get();
			$data['monthly_sales'] = InvoiceDetail::whereMonth('created_at', now()->month)->whereYear('created_at', now()->year)->whereIn('item_id', $item_id)->selectRaw("SUM(qty) as qty")->selectRaw("SUM(price) as amount")->selectRaw("item_id as item_id")->groupBy('item_id')->get();

			foreach ($data['daily_sales'] as $key => $value) {
				if ($value->item_id) {
					$data['daily_sales'][$key]['catalog_id'] = CatalogItem::where('item_id', $value->item_id)->first()->catalog_id;
				}
			}
			foreach ($data['monthly_sales'] as $key => $value) {
				$data['monthly_sales'][$key]['catalog_id'] = CatalogItem::where('item_id', $value->item_id)->first()->catalog_id;
			}
			
			$data['daily_sales'] = $data['daily_sales']->unique('catalog_id');
			$data['monthly_sales'] = $data['monthly_sales']->unique('catalog_id');
			
			$data['daily_favorite_item'] = InvoiceDetail::whereDate('created_at', now())->whereIn('item_id', $item_id)->selectRaw("SUM(qty) as qty")->selectRaw("SUM(price) as price")->selectRaw("item_id as item_id")->groupBy('item_id')->limit(10)->get();
			$data['monthly_favorite_item'] = InvoiceDetail::whereMonth('created_at', now()->month)->whereYear('created_at', now()->year)->whereIn('item_id', $item_id)->selectRaw("SUM(qty) as qty")->selectRaw("SUM(price) as price")->selectRaw("item_id as item_id")->groupBy('item_id')->limit(10)->get();

			foreach ($data['daily_favorite_item'] as $key => $value) {
				$data['daily_favorite_item'][$key]['catalog_id'] = CatalogItem::where('item_id', $value->item_id)->first()->catalog_id;
			}
			foreach ($data['monthly_favorite_item'] as $key => $value) {
				$data['monthly_favorite_item'][$key]['catalog_id'] = CatalogItem::where('item_id', $value->item_id)->first()->catalog_id;
			}

			$data['daily_favorite_item'] = $data['daily_favorite_item']->unique('catalog_id');
			$data['monthly_favorite_item'] = $data['monthly_favorite_item']->unique('catalog_id');
			
			// $data['daily_not_favorite_item'] = InvoiceDetail::whereDate('created_at', now())->whereNotIn('item_id', $item_id)->selectRaw("SUM(qty) as qty")->selectRaw("SUM(price) as price")->selectRaw("item_id as item_id")->groupBy('item_id')->limit(10)->pluck('item_id');
			// $data['monthly_not_favorite_item'] = InvoiceDetail::whereMonth('created_at', now()->month)->whereYear('created_at', now()->year)->whereNotIn('item_id', $item_id)->selectRaw("SUM(qty) as qty")->selectRaw("SUM(price) as price")->selectRaw("item_id as item_id")->groupBy('item_id')->limit(10)->pluck('item_id');
			
			$data['daily_not_favorite_item'] = CatalogItem::whereIn('catalog_id', $catalog_id)->whereNotIn('item_id', $data['daily_favorite_item']->pluck('item_id'))->groupBy('catalog_id')->get();
			$data['monthly_not_favorite_item'] = CatalogItem::whereIn('catalog_id', $catalog_id)->whereNotIn('item_id', $data['monthly_favorite_item']->pluck('item_id'))->groupBy('catalog_id')->get();
			// dd($data);
			$daily_not_favorite_item = array();
			$monthly_not_favorite_item = array();
			foreach ($data['daily_not_favorite_item'] as $key => $value) {
				if ($value->item) {
					$daily_not_favorite_item[$key] = $value;
					$daily_not_favorite_item[$key]['qty'] = 0;
					$daily_not_favorite_item[$key]['price'] = 0;
				}
			}
			foreach ($data['monthly_not_favorite_item'] as $key => $value) {
				if ($value->item) {
					$monthly_not_favorite_item[$key] = $value;
					$monthly_not_favorite_item[$key]['qty'] = 0;
					$monthly_not_favorite_item[$key]['price'] = 0;
				}
			}
			
			if ($data['daily_favorite_item']) {
				foreach ($data['daily_favorite_item'] as $key => $value) {
					$data['daily_not_favorite_item']->push($value);
				}
			}
			if($data['monthly_favorite_item']){
				foreach ($data['monthly_favorite_item'] as $key => $value) {
					$data['monthly_not_favorite_item']->push($value);
				}
			}
			$data['daily_not_favorite_item'] = collect($daily_not_favorite_item)->sortBy('qty')->groupBy('catalog_id');
			$data['monthly_not_favorite_item'] = collect($monthly_not_favorite_item)->sortBy('qty')->groupBy('catalog_id');
			// dd($data);
			return view('pages.dashboard.admin', $data);
		}else{
			$catalog_item = CatalogItem::where('user_id', Auth::user()->id)->where('catalog_id', Session::get('catalogsession'))->get();
			$item_id = array();
			foreach ($catalog_item as $key => $value) {
				$item_id[$key] = $value->item_id;
			}
			$invoiceDetail = InvoiceDetail::whereIn('item_id', $item_id)->get();
			$data['daily_sales'] = InvoiceDetail::whereDate('created_at', now())->whereIn('item_id', $item_id)->sum('price');
			$data['monthly_sales'] = InvoiceDetail::whereMonth('created_at', now()->month)->whereYear('created_at', now()->year)->whereIn('item_id', $item_id)->sum('price');
			$data['daily_favorite_item'] = InvoiceDetail::whereDate('created_at', now())->whereIn('item_id', $item_id)->selectRaw("SUM(qty) as qty")->selectRaw("SUM(price) as price")->selectRaw("item_id as item_id")->groupBy('item_id')->get();
			$data['monthly_favorite_item'] = InvoiceDetail::whereMonth('created_at', now()->month)->whereYear('created_at', now()->year)->whereIn('item_id', $item_id)->selectRaw("SUM(qty) as qty")->selectRaw("SUM(price) as price")->selectRaw("item_id as item_id")->groupBy('item_id')->get();
			$daily_favorite_item_id = array();
			$monthly_favorite_item_id = array();
			foreach ($data['daily_favorite_item'] as $key => $value) {
				$daily_favorite_item_id[$key] = $value->item_id;
			}
			foreach ($data['monthly_favorite_item'] as $key => $value) {
				$monthly_favorite_item_id[$key] = $value->item_id;
			}

			$data['daily_not_favorite_item'] = CatalogItem::where('user_id', Auth::user()->id)->where('catalog_id', Session::get('catalogsession'))->whereNotIn('item_id', $daily_favorite_item_id)->get();
			$data['monthly_not_favorite_item'] = CatalogItem::where('user_id', Auth::user()->id)->where('catalog_id', Session::get('catalogsession'))->whereNotIn('item_id', $monthly_favorite_item_id)->get();
			$daily_not_favorite_item = array();
			$monthly_not_favorite_item = array();
			foreach ($data['daily_not_favorite_item'] as $key => $value) {
				if (isset($value->item)) {
					$daily_not_favorite_item[$key] = $value;
					$daily_not_favorite_item[$key]['qty'] = 0;
				}
			}
			foreach ($data['monthly_not_favorite_item'] as $key => $value) {
				if (isset($value->item)) {
					$monthly_not_favorite_item[$key] = $value;
					$monthly_not_favorite_item[$key]['qty'] = 0;
				}
			}
			$data['daily_favorite_item'] = $data['daily_favorite_item']->sortByDesc('qty')->take(5);
			$data['monthly_favorite_item'] = $data['monthly_favorite_item']->sortByDesc('qty')->take(5);
			$data['daily_not_favorite_item'] = collect($daily_not_favorite_item)->sortBy('qty')->groupBy('qty')->take(5);
			$data['monthly_not_favorite_item'] = collect($monthly_not_favorite_item)->sortBy('qty')->groupBy('qty')->take(5);
			return view('pages.dashboard.user', $data);
		}
    }
	
	public function getData(Request $request){
		if(Auth::user()->owner == 1){
			if(Session::get('catalogsession') == 'All'){
				$catalog = Catalog::where('user_id',Auth::user()->id)->orderBy('id','asc')->pluck('id')->toArray();
			}else{
				$catalog = Catalog::where('id',Session::get('catalogsession'))->where('user_id',Auth::user()->id)->orderBy('id','asc')->pluck('id')->toArray();
			}
		}else{
			$catalog = Catalog::where('id',Auth::user()->catalog)->orderBy('id','asc')->pluck('id')->toArray();
		}

		$data['hotitems'] = CatalogDetail::select('catalogdetail.*',
													'category.category_name',
													'items.items_name',
													'items.item_image_primary')
										->leftJoin('category','catalogdetail.category_id','=','category.id')
										->leftJoin('items','catalogdetail.item','=','items.id')
										->whereIn('catalogdetail.catalog_id',$catalog)
										->where('items.sell','>',0)
										->take(6)
										->orderBy('items.sell','desc')
										->get();

		$invoice = Invoice::select('invoice.*','catalog.catalog_title','catalog.catalog_logo','catalog.catalog_username','catalog.domain')
								->leftJoin('catalog','invoice.catalog_id','=','catalog.id')
								->whereIn('invoice.catalog_id',$catalog)
								->where('invoice.invoice_type','Permanent')
								->orderBy('invoice.id','desc')
								->whereMonth('invoice.created_at', '=', date('m'))
								->whereYear('invoice.created_at', '=', date('Y'))
								->get();

		$data['latestactivity'] = $invoice->take(10);

		$invoice_group = $invoice->groupBy('status');
		$data['checkout'] = isset($invoice_group['Checkout']) ? $invoice_group['Checkout'] : [];
		$data['approve'] = isset($invoice_group['Approve']) ? $invoice_group['Approve'] : [];
		$data['process'] = isset($invoice_group['Process']) ? $invoice_group['Process'] : [];
		$data['delivered'] = isset($invoice_group['Delivered']) ? $invoice_group['Delivered'] : [];
		$data['completed'] = isset($invoice_group['Completed']) ? $invoice_group['Completed'] : [];
		$data['cancel'] = isset($invoice_group['Cancel']) ? $invoice_group['Cancel'] : [];

		$now = Carbon::now();
		$startOfMonth = $now->startOfMonth()->format('Y-m-d');
		$endOfMonth = $now->endOfMonth()->format('Y-m-d');

		$period = CarbonPeriod::create($startOfMonth, $endOfMonth);
		// $dates = $period->toArray();

		// Iterate over the period
		$user_days = [];
		$user_total = [];
		foreach ($period as $date) {
			// $user_days[$date->format('d')] = $invoice->filter(function ($item) use ($date) {
			// 	return ($item->created_at >= $date->format('Y-m-d 00:00:00') && $item->created_at >= $date->format('Y-m-d 23:59:59'));
			// })->count();

			$user_days[] = $date->format('d');
			$temp = $invoice->filter(function ($item) use ($date) {
				return ($item->created_at >= $date->format('Y-m-d 00:00:00') && $item->created_at >= $date->format('Y-m-d 23:59:59'));
			});
			// $group_temp = $temp->filter(function ($item) {
			// 	return (!empty($item->phone) || !is_null($item->phone));
			// })->groupBy('phone');
			
			$group_temp = $temp->groupBy('phone');
			$user_total[] = count($group_temp);
		}

		$data['user_days'] = implode(',', $user_days);
		$data['user_total'] = implode(',', $user_total);

		// $data['checkout'] = Invoice::whereIn('invoice.catalog_id',$catalog)->where('status','Checkout')->where('invoice.invoice_type','Permanent')->whereMonth('created_at', '=', date('m'))->whereYear('created_at', '=', date('Y'))->get();
		// $data['approve'] = Invoice::whereIn('invoice.catalog_id',$catalog)->where('status','Approve')->where('invoice.invoice_type','Permanent')->whereMonth('created_at', '=', date('m'))->whereYear('created_at', '=', date('Y'))->get();
		// $data['process'] = Invoice::whereIn('invoice.catalog_id',$catalog)->where('status','Process')->where('invoice.invoice_type','Permanent')->whereMonth('created_at', '=', date('m'))->whereYear('created_at', '=', date('Y'))->get();
		// $data['delivered'] = Invoice::whereIn('invoice.catalog_id',$catalog)->where('status','Delivered')->where('invoice.invoice_type','Permanent')->whereMonth('created_at', '=', date('m'))->whereYear('created_at', '=', date('Y'))->get();
		// $data['completed'] = Invoice::whereIn('invoice.catalog_id',$catalog)->where('status','Completed')->where('invoice.invoice_type','Permanent')->whereMonth('created_at', '=', date('m'))->whereYear('created_at', '=', date('Y'))->get();
		// $data['cancel'] = Invoice::whereIn('invoice.catalog_id',$catalog)->where('status','Cancel')->where('invoice.invoice_type','Permanent')->whereMonth('created_at', '=', date('m'))->whereYear('created_at', '=', date('Y'))->get();

		return view('pages.dashboard.data',$data);
	}
	public function setCatalogSession($catalog){
		Session::forget('catalogsession');
		Session::put('catalogsession',$catalog);
	}
	public function logout(){
		Session::forget('catalogsession');
		Auth::logout();
    	return redirect('/login');
	}
	public function sendEmail(){
        $invoice = Invoice::where('id',4)->first(); // Jangan dibawa

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
            'subject'=>"Checkout Invoice ".$invoice['invoice_number']
        );
        $content = array(
            'invoice' => $invoice,
            'item' => $item
        );
        // Mail::send('pages.email.transaction', $content, function($message) use ($disp)
        // {
        //     $message->from($disp['emailpengirim'], $disp['namapengirim']);
        //     $message->to($disp['email'], $disp['name'])->subject($disp['subject']);
        //     // $message->to('nanangkoesharwanto@gmail.com', 'nanang yoscan')->subject($disp['subject']);
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
