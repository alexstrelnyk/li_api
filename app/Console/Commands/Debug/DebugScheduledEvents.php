<?php

namespace App\Console\Commands\Debug;

use App\Models\ScheduleTopic;
use Illuminate\Console\Command;

class DebugScheduledEvents extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'debug:scheduled-events';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Show Scheduled events';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        ScheduleTopic::all()->each(function (ScheduleTopic $scheduleTopic) {
            $this->output->writeln(sprintf(
                'Event id=%s email=%s topic=%s practice=%s:%s occurs_at=%s <br>',
                $scheduleTopic->id,
                $scheduleTopic->user->email,
                $scheduleTopic->topic->id,
                $scheduleTopic->topic->has_practice ? '1' : '0',
                $scheduleTopic->topic->contentItem ? ($scheduleTopic->topic->contentItem->is_practice ? '1' : '0') : 'No object',
                $scheduleTopic->occurs_at
            ));
        });
    }
}
