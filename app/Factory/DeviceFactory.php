<?php
declare(strict_types=1);

namespace App\Factory;

use App\Models\Device;

class DeviceFactory
{
    /**
     * @param string $deviceId
     * @param string $type
     * @param string $deviceToken
     *
     * @return Device
     */
    public function create(string $deviceId, string $type, ?string $deviceToken = null): Device
    {
        return new Device(['device_id' => $deviceId, 'device_token' => $deviceToken, 'type' => $type]);
    }
}
