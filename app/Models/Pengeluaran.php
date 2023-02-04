<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pengeluaran extends Model
{
    protected $table = 'pengeluaran';
    protected $guarded = ['id'];

    public function detail()
    {
        return $this->hasMany(PengeluaranDetail::class, 'pengeluaran_id')->orderBy('nama');
    }

    public function catalog()
    {
        return $this->belongsTo(Catalog::class, 'catalog_id', 'id');
    }

}
