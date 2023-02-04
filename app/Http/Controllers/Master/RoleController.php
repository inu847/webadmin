<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\Menu;
use App\Models\MenuCategory;
use App\Models\MenuRoles;
use App\Models\Role;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Auth;

class RoleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data['titlepage']='Role';
        $data['maintitle']='List Role';
        $roles = Role::where('owner_id',Auth::user()->id)->orderBy('id', 'desc')->get();

        return view('pages.role.index', $data, ['roles' => $roles]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $data['titlepage']='Create Role';
        $data['maintitle']='Create Role';
        $users = User::whereNotNull('owner')->get();

        return view('pages.role.create', $data, ['users' => $users]);
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
            'name' => 'required',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->with('failed', $validator->messages()->first());
        }

        $data = $request->all();
        $data['owner_id'] = Auth::user()->id;
        unset($data['_token']);
        $insert = Role::create($data);

        return redirect()->route('role.index')->with('success', 'input data success!');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $data['titlepage']='Detail Role';
        $data['maintitle']='Detail Role';
        $detail = Role::findOrFail($id);
        // $menus = MenuRoles::where('role_id', $id)->get();
        $menus = MenuCategory::where('active', 1)->get();
        // $menus = Menu::where('active', 1)->get();

        return view('pages.role.detail', $data, ['detail' => $detail, 'menus' => $menus]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $data['titlepage']='Edit Role';
        $data['maintitle']='Edit Role';
        $edit = Role::findOrFail($id);
        $users = User::whereNotNull('owner')->get();

        return view('pages.role.edit', $data, ['edit' => $edit, 'users' => $users]);
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
        $update = Role::findOrFail($id);
        $data['owner_id'] = Auth::user()->id;
        $update->update($data);

        return redirect()->route('role.index')->with('success', 'edit data success!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $delete = Role::findOrFail($id);
        $delete->delete();

        return redirect()->route('role.index')->with('success', 'delete data success!');
    }

    public function saveMenuRole(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'role_id' => 'required',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->with('failed', $validator->messages()->first());
        }

        MenuRoles::where('role_id', $request->role_id)->delete();

        $insert = [];
        foreach ($request->menu_category_id as $key => $value) {
            $insert[] = [
                'user_id' => Auth::user()->id,
                'menu_category_id' => $value,
                'role_id' => $request->role_id
            ];
        }

        if($insert){
            MenuRoles::insert($insert);
        }

        $insert = [];
        foreach ($request->menu as $key => $value) {
            $insert[] = [
                'user_id' => Auth::user()->id,
                'menu_id' => $value,
                'role_id' => $request->role_id
            ];
        }

        if($insert){
            MenuRoles::insert($insert);
        }
        
        return redirect()->route('role.index')->with('success', 'input data success!');
    }




    public function createMenuRole()
    {
        $data['titlepage']='Create Role Menu';
        $data['maintitle']='Create Role Menu';
        $users = User::get();
        $menus = Role::get();

        return view('pages.role.createRole', $data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function storeMenuRole(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->with('failed', $validator->messages()->first());
        }

        $data = $request->all();
        unset($data['_token']);
        $insert = Role::create($data);

        return redirect()->route('role.index')->with('success', 'input data success!');
    }
}
