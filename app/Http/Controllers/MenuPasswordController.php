<?php

namespace App\Http\Controllers;

use App\Models\MenuPassword;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\MenuCategory;
use App\Models\Role;
use Illuminate\Support\Facades\Auth;

class MenuPasswordController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data['titlepage']='Menu With Password';
        $data['maintitle']='Menu With Password';
        $menus = MenuCategory::where('active', 1)->get();

        $password_menus = MenuPassword::join('menus', 'menus.id', '=', 'menu_passwords.menu_id')
            ->select('menu_passwords.*', 'url')
            ->whereNotNull('url')
            ->where('using_password', 1)
            ->where('menus.active', 1)
            ->where('user_id', (Auth::user()->parent) ? Auth::user()->parent->id : Auth::id())
            ->pluck('url')->toArray();

        return view('pages.menu.password', $data, ['menus' => $menus, 'password_menus' => $password_menus]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        MenuPassword::where('user_id', (Auth::user()->parent) ? Auth::user()->parent->id : Auth::id())->delete();

        $insert = [];
        if($request->menu){
            foreach ($request->menu as $key => $value) {
                $insert[] = [
                    'user_id' => (Auth::user()->parent) ? Auth::user()->parent->id : Auth::id(),
                    'using_password' => 1,
                    'menu_id' =>$value
                ];
            }
        }

        if($insert){
            $saved = MenuPassword::insert($insert);
        }

        return redirect()->route('menu_password.index')->with('success', 'update data success!');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\MenuPassword  $menuPassword
     * @return \Illuminate\Http\Response
     */
    public function show(MenuPassword $menuPassword)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\MenuPassword  $menuPassword
     * @return \Illuminate\Http\Response
     */
    public function edit(MenuPassword $menuPassword)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\MenuPassword  $menuPassword
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, MenuPassword $menuPassword)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\MenuPassword  $menuPassword
     * @return \Illuminate\Http\Response
     */
    public function destroy(MenuPassword $menuPassword)
    {
        //
    }
}
