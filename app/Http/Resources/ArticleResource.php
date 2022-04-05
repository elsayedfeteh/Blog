<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ArticleResource extends JsonResource
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
            'image_path' => asset('uploads/' . $this->image_path),
            'title' => $this->title,
            'summary' => $this->summary,
            'body' => $this->body,
            'user' => $this->user->name,
            'status' => $this->status,
            'comments' => $this->comments,
            'total_views' => $this->total_views,
            'created_at' => (string) $this->created_at,
        ];
    }
}
