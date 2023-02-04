<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Http\Requests;

use App\Helper\myFunction;

use App\Models\Invoice;
use App\Models\InvoiceDetail;
use App\Models\Items;

use Image;
use Input;
use File;
use Auth;
use getData;

class InvoiceRecipe extends Model
{
    protected $table = 'invoicerecipe';
}
