<?php

namespace App\Manager;

use App\Factory\GlobalSettingsFactory;
use App\Models\SystemSettings;

/**
 * Class GlobalSettingsManager
 * @package App\Manager
 */
class GlobalSettingsManager
{

    /**
     * @var GlobalSettingsFactory
     */
    private $globalSettingsFactory;

    /**
     * GlobalSettingsManager constructor.
     * @param GlobalSettingsFactory $globalSettingsFactory
     */
    public function __construct(GlobalSettingsFactory $globalSettingsFactory)
    {
        $this->globalSettingsFactory = $globalSettingsFactory;
    }

    /**
     * @return SystemSettings
     */
    public function getGlobalSettings(): SystemSettings
    {
        foreach (SystemSettings::DEFAULTS as $field => $fieldValue) {
            $fieldRecord = SystemSettings::query()->where('key', $field)->first();

            if (empty($fieldRecord)) {
                $fieldRecord = $this->globalSettingsFactory->create();
                $fieldRecord->key = $field;
                $fieldRecord->value = $fieldValue;

                $fieldRecord->save();
            }
        }

        return SystemSettings::query()->first();
    }

    /**
     * @param array $data
     *
     * @return SystemSettings
     */
    public function updateGlobalSettings(array $data): SystemSettings
    {
        foreach ($data as $field => $fieldValue) {
            $fieldRecord = SystemSettings::query()->where('key', $field)->first();

            if (!empty($fieldRecord)) {
                $fieldRecord->value = $fieldValue;

                $fieldRecord->update();
            } else {
                $fieldRecord = $this->globalSettingsFactory->create();
                $fieldRecord->key = $field;
                $fieldRecord->value = $fieldValue;

                $fieldRecord->save();
            }
        }

        return SystemSettings::query()->first();
    }

}