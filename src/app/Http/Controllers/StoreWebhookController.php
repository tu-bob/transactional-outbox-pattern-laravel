<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\UseCases\AddWebhookToQueueUseCase;
use Illuminate\Http\JsonResponse;

class StoreWebhookController extends Controller
{
    public function __construct(
        private readonly AddWebhookToQueueUseCase $useCase
    ) {
    }

    public function __invoke(): JsonResponse
    {
        $data = request()->validate([
            'url' => 'required|url',
            'order_id' => 'required|integer',
            'name' => 'required|string',
            'event' => 'required|string',
        ]);

        $this->useCase->execute(
            $data['url'],
            $data['order_id'],
            $data['name'],
            $data['event']
        );

        return response()->json(['message' => 'Webhook added to queue']);
    }
}
