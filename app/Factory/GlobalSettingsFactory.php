<?php

namespace App\Factory;

use App\Models\SystemSettings;

/**
 * Class GlobalSettingsFactory
 * @package App\Factory
 */
class GlobalSettingsFactory
{

    /**
     * @param array|null $data
     *
     * @return SystemSettings
     */
    public function create(?array $data = null): SystemSettings
    {
        if (is_null($data)) {
            return new SystemSettings();
        }
        return new SystemSettings($data);
    }
}