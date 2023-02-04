<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
Route::get('/notif/{catalog?}/{invoice?}/{info?}/{status?}', function($catalog,$invoice,$info,$status){
	event(new \App\Events\NotifEvent(['catalog'=>$catalog,'invoice'=>$catalog,'info'=>$info,'status'=>$status,'note'=>'']));
});

// Route::post('/bell', function(Request $request){
// 	event(new \App\Events\NotifEvent(['catalog'=>$request->input('catalog'),'invoice'=>$request->input('invoice'),'info'=>$request->input('info'),'status'=>$request->input('position'),'note'=>'Bell']));
// });
Route::post('/bellnotif', function(Request $request){
	event(new \App\Events\NotifEvent(['catalog'=>$request->input('catalog'),'invoice'=>$request->input('invoice'),'info'=>$request->input('info'),'status'=>$request->input('position'),'note'=>'Bell']));
});
Route::post('/newtransaction', function(Request $request){
	event(new \App\Events\NotifEvent(['catalog'=>$request->input('catalog'),'invoice'=>$request->input('invoice'),'info'=>$request->input('info'),'status'=>$request->input('position'),'note'=>'Front']));
});

Route::group(['prefix'=>'auth'],function () {
	Route::post('/login', 'Auth\LoginController@authLogin');
});

Route::get('/lang', function(Request $request) {
	\Session::put('locale', $request->bhs);
	return redirect()->back();
});

Auth::routes();

