<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Utm extends Model
{
    protected $fillable = [
        'utm_name',
        'utm_description',
        'utm_type',
        'user_id',
        'url_counts',
        'click_counts',
        'disabled'
    ];

    // Casting
    protected $casts = [
        'disabled' => 'boolean',
    ];

    // Scope a query to only include data for the user
    public function scopeWhereUser($query) {
        return $query->where( 'user_id', auth()->user()->id );
    }

    // Format created at date
    public function getCreatedAtAttribute($value)
    {
       return Carbon::parse($value)->timezone( auth()->user()->timezone )->format( config('settings.date_format') );
    }

    public function handleUrlCounts($is_plus) {
        $this->update(array('url_counts' => $is_plus ? $this->url_counts + 1 : $this->url_counts - 1));
    }

    public function handleClickCounts() {
        $this->update(array('click_counts' => $this->click_counts + 1));
    }
}
