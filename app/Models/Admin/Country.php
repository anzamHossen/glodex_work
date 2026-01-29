<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Country extends Model
{
    use  SoftDeletes;
    protected $guarded = [];
    public $timestamps = true;

    public function countryContinent()
    {
        return $this->belongsTo(CountryContinent::class, 'continent_id');
    }

    public function universities()
    {
        return $this->hasMany(University::class, 'country_id');
    }

    public function courses()
    {
        return $this->hasMany(Course::class, 'country_id');
    }
}
