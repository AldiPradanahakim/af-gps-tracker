<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\MQTT\MQTTSubscriberService;
use Throwable;

class MQTTSubscribeCommand extends Command
{
    /**
     * Artisan Command.
     */
    protected $signature = 'mqtt:subscribe';

    /**
     * Command Description.
     */
    protected $description = 'Start MQTT Subscriber for GPS Tracker';

    public function __construct(
        protected MQTTSubscriberService $subscriberService
    ) {
        parent::__construct();
    }

    /**
     * Execute Command.
     */
    public function handle(): int
    {
        $this->info('========================================');
        $this->info(' GPS Tracker MQTT Subscriber');
        $this->info('========================================');

        $this->line(
            'Broker : ' .
                config('mqtt.host') .
                ':' .
                config('mqtt.port')
        );

        $this->line(
            'Topic  : ' .
                config('mqtt.topics.gps')
        );

        $this->newLine();

        try {

            $this->subscriberService->subscribe();

            return self::SUCCESS;
        } catch (Throwable $exception) {

            report($exception);

            $this->error($exception->getMessage());

            return self::FAILURE;
        }
    }
}
