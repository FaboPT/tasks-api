<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Http\Resources\Support\JsonStandardResource;
use App\Models\User;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;

/**
 * @mixin User
 */
class UserResource extends JsonStandardResource
{
    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     */
    public function toArray(Request $request): array|\JsonSerializable|Arrayable
    {

        return [
            'name' => $this->__get('name'),
            'email' => $this->__get('email'),
            'roles' => RoleResource::collection($this->whenLoaded('roles')),
            'created_at' => $this->whenNotNull($this->__get('created_at')->format('c')),
            'updated_at' => $this->whenNotNull($this->__get('updated_at')->format('c')),
        ];
    }
}
