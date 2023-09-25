<?php
declare(strict_types=1);

namespace App\Console\Commands\Clear;

use App\Models\ScheduleTopic;
use App\Models\Topic;
use App\Models\User;
use Illuminate\Console\Command;

class ClearScheduledEvents extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'clear:scheduled-events';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete invalid scheduled events';

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
            if (!$scheduleTopic->topic instanceof Topic || !$scheduleTopic->user instanceof User) {
                $id = $scheduleTopic->id;
                $scheduleTopic->delete();
                $this->output->writeln(sprintf('Scheduled events %s was been deleted', $id));
            }
        });
    }
}
