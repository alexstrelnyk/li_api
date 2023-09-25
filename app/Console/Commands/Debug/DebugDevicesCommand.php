<?php
declare(strict_types=1);

namespace App\Console\Commands\Debug;

use App\Models\Device;
use App\Models\User;
use Illuminate\Console\Command;

class DebugDevicesCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'debug:devices {userId?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Get list of devices for user';

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
        $devices = Device::all();
        if ($this->argument('userId')) {
            $user = User::findOrFail($this->argument('userId'));
            $devices = $user->devices;
        }

        foreach ($devices as $device) {
            $this->output->writeln('Device type: '.$device->type.' device ID '.$device->device_id.' token '.$device->device_token);
        }
    }
}