Route::group(['middleware'=>'password.edit'],function () {

	Route::get('/', 'DashboardController@index');
	Route::get('/catalog-session/{catalog?}', 'DashboardController@setCatalogSession');
	Route::get('/dahsboard-data', 'DashboardController@getData');
	Route::get('/logout', 'DashboardController@logout');
	Route::get('/email','DashboardController@sendEmail');

	Route::get('/home', function(){
		return redirect('/');
	});

	// Bell
	Route::group(['prefix'=>'bell'],function () {
		Route::post('/data', 'Master\BellController@getData');
	});
	Route::resource('bell','Master\BellController');
	// End

	// Customer
	Route::group(['prefix'=>'customer'],function () {
		Route::post('/data', 'Master\CustomerController@getData');
		Route::get('/export', 'Master\CustomerController@export')->name('customer.export');
		Route::post('/detail-invoice', 'Master\CustomerController@detailInvoice')->name('customer.detailInvoice');
	});
	Route::resource('customer','Master\CustomerController');
	// End

	// Category Item
	Route::group(['prefix'=>'category'],function () {
		Route::post('/data', 'Master\CategoryController@getData');
		Route::get('/delete/{id?}', 'Master\CategoryController@destroy');
	});
	Route::resource('category','Master\CategoryController');
	// End

	Route::group(['prefix'=>'price_type'],function () {
		Route::post('/data', 'Master\PriceTypeController@getData');
		Route::get('/delete/{id?}', 'Master\PriceTypeController@destroy');
	});
	Route::resource('price_type','Master\PriceTypeController');

	// Category Addon
	Route::group(['prefix'=>'categoryadd'],function () {
		Route::post('/data', 'Master\CategoryAddController@getData');
		Route::get('/delete/{id?}', 'Master\CategoryAddController@destroy');
	});
	Route::resource('categoryadd','Master\CategoryAddController');
	// End

	// User
	Route::group(['prefix'=>'user'],function () {
		Route::post('/data', 'Master\UserController@getData');
		Route::get('/delete/{id?}', 'Master\UserController@destroy');
	});
	Route::resource('user','Master\UserController');
	// End

	// Sub Cat
	Route::group(['prefix'=>'subcategory'],function () {
		Route::post('/data', 'Master\SubCategoryController@getData');
		Route::get('/delete/{id?}', 'Master\SubCategoryController@destroy');
	});
	Route::resource('subcategory','Master\SubCategoryController');
	// End

	// Items
	Route::group(['prefix'=>'items'],function () {
		Route::post('/data', 'Master\ItemsController@getData');
		Route::get('/delete/{id?}', 'Master\ItemsController@destroy');
		Route::any('/addons/{id?}', 'Master\ItemsController@addons');
		Route::any('/addaddons/{id?}', 'Master\ItemsController@addaddons');
		Route::any('/ingredient/{id?}', 'Master\ItemsController@ingredient');
		Route::get('/detailingredient/{id?}', 'Master\ItemsController@detailingredient');
		Route::post('/addingredient/{id?}', 'Master\ItemsController@addingredient');
		Route::post('/updateingredient/{id?}', 'Master\ItemsController@updateingredient');
		Route::get('/gallery/{id?}', 'Master\ItemsController@gallery');
		Route::get('/delete/addon/{id?}/{addons?}', 'Master\ItemsController@deleteaddons');
		Route::get('/delete/ingredient/{id?}/{ingredient?}', 'Master\ItemsController@deleteingredient');
		Route::get('/primaryimage/{id?}/{image?}', 'Master\ItemsController@primaryimage');
		Route::get('/deleteimage/{id?}/{image?}/{position?}', 'Master\ItemsController@deleteimage');
	});
	Route::resource('items','Master\ItemsController');
	//End

	// Material
	Route::group(['prefix'=>'material'],function () {
		Route::post('/data', 'Master\MaterialController@getData');
		Route::get('/delete/{id?}', 'Master\MaterialController@destroy');
		Route::get('/detail/{id?}', 'Master\MaterialController@show');
		Route::any('/stock/{id?}', 'Master\MaterialController@stock');
		Route::post('/addstock/{id?}', 'Master\MaterialController@addstock');
		Route::post('/updatestock/{id?}', 'Master\MaterialController@updatestock');
		Route::get('/delete/stock/{id?}/{stock?}', 'Master\MaterialController@deletestock');
	});
	Route::resource('material','Master\MaterialController');
	// End

	// Material
	Route::group(['prefix'=>'stock-addons'],function () {
		Route::post('/data', 'Master\StockAddonController@getData');
		Route::get('/delete/{id?}', 'Master\StockAddonController@destroy');
		Route::get('/detail/{id?}', 'Master\StockAddonController@show');
		Route::any('/stock/{id?}', 'Master\StockAddonController@stock');
		Route::post('/addstock/{id?}', 'Master\StockAddonController@addstock');
		Route::post('/updatestock/{id?}', 'Master\StockAddonController@updatestock');
		Route::get('/delete/stock/{id?}/{stock?}', 'Master\StockAddonController@deletestock');
	});
	Route::resource('stock-addons','Master\StockAddonController');
	// End

	// Catalog Stock
	Route::group(['prefix'=>'stock'],function () {
		Route::post('/data', 'Master\StockController@getData');
		Route::get('/delete/{id?}', 'Master\StockController@destroy');
		Route::get('/detail/{id?}', 'Master\StockController@show');
		Route::any('/stock/{id?}/{catalog?}', 'Master\StockController@stock');

		Route::post('/addstock/{id?}', 'Master\StockController@addstock');
		Route::post('/updatestock/{id?}', 'Master\StockController@updatestock');
		Route::get('/delete/stock/{id?}/{catalog?}', 'Master\StockController@deletestock');
	});
	Route::resource('stock','Master\StockController');
	// End

	// Addons
	Route::group(['prefix'=>'addons'],function () {
		Route::post('/data', 'Master\AddonsController@getData');
		Route::get('/delete/{id?}', 'Master\AddonsController@destroy');
		Route::any('/serving/{id?}', 'Master\AddonsController@serving');
		Route::get('/detailserving/{id?}', 'Master\AddonsController@detailserving');
		Route::post('/addserving/{id?}', 'Master\AddonsController@addserving');
		Route::post('/updateserving/{id?}', 'Master\AddonsController@updateserving');
		Route::get('/delete/serving/{id?}/{serving?}', 'Master\AddonsController@deleteserving');
	});
	Route::resource('addons','Master\AddonsController');
	// End

	// Slider
	Route::group(['prefix'=>'sliders'],function () {
		Route::post('/data', 'Master\SlidersController@getData');
		Route::get('/delete/{id?}', 'Master\SlidersController@destroy');
	});
	Route::resource('sliders','Master\SlidersController');
	// End

	// Voucher
	Route::group(['prefix'=>'voucher'],function () {
		Route::post('/data', 'Master\VoucherController@getData');
		Route::get('/delete/{id?}', 'Master\VoucherController@destroy');
		Route::get('/usagevoucher/{code?}', 'Master\VoucherController@usagevoucher');
	});
	Route::resource('voucher','Master\VoucherController');
	// End

	// loyalty
	Route::group(['prefix'=>'loyalty'],function () {
		Route::post('/data', 'Master\LoyaltyController@getData');
		Route::get('/delete/{id?}', 'Master\LoyaltyController@destroy');
		Route::get('/usageloyalty/{code?}', 'Master\LoyaltyController@usageloyalty');
	});
	Route::resource('loyalty','Master\LoyaltyController');
	// End

	// Package
	Route::group(['prefix'=>'package'],function () {
		Route::post('/data', 'Master\PackageController@getData');
		Route::get('/delete/{id?}', 'Master\PackageController@destroy');
		Route::any('/price/{id?}', 'Master\PackageController@price');
		Route::get('/pricedetail/{id?}', 'Master\PackageController@pricedetail');
		Route::get('/pricedelete/{id?}', 'Master\PackageController@pricedelete');
		Route::post('/addprice', 'Master\PackageController@addprice');
		Route::post('/updateprice', 'Master\PackageController@updateprice');
	});
	Route::resource('package','Master\PackageController');
	// End

	// Main Features
	Route::group(['prefix'=>'mainfeatures'],function () {
		Route::post('/data', 'Master\MainFeaturesController@getData');
		Route::get('/delete/{id?}', 'Master\MainFeaturesController@destroy');
	});
	Route::resource('mainfeatures','Master\MainFeaturesController');
	// End

	// Catalog
	Route::group(['prefix'=>'catalog'],function () {
		Route::get('/otp', 'CatalogController@otp');
		Route::get('/send_otp', 'CatalogController@send_otp');
		Route::post('/process_otp', 'CatalogController@process_otp');

		Route::post('/data', 'CatalogController@getData');
		Route::post('/save_detail', 'CatalogController@saveDetail');
		Route::get('/delete/{id?}', 'CatalogController@destroy');
		Route::any('/items/{id?}', 'CatalogController@items');
		Route::get('/qrcode/{id?}', 'CatalogController@qrcode');
		Route::get('/qriscode/{id?}', 'CatalogController@qriscode');
		Route::any('/additems/{id?}', 'CatalogController@additems');
		Route::any('/add_catalog_item/{id?}', 'CatalogController@addCatalogItem');
		Route::any('/balance/{id?}', 'CatalogController@indexBalance');
		Route::any('/cek_balance/{id?}', 'CatalogController@cekBalance');
		Route::get('/xendit_bank', 'CatalogController@xenditBank');
		Route::any('/price_type/{id}', 'CatalogController@priceType');
		Route::get('/position/category/{catalog?}/{category?}', 'CatalogController@getPositionCategory');
		Route::get('/position/subcategory/{catalog?}/{category?}/{subcategory?}', 'CatalogController@getPositionSubCategory');
		Route::any('/change/position/{catalog?}/{me?}/{current?}/{status?}', 'CatalogController@changeStatus');
		Route::get('/delete/element/{catalog?}/{me?}/{type?}', 'CatalogController@deleteElement');
		Route::get('/available/element/{catalog?}/{me?}/{available?}', 'CatalogController@availableElement');
		Route::any('/item_prices/{id?}', 'CatalogController@item_prices');
		Route::post('/dataPrice', 'CatalogController@getDataPrice');
		Route::any('/manage_item_prices/{id?}/{item?}', 'CatalogController@manage_item_prices');
	});
	Route::resource('catalog','CatalogController');

	Route::get('monitoring-merchant', 'CatalogController@monitoringMerchant')->name('catalog.monitoringMerchant');
	Route::post('monitoring-merchant/data', 'CatalogController@monitoringMerchantData');
	Route::get('monitoring-merchant/detail/{id?}', 'CatalogController@monitoringMerchantDetail')->name('catalog.monitoringMerchantDetail');
	Route::post('monitoring-merchant/import', 'CatalogController@monitoringMerchantImport')->name('catalog.monitoringMerchantImport');
	// End

	Route::group(['prefix'=>'transaction'],function () {
		Route::get('/status/{invoice?}/{status?}/{lunas?}', 'TransactionController@updatestatus');
		Route::get('/lunas/{invoice?}/{status?}/{lunas?}', 'TransactionController@lunas');
		Route::get('/detail/{invoice?}', 'TransactionController@detail');
		Route::get('/detailpopup/{invoice?}', 'TransactionController@detailpopup');
		Route::get('/delete/item/{id?}', 'TransactionController@deleteitem');
		Route::post('/cancel/order', 'TransactionController@cancelorder');
		Route::any('/report/{status?}/{startdate?}/{enddate?}', 'TransactionController@report');
		Route::any('/{status?}', 'TransactionController@index');
		Route::get('/print/{inv?}', 'TransactionController@generateStruk');
		Route::any('/income/report/{status?}/{startdate?}/{enddate?}', 'TransactionController@income');
	});

	Route::group(['prefix'=>'pos'],function () {
		Route::any('/', 'POSController@index');
		Route::any('/table', 'POSController@getTable');
		Route::any('/tablepending', 'POSController@getTablePending');
		Route::any('/tableonline', 'POSController@getTableOnline');
		Route::any('/data', 'POSController@getData');
		Route::post('/update', 'POSController@updateData');
		Route::post('/updatecartbackpayemnt', 'POSController@updateCartBackData');
		Route::post('/updateaddons', 'POSController@updatecartaddons');
		Route::post('/checkout', 'POSController@checkoutData');
		Route::post('/completepending', 'POSController@completePending');
		Route::get('/cancel', 'POSController@cancelData');
		Route::get('/clonedata', 'POSController@getCloneData');
		Route::get('/addons/{item?}/{group?}', 'POSController@addons');
		Route::get('/delete/{id?}', 'POSController@deleteData');
		Route::get('/delete-addon/{detail?}/{group?}', 'POSController@deleteaddon');
		Route::get('/clone/{item?}/{detailinvoice?}', 'POSController@clonedata');
		Route::get('/invoicepending/{id?}', 'POSController@showInvoice');
		Route::get('/detailinvoicepending/{id?}', 'POSController@getDataPending');
		Route::get('/edit/{id?}', 'POSController@edit');
		Route::any('/editdata/{id?}', 'POSController@getEditData');
		Route::get('/detail/{id?}', 'POSController@showDetail');
		Route::get('/selectitem/{id?}', 'POSController@showItem');
	});

	Route::group(['prefix'=>'payment'],function () {
		Route::get('/snap/{total?}','PaymentController@token');
		Route::any('/finish','PaymentController@finishPayment');
	});

	// Member
	Route::group(['prefix'=>'member'],function () {
		Route::post('/data', 'MemberController@getData');
		Route::get('/show/{id?}', 'MemberController@show');
		Route::get('/block/{id?}/{active?}', 'MemberController@block');
	});
	Route::resource('member','MemberController');

	// Account Foodcourt
	Route::group(['prefix'=>'account-foodcourt'],function () {
		Route::post('/data', 'AccountFoodcourtController@getData');
		Route::get('/show/{id?}', 'AccountFoodcourtController@show');
	});
	Route::resource('account-foodcourt','AccountFoodcourtController');

	// Affiliate Foodcourt
	Route::group(['prefix'=>'affiliate-foodcourt'],function () {
		Route::post('/data', 'AffiliateFoodcourtController@getData');
		Route::get('/show/{id?}', 'AffiliateFoodcourtController@show');
	});
	Route::resource('affiliate-foodcourt','AffiliateFoodcourtController');
	// End

	// MASTER INGREDIENT
	// Route::group(['prefix'=>'ingredient'],function () {
	// 	Route::post('/data', 'AffiliateFoodcourtController@getData');
	// 	Route::get('/show/{id?}', 'AffiliateFoodcourtController@show');
	// });
	// Route::resource('ingredient','AffiliateFoodcourtController');
	// End

	// MASTER INGREDIENT
	Route::group(['prefix'=>'ingredient'],function () {
		Route::post('/data', 'Master\IngredientController@getData');
		Route::get('/show/{id?}', 'Master\IngredientController@show');
	});
	Route::resource('ingredient','Master\IngredientController');
	// End

	// Register
	Route::group(['prefix'=>'register'],function () {
		Route::post('/data/{status?}', 'RegisterController@getData');
		Route::get('/show/{id?}', 'RegisterController@show');
		Route::get('/detail/{id?}', 'RegisterController@detail');
		Route::get('/approve/{id?}', 'RegisterController@approve');
		//Route::get('/block/{id?}/{active?}', 'RegisterController@block');
		Route::post('/reject', 'RegisterController@rejected');
		Route::get('/{status?}', 'RegisterController@index');
	});
	Route::resource('register','RegisterController');
	// End

	// Tutorial
	Route::group(['prefix'=>'tutorial'],function () {
		Route::post('/data', 'TutorialController@getData');
		Route::get('/delete/{id?}', 'TutorialController@destroy');
	});
	Route::resource('tutorial','TutorialController');
	//End

	// Complaint
	Route::group(['prefix'=>'complaint'],function () {
		Route::post('/data', 'ComplaintController@getData');
		Route::get('/delete/{id?}', 'ComplaintController@destroy');
	});
	Route::resource('complaint','ComplaintController');
	//End

	// User Complaint
	Route::group(['prefix'=>'user_complaint'],function () {
		Route::post('/data', 'UserComplaintController@getData');
		Route::get('/delete/{id?}', 'UserComplaintController@destroy');
	});
	Route::resource('user_complaint','UserComplaintController');
	//End

	// Service
	Route::group(['prefix'=>'service'],function () {
		Route::post('/data', 'ServiceController@getData');
		Route::get('/delete/{id?}', 'ServiceController@destroy');
	});
	Route::resource('service','ServiceController');
	//End

	// User Service
	Route::group(['prefix'=>'user_service'],function () {
		Route::post('/data', 'UserServiceController@getData');
		Route::get('/delete/{id?}', 'UserServiceController@destroy');
	});
	Route::resource('user_service','UserServiceController');
	//End

	//view menu
	Route::get('/viewmenus/{id}', 'ViewMenusController@index');
	Route::post('/viewmenus/{id}', 'ViewMenusController@update');

	//profile
	Route::get('/profile', 'ProfileController@index');
	Route::post('/profile/{id}', 'ProfileController@update');

	// // MemberStatus
	// Route::group(['prefix'=>'memberstatus'],function () {
	// 	Route::post('/data', 'MemberStatusController@getData');
	// 	Route::get('/show/{id?}', 'MemberStatusController@show');
	// 	Route::get('/perpanjang', 'MemberStatusController@perpanjang');
	// 	Route::post('/perpanjang/paket', 'MemberStatusController@store');
	// 	//Route::get('/block/{id?}/{active?}', 'MemberStatusController@block');
	// });
	// Route::resource('memberstatus','MemberStatusController');

	Route::resource('manage-user', Master\ManageUserController::class);
	Route::resource('table','TableController');
	Route::resource('menu','Master\MenuController');

	Route::get('pengeluaran/delete_detail/{id}','PengeluaranController@delete_detail')->name('pengeluaran.delete_detail');
	Route::get('pengeluaran/detail/{id}','PengeluaranController@detail')->name('pengeluaran.detail');
	Route::post('pengeluaran/detail/{id}','PengeluaranController@save_detail')->name('pengeluaran.save_detail');
	Route::resource('pengeluaran','PengeluaranController');

	Route::get('createMenu/{id}','Master\MenuController@createMenu')->name('create.Menu');
	Route::get('editMenu/{id}/edit','Master\MenuController@editMenu')->name('edit.Menu');
	Route::post('updateMenu/{id}','Master\MenuController@updateMenu')->name('update.Menu');
	Route::post('storeMenu/{id}','Master\MenuController@storeMenu')->name('store.Menu');
	Route::delete('destroyMenu/{id}','Master\MenuController@destroyMenu')->name('destroy.Menu');

	Route::get('list-menu-create-menu-role','Master\MenuController@indexMenuRole')->name('menu.indexMenuRole');
	Route::get('menu-create-menu-role','Master\MenuController@createMenuRole')->name('menu.createMenuRole');
	Route::post('menu-store-menu-role','Master\MenuController@storeMenuRole')->name('menu.storeMenuRole');
	Route::post('save_menu_role','Master\RoleController@saveMenuRole')->name('saveMenuRole');
	Route::resource('role','Master\RoleController');
	Route::resource('menu-roles','MenuRoleController');

	Route::resource('request-catalog','RequestCatalogController');
	Route::resource('catalog-approval','CatalogApproval');
	Route::resource('menu_password','MenuPasswordController');

	Route::get('monitoring-foodcourt/{id?}', 'FoodCourtController@monitoringFoodcourt')->name('foodcourt.monitoring');
	Route::get('get-monitoring-foodcourt/{id?}', 'FoodCourtController@monitoringFoodcourt')->name('foodcourt.monitoring.get');

	Route::get('createFoodcourtCatalog/{id}','FoodCourtController@createFoodcourtCatalog')->name('create.FoodcourtCatalog');
	Route::get('editFoodcourtCatalog/{id}/edit','FoodCourtController@editFoodcourtCatalog')->name('edit.FoodcourtCatalog');
	Route::post('updateFoodcourtCatalog/{id}','FoodCourtController@updateFoodcourtCatalog')->name('update.FoodcourtCatalog');
	Route::post('storeFoodcourtCatalog/{id}','FoodCourtController@storeFoodcourtCatalog')->name('store.FoodcourtCatalog');
	Route::get('checkCatalog','FoodCourtController@checkCatalog')->name('check.Catalog');
	Route::delete('destroyFoodcourtCatalog/{id}','FoodCourtController@destroyFoodcourtCatalog')->name('destroy.FoodcourtCatalog');
	Route::get('qrcodeFoodcourt','FoodCourtController@qrcode')->name('qrcode.foodcourt');
	Route::resource('metode-pembayaran','MetodePembayaranController');
	Route::get('createMetodePembayaran/{id}','MetodePembayaranController@createMetodePembayaran')->name('create.MetodePembayaran');
	Route::get('editMetodePembayaran/{id}/edit','MetodePembayaranController@editMetodePembayaran')->name('edit.MetodePembayaran');
	Route::post('updateMetodePembayaran/{id}','MetodePembayaranController@updateMetodePembayaran')->name('update.MetodePembayaran');
	Route::post('storeMetodePembayaran/{id}','MetodePembayaranController@storeMetodePembayaran')->name('store.MetodePembayaran');
	Route::delete('destroyMetodePembayaran/{id}','MetodePembayaranController@destroyMetodePembayaran')->name('destroy.MetodePembayaran');

	Route::resource('foodcourt','FoodCourtController');
});
