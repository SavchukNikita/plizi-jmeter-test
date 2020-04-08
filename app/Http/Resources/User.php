<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class User extends JsonResource
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
            'id' => $this->id,
            'email' => $this->email,
            'isOnline' => $this->isOnline(),
            'profile' => new Profile($this->profile)
        ];
    }

    public function isOnline() : bool
    {
        $period = config('user_activity_margin');
        return $this->last_activity_dt > strtotime("-$period minutes");
    }
}


