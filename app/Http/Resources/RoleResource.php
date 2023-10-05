<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Http\Resources\Support\JsonStandardResource;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;

/**
 * @mixin Role
 */
class RoleResource extends JsonStandardResource
{
    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     */
    public function toArray(Request $request): array|\JsonSerializable|Arrayable
    {
        return [
            'name' => $this->name,

        ];
    }
}
