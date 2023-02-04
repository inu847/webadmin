<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MetodePembayaran extends Model
{
    protected $table = 'metode_pembayaran';

    // protected $fillable = ['name',
    //                         'metode_pembayaran_group_id',
    //                         'order_view',
    //                         'logo',
    //                         'code',
    //                         'active'];

    /**
     * The attributes that are protected from mass assignable, others are mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id'];

    public function metodePembayaranGroup()
    {
        return $this->belongsTo(MetodePembayaranGroup::class, 'metode_pembayaran_group_id', 'id');
    }

    public function group()
    {
        return $this->belongsTo(MetodePembayaranGroup::class);
    }
}
