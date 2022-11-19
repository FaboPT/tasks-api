<?php

namespace App\Http\Resources;

use App\Http\Resources\Support\JsonStandardResource;
use App\Models\Task;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use JsonSerializable;

/**
 * @mixin Task
 */
class TaskResource extends JsonStandardResource
{

    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     * @return array|Arrayable|JsonSerializable
     */
    public function toArray($request): array|Arrayable|JsonSerializable
    {
        return parent::toArray($request);
        /*return [
            "id" => $this->id,
            "summary" => $this->summary,
            "status" => $this->status ? 'performed' : 'open',
            "performed_at" => $this->performed_at,
            'created_at' => $this->whenNotNull($this->created_at->format('c')),
            'updated_at' => $this->whenNotNull($this->updated_at?->format('c')),
            'user' => new UserResource($this->whenLoaded('user')),
            //'user' => (UserResource::make($this->whenLoaded('user'))),

        ];*/
    }
}
