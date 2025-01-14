<?php

declare(strict_types=1);

namespace App\Infrastructure\Db\Repositories;

use App\Domain\Entity\FailedAttempt;
use App\Domain\Repository\FailedAttemptRepository as Repository;
use App\Models\FailedAttempt as FailedAttemptModel;

class FailedAttemptRepository implements Repository
{
    public function save(FailedAttempt $failedAttempt): void
    {
        FailedAttemptModel::query()->updateOrCreate(
            ['url' => $failedAttempt->getUrl()],
            ['attempts' => $failedAttempt->getAttempts()]
        );
    }
}
