<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PengeluaranDetail extends Model
{
    protected $table = 'pengeluaran_detail';
    protected $guarded = ['id'];

    public function pengeluaran()
    {
        return $this->belongsTo(Pengeluaran::class, 'pengeluaran_id');
    }

}
