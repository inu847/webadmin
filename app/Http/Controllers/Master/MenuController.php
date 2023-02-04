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

class MenuController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data['titlepage']='Menu';
        $data['maintitle']='List Menu Category';
        // $menus = Menu::orderBy('id', 'desc')->get();
        $menus = MenuCategory::orderBy('name', 'asc')->get();
        
        return view('pages.menu.index', $data, ['menus' => $menus]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $data['titlepage']='Create Menu';
        $data['maintitle']='Create Category Menu';

        return view('pages.menu.create', $data);
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
        unset($data['_token']);
        $insert = MenuCategory::create($data);

        return redirect()->route('menu.index')->with('success', 'input data success!');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $data['titlepage']='Detail Menu';
        $data['maintitle']='Detail Menu';
        $detail = MenuCategory::findOrFail($id);
        $menus = Menu::where('menu_category_id', $id)->orderBy('order_view', 'asc')->orderBy('name', 'asc')->get();

        return view('pages.menu.detail', $data, ['detail' => $detail, 'menus' => $menus]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $data['titlepage']='Edit Menu';
        $data['maintitle']='Edit Menu';
        $edit = MenuCategory::findOrFail($id);

        return view('pages.menu.edit', $data, ['edit' => $edit]);
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
        $update = MenuCategory::findOrFail($id);
        $update->update($data);

        return redirect()->route('menu.index')->with('success', 'edit data success!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $delete = MenuCategory::findOrFail($id);
        if($delete->delete()){
            $status='success';
            $message='Your request was successful.';
        }else{
            $status='error';
            $message='Oh snap! something went wrong.';
        }
        $notif=['status'=>$status,'message'=>$message];
        return response()->json($notif);

        // return redirect()->route('menu.index')->with('success', 'delete data success!');
    }

    public function createMenu($id)
    {
        $data['titlepage']='Menu';
        $data['maintitle']='List Menu';
        $data['id'] = $id;

        return view('pages.menu.createMp', $data);
    }

    public function storeMenu(Request $request, $id)
    {
        $data = $request->all();
        $data['menu_category_id'] = $id;
        $create = Menu::create($data);
        return redirect('menu/'.$id)->with('success', 'input data success!');
    }

    public function editMenu($id)
    {
        $edit = Menu::findOrFail($id);
        
        return view('pages.menu.editMp', ['edit' => $edit]);
    }

    public function updateMenu(Request $request, $id)
    {
        try {
            $update_data = Menu::findOrFail($id);
            $parent = $update_data->menu_category_id;
            $update_data->update($request->all());
            return redirect('menu/'.$parent)->with('success', 'edit data success!');
        } catch (\Throwable $th) {
            $update_data = Menu::findOrFail($id)->update($request->all());
            return redirect()->back()->with('danger', 'failed edit data!');
        }
    }

    public function destroyMenu($id)
    {
        $delete = Menu::findOrFail($id);

        if($delete->delete()){
            $status='success';
            $message='Your request was successful.';
        }else{
            $status='error';
            $message='Oh snap! something went wrong.';
        }
        $notif=['status'=>$status,'message'=>$message];
        return response()->json($notif);
    }
}
