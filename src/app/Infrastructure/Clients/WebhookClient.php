<?php

declare(strict_types=1);

namespace App\Infrastructure\Clients;

use App\Domain\Client\WebhookClient as Client;
use App\Domain\Entity\Webhook;
use App\Exceptions\Clients\RequestBodyEncodeException;
use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\RequestOptions;

readonly class WebhookClient implements Client
{
    public function __construct(
        private GuzzleClient $httpClient
    ) {
    }

    /**
     * @throws RequestBodyEncodeException
     */
    public function send(Webhook $webhook): bool
    {
        $body = json_encode($this->mapWebhookToRequestData($webhook));

        if ($body === false) {
            throw new RequestBodyEncodeException();
        }

        $headers = [
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
        ];

        $options = [
            RequestOptions::HTTP_ERRORS => true,
            RequestOptions::HEADERS => $headers,
            RequestOptions::BODY => $body,
        ];

        try {
            $response = $this->httpClient->post($webhook->getUrl(), $options);
        } catch (GuzzleException $e) {
            // Log the error
            return false;
        }

        if ($response->getStatusCode() !== 200) {
            return false;
        }

        return true;
    }

    private function mapWebhookToRequestData(Webhook $webhook): array
    {
        return [
            'name' => $webhook->getName(),
            'order_id' => $webhook->getOrderId(),
            'event' => $webhook->getEvent(),
        ];
    }
}
