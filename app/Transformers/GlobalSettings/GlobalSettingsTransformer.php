<?php

namespace App\Transformers\GlobalSettings;

use App\Transformers\AbstractTransformer;
use \App\Models\SystemSettings;

/**
 * Class GlobalSettingsTransformer
 * @package App\Transformers\GlobalSettings
 */
class GlobalSettingsTransformer extends AbstractTransformer implements GlobalSettingsTransformerInterface
{

    /**
     * @param SystemSettings $globalSettings
     *
     * @return array
     */
    public function transform(SystemSettings $globalSettings): array
    {
        $globalSettings = SystemSettings::query()->get()->toArray();

        $settings = [];
        foreach ($globalSettings as $globalSetting) {
            $settings[$globalSetting['key']] = $globalSetting['value'];
        }

        return $settings;
    }
}