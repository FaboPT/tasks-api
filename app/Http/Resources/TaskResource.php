<?php

namespace App\Http\Resources;

use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Task
 */
class TaskResource extends JsonResource
{

    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            "id" => $this->id,
            "summary" => $this->summary,
            "status" => $this->status,
            "performed_at" => $this->performed_at,
            'created_at' => $this->whenNotNull($this->created_at->format('c')),
            'updated_at' => $this->whenNotNull($this->updated_at?->format('c')),
            'user' => new UserResource($this->whenLoaded('user')),
            //'user' => (UserResource::make($this->whenLoaded('user'))),

        ];
    }
}
