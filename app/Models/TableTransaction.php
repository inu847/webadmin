<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TableTransaction extends Model
{
    public function table()
    {
        return $this->belongsTo(Table::class, 'table_id', 'id');
    }
}
