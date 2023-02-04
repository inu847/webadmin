<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CatalogMetodePembayaran extends Model
{
    protected $table = 'catalog_metode_pembayaran';
    /**
     * The attributes that are protected from mass assignable, others are mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id'];
}
