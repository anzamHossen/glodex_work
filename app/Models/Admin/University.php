<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class University extends Model
{
    use  SoftDeletes;
    protected $guarded = [];
    public $timestamps = true;

    public function country()
    {
        return $this->belongsTo(Country::class, 'country_id');
    }

    public function courses()
    {
        return $this->hasMany(Course::class, 'university_id');
    }

}
