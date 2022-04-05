<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ProfileResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        // return parent::toArray($request);
        return [
            'id' => $this->id,
            'user' => $this->user->name,
            'photo' => asset('uploads/' . $this->photo),
            'phone' => $this->phone,
            'description' => $this->description,
            'gender' => $this->gender,
            'created_at' => (string) $this->created_at,
        ];
    }
}
