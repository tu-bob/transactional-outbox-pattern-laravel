<?php

declare(strict_types=1);

namespace Tests\Unit\UseCases;

use App\Domain\Client\WebhookClient;
use App\Domain\Entity\Webhook;
use App\Domain\Enum\WebhookStatus;
use App\Domain\Repository\FailedAttemptRepository;
use App\Domain\Repository\QueueItemRepository;
use App\Domain\Repository\Repository;
use App\Infrastructure\Db\Mappers\QueueItemMapper;
use App\Infrastructure\Db\Mappers\WebhookMapper;
use App\Models\QueueItem as QueueItemModel;
use App\Models\Webhook as WebhookModel;
use App\UseCases\SendWebhooksUseCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SendWebhooksUseCaseTest extends TestCase
{
    use RefreshDatabase;

    private readonly SendWebhooksUseCase $useCase;

    private readonly WebhookClient $client;

    public function setUp(): void
    {
        parent::setUp();

        $this->client = $this->createMock(WebhookClient::class);

        $this->useCase = new SendWebhooksUseCase(
            $this->client,
            app(Repository::class),
            app(QueueItemRepository::class),
            app(FailedAttemptRepository::class),
            app('log')
        );
    }

    public function testItMarksWebhookAsSentWhenRequestIsSuccessful() {
        $webhook = $this->createWebhook();

        $this->client->expects($this->once())
            ->method('send')
            ->with($webhook)
            ->willReturn(true);

        $this->useCase->execute();

        $this->assertDatabaseHas('queue_items', [
            'status' => WebhookStatus::SUCCESS,
            'webhook_id' => $webhook->getId(),
        ]);
    }

    public function testItMarksWebhookAsFailedWhenRequestIsNotSuccessful() {
        $webhook = $this->createWebhook();

        $this->client->expects($this->once())
            ->method('send')
            ->with($webhook)
            ->willReturn(false);

        $this->useCase->execute();

        $this->assertDatabaseHas('queue_items', [
            'status' => WebhookStatus::FAILED,
            'webhook_id' => $webhook->getId(),
        ]);
    }

    public function testItSchedulesRetryWhenRequestIsNotSuccessful() {
        $webhook = $this->createWebhook();

        $this->client->expects($this->once())
            ->method('send')
            ->with($webhook)
            ->willReturn(false);

        $this->useCase->execute();

        $actualTriesCount = QueueItemModel::query()
            ->where('webhook_id', $webhook->getId())
            ->count();

        $this->assertEquals(2, $actualTriesCount);
    }

    public function testItDoesNotScheduleRetryWhenMaxRetryDelayReached(): void
    {
        $webhook = $this->createWebhook([
            'attempt' => 6,
        ]);

        $this->client->expects($this->once())
            ->method('send')
            ->with($webhook)
            ->willReturn(false);

        $this->useCase->execute();

        $actualTriesCount = QueueItemModel::query()
            ->where('webhook_id', $webhook->getId())
            ->count();

        $this->assertEquals(1, $actualTriesCount);
    }

    public function testItCreatesFailedAttempt()
    {
        $webhook = $this->createWebhook();

        $this->client->expects($this->once())
            ->method('send')
            ->with($webhook)
            ->willReturn(false);

        $this->useCase->execute();

        $this->assertDatabaseHas('failed_attempts', [
            'url' => $webhook->getUrl(),
            'attempts' => 1,
        ]);
    }

    private function createWebhook(array $queueItemAttributes = []): Webhook
    {
        $webhook = WebhookModel::factory()->create();

        $qAttributes = array_merge([
            'webhook_id' => $webhook->id,
            'status' => WebhookStatus::PENDING,
            'retry_at' => now(),
            'attempt' => 0,
        ], $queueItemAttributes);

        $queueItem = QueueItemModel::query()->create($qAttributes);

        $webhook = new WebhookMapper()->toEntity($webhook);
        $queueItem = new QueueItemMapper()->toEntity($queueItem);
        $queueItem->setWebhook($webhook);

        return $webhook;
    }
}
