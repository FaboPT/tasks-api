<?php

namespace App\Http\Resources\Support;

use Illuminate\Http\Resources\Json\JsonResource;

class JsonStandardResource extends JsonResource
{
    /**
     * JsonStandardResource constructor.
     */
    public function __construct(mixed $resource, string $message = null, string $wrap = 'data')
    {
        parent::__construct($resource);

        $this->additional([
            'message' => __($message),
            'success' => true,
        ]);

        JsonResource::$wrap = $wrap;
    }


}
