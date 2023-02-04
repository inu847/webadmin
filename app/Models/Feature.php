<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Http\Requests;

use App\Helper\myFunction;

use Input;
use File;
use Auth;

class Feature extends Model
{
    protected $table = 'feature';
}
