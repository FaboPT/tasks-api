<?php

namespace App\Http\Resources;

use App\Http\Resources\Support\JsonStandardResource;
use App\Models\User;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use JsonSerializable;

/**
 * @mixin User
 */
class UserResource extends JsonStandardResource
{
    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     * @return array|Arrayable|JsonSerializable
     */
    public function toArray($request): array|JsonSerializable|Arrayable
    {

        return [
            'name' => $this->name,
            'email' => $this->email,
            'roles' => RoleResource::collection($this->whenLoaded('roles')),
            'created_at' => $this->whenNotNull($this->created_at->format('c')),
            'updated_at' => $this->whenNotNull($this->updated_at->format('c')),
        ];
    }
}