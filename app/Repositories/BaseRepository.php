<?php

declare(strict_types=1);

namespace App\Repositories;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;

abstract class BaseRepository
{
    abstract public function store(array $attributes): Model;

    abstract public function update(int $id, array $attributes): Model|bool;

    abstract public function destroy(int $id): bool;

    abstract protected function all(): LengthAwarePaginator;
}
