<?php

declare(strict_types=1);

namespace App\Infrastructure\Db\Repositories;

use App\Domain\Repository\Repository as RepositoryInterface;
use Illuminate\Support\Facades\DB;

class Repository implements RepositoryInterface
{
    public function startTransaction(): void
    {
        DB::beginTransaction();
    }

    public function commitTransaction(): void
    {
        DB::commit();
    }

    public function rollbackTransaction(): void
    {
        DB::rollBack();
    }
}
