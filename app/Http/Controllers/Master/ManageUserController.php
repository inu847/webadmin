<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\Catalog;
use App\Models\Invoice;
use App\Models\Package;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Auth;
use Str;
use Storage;

class ManageUserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data['titlepage']='Manage User';
        $data['maintitle']='Manage User';
        $users = User::orderBy('id', 'desc')->get();

        return view('pages.manageUser.index', $data, ['users' => $users]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $data['titlepage']='Create User';
        $data['maintitle']='Create User';
        $users = User::where('as_affiliate', 1)->orderBy('name', 'asc')->get();
        // $catalogs = Catalog::orderBy('id', 'desc')->get();
        $affiliate = null;
        if(request('affiliate')){
            $affiliate = 1;
        }

        return view('pages.manageUser.create', $data, ['users' => $users, 'affiliate' => $affiliate]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|min:3',
            // 'catalog' => 'required',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:5',
            'repassword' => 'required_with:password|same:password|min:5',
            'phone' => 'required|min:10',
            'photos' => 'mimes:jpg,png,jpeg',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->with('failed', $validator->messages()->first());
        }

        $data = $request->all();
        $data['password'] = Hash::make($data['password']);
        // $photo = $request->file('photos');
        // if ($photo) {
        //     $file = $photo->store('photo', 'public');
        //     $data['photo'] = $file;
        // }

        $insert = User::create($data);

        if($insert){
            if($request->hasFile('photos')){
                $subpath = 'users/'.$insert->id.'/photos';
                $file = $request->file('photos');
                $filename=Str::slug(trim($request->name),"_").'_'.$insert->id;
                $extension = $file->extension();
                $filenametostore = $filename.'.'.$extension;
                Storage::disk('s3')->putFileAs($subpath, $file, $filenametostore);
                $url = Storage::disk('s3')->url($subpath.'/'.$filenametostore);
                $data['photo'] = $url;
            }

            $status='success';
            $message='Your request was successful.';
        }else{
            $status='error';
            $message='Oh snap! something went wrong.';
        }

        $notif=['status'=>$status,'message'=>$message];
        return response()->json($notif);

        // return redirect()->route('manage-user.index')->with('success', 'input data success!');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $data['titlepage']='Detail User';
        $data['maintitle']='Detail User';
        $detail = User::with(['catalog', 'member'])->findOrFail($id);

        // AFFILATE
        $affiliate = User::where('affiliate_id', $id)
        ->where('owner',1)
        ->orderBy('name')->get();
        
        // get all user transaction
        foreach ($affiliate as $key => $value) {
            // get all user catalogs
            $catalogs = Catalog::where('user_id', $value->id)->get();
            $value->catalogs = $catalogs->pluck('catalog_title', 'id')->toArray();
        
            // get all invoice based on user catalogs
            $invoice = Invoice::select(['amount', 'payment_method'])
            ->whereIn('catalog_id', $catalogs->pluck('id')->toArray());

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

        return view('pages.manageUser.detail', $data, ['detail' => $detail, 'affiliate' => $affiliate]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $data['titlepage']='Edit User';
        $data['maintitle']='Edit User';
        $edit = User::findOrFail($id);
        $users = User::where('as_affiliate', 1)->orderBy('name', 'asc')->get();
        // $catalogs = Catalog::orderBy('id', 'desc')->get();
        $affiliate = null;
        if(request('affiliate')){
            $affiliate = 1;
        }

        return view('pages.manageUser.edit', $data, ['edit' => $edit, 'users' => $users, 'affiliate' => $affiliate]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $id = $request->id;

        $validator = Validator::make($request->all(), [
            'name' => 'required|min:3',
            // 'catalog' => 'required',
            'repassword' => 'same:password',
            'phone' => 'required|min:10',
            'photos' => 'mimes:jpg,png,jpeg',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->with('failed', $validator->messages()->first());
        }

        $data = $request->all();

        if($request->password){
            $data['password'] = Hash::make($data['password']);
        }
        else{
            unset($data['password']);
            unset($data['repassword']);
        }

        // $photo = $request->file('photos');
        // if ($photo) {
        //     $file = $photo->store('photo', 'public');
        //     $data['photo'] = $file;
        // }

        if($request->hasFile('photos')){
            $subpath = 'users/'.Auth::user()->id.'/photos';
            $file = $request->file('photos');
            $filename=Str::slug(trim($request->name),"_").'_'.$id;
            $extension = $file->extension();
            $filenametostore = $filename.'.'.$extension;
            Storage::disk('s3')->putFileAs($subpath, $file, $filenametostore);
            $url = Storage::disk('s3')->url($subpath.'/'.$filenametostore);
            $data['photo'] = $url;
        }

        unset($data['id']);
        unset($data['_token']);
        unset($data['_method']);

        $update = User::findOrFail($id);
        $update->update($data);

        // return redirect()->route('manage-user.index')->with('success', 'edit data success!');
        if($update){
            $status='success';
            $message='Your request was successful.';
        }else{
            $status='error';
            $message='Oh snap! something went wrong.';
        }

        $notif=['status'=>$status,'message'=>$message];
        return response()->json($notif);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $delete = User::findOrFail($id);
        $delete->delete();

        return redirect()->route('manage-user.index')->with('success', 'delete data success!');
    }
}
