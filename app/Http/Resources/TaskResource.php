<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class TaskResource extends JsonResource
{
    public static $wrap = 'tasks';
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            "id" => $this->id,
            "summary" => $this->summary,
            "status" => $this->status,
            "performed_at" => $this->performed_at,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            "deleted_at" => $this->deleted_at,
            "user" => [
                "name" => $this->user->name,
                "email" => $this->user->email,
                "roles" => [
                    'name'=>$this->user->roles->implode("name",' '),
                ],
            ]
        ];
    }
}
