<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

use App\User;
use Illuminate\Support\Str;
use Auth;
use Session;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
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
    public function index()
    {
        $email = Auth::user()->email; 
        $id = Auth::id();
        $data['titlepage']='Profile';
		$data['maintitle']='My Profile';
		$data['id']=$id;
        $data['biodata'] = Auth::user();
        // $data['biodata'] = DB::table('users')
        //     ->where('id', '=', $id, 'OR', 'email', '=', $email)->first();
        return view('profile', $data);
    }

    public function update(Request $request, $id) {
        if ($request->repassword) {
            DB::table('users')
                ->where('id', '=', $id)
                ->update(
                [
                    'password' => \Hash::make($request->password),
                ]
            );
        } else {
            $insert = [
                    'password_edit' => $request->password_edit,
                    'password_edit_timeout' => $request->password_edit_timeout,
                    'name' => $request->name,
                    'phone' => $request->phone,
                    'address' => $request->address
            ];

            if($request->hasFile('photos')){
                // $file = $photo->store('images/users/'.$id, 'public');
                // $insert['photo'] = $file;
                $subpath = 'users/'.Auth::user()->id.'/photos';
                $file = $request->file('photos');
                $filename=Str::slug(trim($request->name),"_").'_'.$id;
                $extension = $file->extension();
                $filenametostore = $filename.'.'.$extension;
                Storage::disk('s3')->putFileAs($subpath, $file, $filenametostore);
                $url = Storage::disk('s3')->url($subpath.'/'.$filenametostore);
                $insert['photo'] = $url;
            }

            DB::table('users')
                ->where('id', '=', $id)
                ->update($insert);
        }
        return redirect('profile');
    }
}
