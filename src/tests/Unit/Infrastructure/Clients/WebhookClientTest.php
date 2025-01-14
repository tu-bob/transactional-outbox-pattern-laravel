<?php

declare(strict_types=1);

namespace Tests\Unit\Infrastructure\Clients;

use App\Domain\Entity\Webhook;
use App\Infrastructure\Clients\WebhookClient;
use App\Infrastructure\Db\Mappers\WebhookMapper;
use App\Models\Webhook as WebhookModel;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\RequestOptions;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Tests\TestCase;

class WebhookClientTest extends TestCase
{
    private readonly WebhookClient $client;

    private readonly Client $httpClient;

    public function setUp(): void
    {
        parent::setUp();

        $this->httpClient = $this->createMock(Client::class);
        $this->client = new WebhookClient($this->httpClient);
    }

    public function testItReturnsTrueWhenRequestIsSent()
    {
        $webhook = $this->makeWebhook();

        $expectedOptions = [
            RequestOptions::HTTP_ERRORS => true,
            RequestOptions::HEADERS => [
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
            ],
            RequestOptions::BODY => json_encode([
                'name' => $webhook->getName(),
                'order_id' => $webhook->getOrderId(),
                'event' => $webhook->getEvent(),
            ]),
        ];

        $this->httpClient->expects($this->once())
            ->method('post')
            ->with($webhook->getUrl(), $expectedOptions)
            ->willReturn($this->mockSuccessResponse());

        $requestSent = $this->client->send($webhook);

        $this->assertTrue($requestSent);
    }

    public function testItReturnsFalseWhenRequestFails()
    {
        $webhook = $this->makeWebhook();

        $this->httpClient->expects($this->once())
            ->method('post')
            ->willReturnCallback(function () {
                throw new RequestException('Error', $this->createMock(RequestInterface::class));
            });

        $requestSent = $this->client->send($webhook);

        $this->assertFalse($requestSent);
    }


    private function makeWebhook(): Webhook
    {
        $webhook = WebhookModel::factory()->make();
        return new WebhookMapper()->toEntity($webhook);
    }

    private function mockSuccessResponse(): ResponseInterface
    {
        $successResponse = $this->createMock(ResponseInterface::class);
        $successResponse->method('getStatusCode')->willReturn(200);
        return $successResponse;
    }
}
