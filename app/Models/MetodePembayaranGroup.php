<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MetodePembayaranGroup extends Model
{
    // protected $fillable = ["name",
    //                         "minimal_bisa_cicilan",
    //                         "maksimal_bisa_cicilan",
    //                         "image",
    //                         "keterangan",
    //                         "active",
    //                         "urutan"];

    /**
     * The attributes that are protected from mass assignable, others are mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id'];

    public function metodePembayaran()
    {
        return $this->hasMany(MetodePembayaran::class);
    }

    public function metode()
    {
        return $this->hasMany(MetodePembayaran::class);
    }
}
