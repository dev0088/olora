<?php

namespace App\Http\Resources;

use DanTheCoder\SaaSCore\Account\Resources\User as UserResource;
use Illuminate\Http\Resources\Json\JsonResource;

class Utm extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id'                => $this->id,
            'utm_name'          => $this->utm_name,
            'utm_description'   => $this->utm_description,
            'utm_type'          => $this->utm_type,
            'created_at'        => $this->created_at,
            'user_name'         => auth()->user()->name,
            'url_counts'        => $this->url_counts,
            'click_counts'      => $this->click_counts,
            'disabled'          => $this->disabled
        ];
    }
}
