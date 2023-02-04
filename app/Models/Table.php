<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Table extends Model
{
    use SoftDeletes;
    protected $table = 'tables';
    protected $guard = [];
    protected $fillable = ['code', 'table', 'status', 'catalog_id'];

    public function catalog()
    {
        return $this->belongsTo(Catalog::class, 'catalog_id', 'id');
    }

    public function tableTransaction()
    {
        return $this->hasMany(TableTransaction::class)->orderBy('id', 'desc');
    }
}
