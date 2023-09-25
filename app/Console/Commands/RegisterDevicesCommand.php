<?php

namespace App\Console\Commands;

use App\Manager\DeviceManager;
use App\Models\Device;
use Exception;
use Illuminate\Console\Command;

class RegisterDevicesCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'debug:register-devices';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Register all devices in notification hub';

    /**
     * @var DeviceManager
     */
    private $deviceManager;

    /**
     * Create a new command instance.
     *
     * @param DeviceManager $deviceManager
     */
    public function __construct(DeviceManager $deviceManager)
    {
        parent::__construct();
        $this->deviceManager = $deviceManager;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $devices = Device::all();

        foreach ($devices as $device) {
            /** @var Device $device */
            try {
                $this->deviceManager->registerDevice($device);
                $this->output->writeln('Device '.$device->id.' was been sent to register');
            } catch (Exception $exception) {
                $this->output->writeln('Device '.$device->id.' ('.$device->device_token.') could not be registered');
            }
        }
    }
}
