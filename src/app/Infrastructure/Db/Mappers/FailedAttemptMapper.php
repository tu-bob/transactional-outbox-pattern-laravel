<?php

declare(strict_types=1);

namespace App\Infrastructure\Db\Mappers;

use App\Domain\Entity\FailedAttempt;
use App\Models\FailedAttempt as FailedAttemptModel;

class FailedAttemptMapper
{
    public function toEntity(FailedAttemptModel $data): FailedAttempt
    {
        return new FailedAttempt(
            $data->id,
            $data->url,
            $data->attempts
        );
    }

    public function toModel(FailedAttempt $data): FailedAttemptModel
    {
        $model = new FailedAttemptModel();
        $model->id = $data->getId();
        $model->url = $data->getUrl();
        $model->attempts = $data->getAttempts();

        $model->exists = $data->getId() !== null;

        return $model;
    }
}
