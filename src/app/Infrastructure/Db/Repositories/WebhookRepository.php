<?php

declare(strict_types=1);

namespace App\Infrastructure\Db\Repositories;

use App\Domain\Entity\Webhook;
use App\Domain\Repository\WebhookRepository as Repository;
use App\Infrastructure\Db\Mappers\WebhookMapper;

readonly class WebhookRepository implements Repository
{
    public function __construct(
        private WebhookMapper $mapper
    ) {
    }

    public function save(Webhook $webhook): void
    {
        $model = $this->mapper->toModel($webhook);
        $model->save();
        $webhook->setId($model->id);
    }
}
