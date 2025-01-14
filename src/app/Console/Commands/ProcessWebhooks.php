<?php

namespace App\Console\Commands;

use App\UseCases\SendWebhooksUseCase;
use Illuminate\Console\Command;

class ProcessWebhooks extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:process-webhooks';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle(SendWebhooksUseCase $useCase): void
    {
        $this->info('Starting to process webhooks');
        while (true) {
            $useCase->execute();
            sleep(1);
            $this->info('Finished processing webhooks');
        }
    }
}
