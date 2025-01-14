<?php

declare(strict_types=1);

namespace App\Infrastructure\Db\Mappers;

use App\Domain\Entity\Webhook;
use App\Models\Webhook as WebhookModel;

class WebhookMapper
{
    public function toEntity(WebhookModel $webhook): Webhook
    {
        return new Webhook(
            id: $webhook->id,
            url: $webhook->url,
            name: $webhook->name,
            event: $webhook->event,
            orderId: $webhook->order_id,
        );
    }

    public function toModel(Webhook $webhook): WebhookModel
    {
        $model = new WebhookModel([
            'id' => $webhook->getId(),
            'url' => $webhook->getUrl(),
            'name' => $webhook->getName(),
            'event' => $webhook->getEvent(),
            'order_id' => $webhook->getOrderId(),
        ]);

        $model->exists = $webhook->getId() !== null;

        return $model;
    }
}
