<?php

namespace Database\Seeders;

use App\Models\QueueItem;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Webhook;
use App\UseCases\AddWebhookToQueueUseCase;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{

    public function __construct(
        private readonly AddWebhookToQueueUseCase $useCase
    ) {
    }

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $data = [
            ['URL' => 'https://webhook-test.info1100.workers.dev/success1', 'ORDER ID' => 1, 'NAME' => 'Olimpia Krasteva', 'EVENT' => 'Spooky Summit'],
            ['URL' => 'https://webhook-test.info1100.workers.dev/fail1', 'ORDER ID' => 2, 'NAME' => 'Kumaran Powell', 'EVENT' => 'Serene Sands'],
            ['URL' => 'https://webhook-test.info1100.workers.dev/success2', 'ORDER ID' => 3, 'NAME' => 'Viraja Qurbonova', 'EVENT' => 'Glow Gallery'],
            ['URL' => 'https://webhook-test.info1100.workers.dev/success1', 'ORDER ID' => 4, 'NAME' => 'Kwesi Martinek', 'EVENT' => 'Spooky Summit'],
            ['URL' => 'https://webhook-test.info1100.workers.dev/fail1', 'ORDER ID' => 5, 'NAME' => 'Suada Katz', 'EVENT' => 'Serene Sands'],
            ['URL' => 'https://webhook-test.info1100.workers.dev/retry1', 'ORDER ID' => 6, 'NAME' => 'Neha Lebeau', 'EVENT' => 'Fall Foliage Farm'],
            ['URL' => 'https://webhook-test.info1100.workers.dev/success2', 'ORDER ID' => 7, 'NAME' => 'Ammiel Neri', 'EVENT' => 'Glow Gallery'],
            ['URL' => 'https://webhook-test.info1100.workers.dev/fail2', 'ORDER ID' => 8, 'NAME' => 'Cecilija Poindexter', 'EVENT' => 'Prism Pavilion'],
            ['URL' => 'https://webhook-test.info1100.workers.dev/retry1', 'ORDER ID' => 9, 'NAME' => 'Arcadia Reynell', 'EVENT' => 'Fall Foliage Farm'],
            ['URL' => 'https://webhook-test.info1100.workers.dev/success1', 'ORDER ID' => 10, 'NAME' => 'Zoriana Donovan', 'EVENT' => 'Spooky Summit'],
            ['URL' => 'https://webhook-test.info1100.workers.dev/fail1', 'ORDER ID' => 11, 'NAME' => 'Lorens Starek', 'EVENT' => 'Serene Sands'],
            ['URL' => 'https://webhook-test.info1100.workers.dev/fail1', 'ORDER ID' => 12, 'NAME' => 'Lada Dalgaard', 'EVENT' => 'Serene Sands'],
            ['URL' => 'https://webhook-test.info1100.workers.dev/fail1', 'ORDER ID' => 13, 'NAME' => 'Hari Pavia', 'EVENT' => 'Serene Sands'],
            ['URL' => 'https://webhook-test.info1100.workers.dev/fail1', 'ORDER ID' => 14, 'NAME' => 'Min-Jun Vasilyev', 'EVENT' => 'Serene Sands'],
            ['URL' => 'https://webhook-test.info1100.workers.dev/fail1', 'ORDER ID' => 15, 'NAME' => 'Alphonzo Avellino', 'EVENT' => 'Serene Sands'],
        ];

        foreach ($data as $item) {
            $this->useCase->execute(
                url: $item['URL'],
                orderId: $item['ORDER ID'],
                name: $item['NAME'],
                event: $item['EVENT']
            );
        }
    }
}
