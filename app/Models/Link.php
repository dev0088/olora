<?php

namespace App\Models;

use Carbon\Carbon;
use App\Models\Utm;
use Illuminate\Database\Eloquent\Model;

class Link extends Model
{

    // Guarded attributes
    protected $guarded = [];


    // Casting
    protected $casts = [
        'iframe_blocked'    => 'boolean',
        'disabled'          => 'boolean',
    ];


    // Get user relationship
    public function user()
    {
        return $this->belongsTo( User::class );
    }


    // Get campaign relationship
    public function campaign()
    {
   		return $this->belongsTo( Campaign::class );
    }

    // Get campaign relationship
    public function domain()
    {
        return $this->belongsTo( Domain::class );
    }


    // Pixels pivot relationship
    public function pixels()
    {
        return $this->belongsToMany( Pixel::class, 'link_pixel', 'link_id', 'pixel_id');
    }


    // Custom scripts pivot relationship
    public function scripts()
    {
        return $this->belongsToMany( CustomScript::class, 'link_custom_script', 'link_id', 'custom_script_id');
    }


    // Call to action pivot relationship
    public function cta()
    {
        return $this->belongsToMany( CallToAction::class, 'link_call_to_action', 'link_id', 'call_to_action_id');
    }


    // Scope a query to only include data for the user
    public function scopeWhereUser($query) {
        return $query->where( 'user_id', auth()->user()->id );
    }


    // Scope a query to only return active links
    public function scopeActive($query) {
        return $query->whereNull('disabled');
    }


    // Format created at date
    public function getCreatedAtAttribute($value)
    {
       return Carbon::parse($value)->timezone( auth()->user()->timezone )->format( config('settings.date_format') );
    }

    public function handleUtmAnalysis($old_values) {
        $utm_params = ['utm_campaign_id', 'utm_source_id', 'utm_medium_id', 'utm_content_id', 'utm_term_id'];

        foreach ($this->getChanges() as $key => $new_value) {
            foreach ($utm_params as $attribute) {
                if ($key == $attribute) {
                    $old_value = $old_values[$attribute];
                    $new_value = $this->$attribute;

                    if ($old_value !== null)
                        Utm::find($old_value)->handleUrlCounts(false);

                    if ($new_value !== null)
                        Utm::find($new_value)->handleUrlCounts(true);
                }
            }
        }
    }

    public function handleUtmClickAnalysis() {
        $utm_params = array('utm_campaign_id', 'utm_medium_id', 'utm_source_id', 'utm_content_id', 'utm_term_id');

        foreach ($this->attributes as $key => $attribute) {

            foreach ($utm_params as $utm_index => $utm_attribute) {
                if ($key === $utm_attribute && $attribute !== null) {
                    Utm::find($attribute)->handleClickCounts();
                }
            }

        }
    }

}
