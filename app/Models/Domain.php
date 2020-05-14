<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Domain extends Model
{
    // Guarded attributes
    protected $guarded = [];


    // Scope a query to only include data for the user
    public function scopeWhereUser($query) {
        return $query->where( 'user_id', auth()->user()->id );
    }


    // Format created at date
    public function getCreatedAtAttribute($value)
    {
       return Carbon::parse($value)->timezone( auth()->user()->timezone )->format( config('settings.date_format') );
    }
}