<?php

declare(strict_types=1);

namespace App\Domain\Repository;

use App\Domain\Entity\FailedAttempt;

interface FailedAttemptRepository
{
    public function save(FailedAttempt $failedAttempt): void;
}
