<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

use App\User;

use Auth;
use Session;

class ViewMenusController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index(Request $request, $id)
    {
        $catalog = DB::table('catalog')->where('id', $id)->first();
        $data['catalog']=$catalog;
        // $data['titlepage']='View Menus';
		// $data['maintitle']='Setting View Menu : ' . $catalog->catalog_title;
        $data['titlepage']='Catalog Items';
        $data['maintitle']='Manage Catalog Items';
  
		$data['id']=$id;
        $data['view_subcat'] = $catalog->view_subcat;
        $data['view_item'] = $catalog->view_item;
        $data['theme'] = $catalog->theme;

        return view('viewmenus',$data);
    }

    // public function index(Request $request, $id)
    // {
    //     $email = Auth::user()->email; $id = Auth::id();
    //     $data['titlepage']='View Menus';
	// 	$data['maintitle']='Setting View Menu';
	// 	$data['id']=$id;
    //     $data['view_subcat'] = DB::table('catalog')
    //         ->select('catalog.view_subcat')
    //         ->where('user_id', '=', $id, 'OR', 'email_contact', '=', $email)->first();
    //     $data['view_item'] = DB::table('catalog')
    //         ->select('catalog.view_item')
    //         ->where('user_id', '=', $id, 'OR', 'email_contact', '=', $email)->first();
    //     //dump(Auth::user()->email);
    //     return view('viewmenus',$data);
    // }

    public function update(Request $request, $id) {
        if ($request->theme == 'default') {
            $request->theme = null;
        }
        DB::table('catalog')
            ->where('id', '=', $id, 'OR', 'email_contact', '=', $id)
            ->update(
            [
                'view_subcat' => $request->view_subcat,
                'view_item' => $request->view_item,
                'theme' => $request->theme
            ]
        );
        
        $status='success';
        $message='Your request was successful.';
        $notif=['status'=>$status,'message'=>$message];

        return redirect('viewmenus/'.$id)->with($notif);
        // return redirect()->route('catalog.index')->with($notif);
    }
}
