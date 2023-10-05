<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Http\Resources\Support\JsonStandardResource;
use App\Models\Task;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;

/**
 * @mixin Task
 */
class TaskResource extends JsonStandardResource
{
    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     */
    public function toArray(Request $request): array|Arrayable|\JsonSerializable
    {
        return parent::toArray($request);
        /*return [
            "id" => $this->__get('id'),
            "summary" => $this->__get('summary'),
            "status" => $this->__get('status') ? 'performed' : 'open',
            "performed_at" => $this->__get('performed_at'),
            'created_at' => $this->whenNotNull($this->__get('created_at')->format('c')),
            'updated_at' => $this->whenNotNull($this->__get('updated_at')?->format('c')),
            'user' => new UserResource($this->whenLoaded('user')),
            //'user' => (UserResource::make($this->whenLoaded('user'))),
        ];*/
    }
}
