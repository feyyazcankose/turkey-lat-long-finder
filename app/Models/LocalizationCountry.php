<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LocalizationCountry extends Model
{
    use HasFactory;

    protected $table = 'localization_countries';

    protected $fillable = [
        'binary_code',
        'triple_code',
        'country_name',
        'phone_code',
        'status',
    ];

    // Her ülkenin birden fazla şehri olabilir
    public function cities()
    {
        return $this->hasMany(LocalizationCity::class);
    }
}
