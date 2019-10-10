<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class NameResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        // return in a more JS-friendly format
        // for this task the web-client doesn't need to know the id
        return [
            'firstName' => $this->first_name,
            'lastName' => $this->last_name,
        ];
    }
}
