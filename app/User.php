<?php

namespace App;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Http\Requests;
use App\Helper\myFunction;
use App\Models\Catalog;
use App\Models\Register;
use App\Models\Role;
use Auth;
use Input;
use File;
use Image;
use Hash;

class User extends Authenticatable
{
    use Notifiable;
    protected $table = 'users';
    protected $guarded = ['id'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    // protected $fillable = [
    //     'name', 'email', 'password',
    //     'username',
    //     'address',
    //     'temp_password',
    //     'photo',
    //     'phone',
    //     'number_catalog',
    //     'parent_id',
    //     'catalog',
    //     'level',
    //     'active',
    //     'user_show',
    //     'legitimate',
    //     'owner',
    // ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public static function save_data($request)
    {
        try {
            DB::transaction(function () use ($request) {
                $data = $request->all();
                $id = myFunction::id('users', 'id');
                $username = explode('@',$data['email'])[0];
                $req = new User();
                $req->id = $id;
                $req->username = $username;
                $req->name = $data['name'];
                $req->email = $data['email'];
                $req->phone = $data['phone'];
                $req->password = Hash::make($data['password']);
                $req->level = 'User';
                $req->active = 'Y';
                $req->parent_id = Auth::user()->id;
                $req->catalog = $data['catalogid'];
                $req->role_id = $data['role_id'];
                $req->api_token = Str::random(40);
                $req->save();
            });
        } catch (\Exception $e) {
            return false;
        }
        return true;
    }
    public static function update_data($request)
    {
        try {
            DB::transaction(function () use ($request) {
                $data = $request->all();

                $user = User::where('id', $data['id'])->first();
                $user->name = $data['name'];
                $user->catalog = $data['catalogid'];
                $user->email = $data['email'];
                $user->phone = $data['phone'];
                $user->role_id = $data['role_id'];

                if(empty($user->api_token)){
                    $user->api_token = Str::random(40);
                }
                
                $user->save();
              
            });
        } catch (\Exception $e) {
            return false;
        }
        return true;
    }
    public static function update_register($request)
    {
        try {
            DB::transaction(function () use ($request) {
              $data = $request->all();
              User::where('id', $data['id'])->update(['number_catalog' => $data['catalog']]);
              //Register::where('user_id', $data['id'])->update(['package_id' => $data['package_id']]);
            });
        } catch (\Exception $e) {
            return false;
        }
        return true;
    }
    public static function delete_data($id)
    {
        try {
            DB::transaction(function () use ($id) {
              User::where('id', $id)->delete();
            });
        } catch (\Exception $e) {
            return false;
        }
        return true;
    }
    public static function block_user($id,$active)
    {
        try {
            DB::transaction(function () use ($id,$active) {
              User::where('id', $id)->update(['active' => $active]);
            });
        } catch (\Exception $e) {
            return false;
        }
        return true;
    }

    public function role()
    {
        return $this->belongsTo(Role::class, 'role_id', 'id');
    }

    public function catalogUser()
    {
        return $this->belongsTo(Catalog::class, 'catalog', 'id');
    }

    public function catalog()
    {
        return $this->hasMany('App\Models\Catalog', 'user_id');
    }

    public function member()
    {
        return $this->hasMany(User::class, 'parent_id', 'id');
    }

    public function affiliate()
    {
        return $this->belongsTo(User::class, 'affiliate_id', 'id');
    }

    public function parent()
    {
        return $this->belongsTo(User::class, 'parent_id', 'id');
    }
}
