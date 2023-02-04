<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use App\Models\MenuRoles;
use App\Models\Role;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Auth;

class MenuRoleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data['titlepage']='Menu Permession';
        $data['maintitle']='List Menu Permession';
        $menus = MenuRoles::where('user_id', Auth::user()->id)->orderBy('id', 'desc')->paginate(10);
        // dd($menus);
        return view('pages.menuRole.index', $data, ['menus' => $menus]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $data['titlepage']='Create Menu Permession';
        $data['maintitle']='Create Menu Permession';
        $users = User::get();
        $menus = Menu::get();
        $roles = Role::where('owner_id', Auth::user()->id)->get();

        return view('pages.menuRole.create', $data, [
                                                    'users' => $users,
                                                    'menus' => $menus,
                                                    'roles' => $roles]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $data = $request->all();
        $data['user_id'] = Auth::user()->id;
        unset($data['_token']);
        $insert = MenuRoles::create($data);

        return redirect()->route('menu-roles.index')->with('success', 'input data success!');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $data['titlepage']='Detail Menu Permession';
        $data['maintitle']='Detail Menu Permession';
        $detail = MenuRoles::findOrFail($id);

        return view('pages.menuRole.detail', $data, ['detail' => $detail]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $data['titlepage']='Edit Menu Permession';
        $data['maintitle']='Edit Menu Permession';
        $edit = MenuRoles::findOrFail($id);
        $users = User::get();
        $menus = Menu::get();
        $roles = Role::where('owner_id', Auth::user()->id)->get();

        return view('pages.menuRole.edit', $data, ['edit' => $edit, 
                                                'users' => $users, 
                                                'menus' => $menus, 
                                                'roles' => $roles]);
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
        $data = $request->all();
        $data['user_id'] = Auth::user()->id;
        $update = MenuRoles::findOrFail($id);
        $update->update($data);

        return redirect()->route('menu-roles.index')->with('success', 'edit data success!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $delete = MenuRoles::findOrFail($id);
        if($delete->delete()){
            $status='success';
            $message='Your request was successful.';
        }else{
            $status='error';
            $message='Oh snap! something went wrong.';
        }
        $notif=['status'=>$status,'message'=>$message];
        return response()->json($notif);

        // return redirect()->route('menu-roles.index')->with('success', 'delete data success!');
    }
}
