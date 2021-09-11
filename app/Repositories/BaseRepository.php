<?php

namespace App\Repositories;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

abstract class BaseRepository
{
    protected object $obj;

    protected function __construct(object $obj)
    {
        $this->obj = $obj;
    }

    abstract public function store(array $attributes): Model;

    abstract public function update(int $id, array $attributes): bool;

    abstract public function destroy(int $id): bool;

    abstract protected function all(): Collection;
}
