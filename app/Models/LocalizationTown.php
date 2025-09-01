<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LocalizationTown extends Model
{
    use HasFactory;

    protected $table = 'localization_towns';

    protected $fillable = [
        'city_id',
        'town_name',
        'latitude',
        'longitude',
        'status',
    ];

    // Her ilçe bir şehre ait olabilir
    public function city()
    {
        return $this->belongsTo(LocalizationCity::class);
    }
}
