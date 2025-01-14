<?php

declare(strict_types=1);

namespace App\Domain\Repository;

interface Repository
{
    public function startTransaction(): void;

    public function commitTransaction(): void;

    public function rollbackTransaction(): void;
}
