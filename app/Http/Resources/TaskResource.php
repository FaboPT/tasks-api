<?php

namespace App\Http\Resources;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use JsonSerializable;

class TaskResource extends JsonResource
{

    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     * @return array|Arrayable|JsonSerializable
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
                    'name' => $this->user->roles->implode("name", ' '),
                ],
            ]
        ];
    }
}
