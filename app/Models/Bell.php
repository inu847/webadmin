<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Http\Requests;

use Input;
use Auth;
use Session;
use getData;
use myFunction;

class Bell extends Model
{
    protected $table = 'bell';

    public static function call_cashier($request){
        try {
            DB::transaction(function () use ($request) {
                $data=$request->all();
                $var=new Bell;
                $var->id=myFunction::id('bell','id');
                $var->catalog_id=trim($data['catalog']);
                $var->table_position=trim($data['position']);
                $var->message=trim($data['message']);
                $var->save();
            });
         }
        catch(\Exception $e) {
            return false;
        }
        return true;
    }
}
