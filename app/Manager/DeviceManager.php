<?php
declare(strict_types=1);

namespace App\Manager;

use App\Factory\DeviceFactory;
use App\Models\Device;
use App\Models\User;
use App\Services\Azure\NotificationHubService\Exception\DeviceRegistrationException;
use App\Services\Azure\NotificationHubService\NotificationHubService;
use Auth;

class DeviceManager
{
    /**
     * @var DeviceFactory
     */
    private $deviceFactory;

    /**
     * @var NotificationHubService
     */
    private $notificationHubService;

    /**
     * DeviceManager constructor.
     *
     * @param DeviceFactory $deviceFactory
     * @param NotificationHubService $notificationHubService
     */
    public function __construct(DeviceFactory $deviceFactory, NotificationHubService $notificationHubService)
    {
        $this->deviceFactory = $deviceFactory;
        $this->notificationHubService = $notificationHubService;
    }

    /**
     * @param string $deviceId
     * @param string $type
     * @param string|null $deviceToken
     *
     * @throws DeviceRegistrationException
     */
    public function addDevice(string $deviceId, string $type, ?string $deviceToken = null): void
    {
        $device = Device::where('device_id', $deviceId)->first();
        /** @var User $user */
        $user = Auth::user();

        if (!$device instanceof Device) {
            $device = $this->deviceFactory->create($deviceId, $type, $deviceToken);
            $device->save();
        } elseif ($deviceToken !== null) {
            $device->device_token = $deviceToken;
            $device->save();
            $this->registerDevice($device);
        }

        // Detach this device from all users
        $device->users()->detach();

        // Attach this device only to current user
        $user->devices()->attach($device);
    }

    /**
     * @param Device $device
     *
     * @throws DeviceRegistrationException
     */
    public function registerDevice(Device $device): void
    {
        $deviceRegistration = $this->notificationHubService->registerDevice($device->type, $device->device_token);
        $device->registration_id = $deviceRegistration->getRegistrationId();
        $device->save();
    }

    /**
     * @param string $deviceId
     */
    public function removeDevice(string $deviceId): void
    {
        $device = Device::where('device_id', $deviceId)->first();
        /** @var User $user */
        $user = Auth::user();

        if ($device instanceof Device) {
            $user->devices()->detach($device);
        }
    }
}