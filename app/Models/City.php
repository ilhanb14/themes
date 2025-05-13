<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class City extends Model
{
    protected $fillable = [
        'name',
        'country',
        'continent',
        'population',
        'latitude',
        'longitude',
        'known_for',
        'founded_year',
        'is_capital',
        'annual_tourists'
    ];
}
