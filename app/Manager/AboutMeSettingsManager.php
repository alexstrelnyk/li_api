<?php
declare(strict_types=1);

namespace App\Manager;

use App\Models\AboutMeSettings;
use App\Models\User;

class AboutMeSettingsManager
{
    /**
     * @param User $user
     * @param array|null $data
     * @return AboutMeSettings
     */
    public function getByUser(User $user, ?array $data = AboutMeSettings::ABOUT_ME_SETTINGS_DEFAULTS): AboutMeSettings
    {
        return $user->aboutMeSettings()->firstOrCreate(['user_id' => $user->id], $data);
    }

    /**
     * @param User $user
     * @param array $data
     * @return AboutMeSettings
     */
    public function update(User $user, array $data): AboutMeSettings
    {
        if (isset($data['tip_time'])) {
            unset($data['tip_time']);
        }

        /** @var AboutMeSettings $aboutMeSettings */
        $aboutMeSettings = $user->aboutMeSettings()->updateOrCreate(['user_id' => $user->id], $data);

        return $aboutMeSettings;
    }

    /**
     * @param User $user
     * @return AboutMeSettings
     */
    public function toggleShareActivity(User $user): AboutMeSettings
    {
        /** @var AboutMeSettings $aboutMeSettings */
        $aboutMeSettings = $user->aboutMeSettings;
        $aboutMeSettings->share_activity = !$aboutMeSettings->share_activity;
        $aboutMeSettings->update();

        return $aboutMeSettings;
    }
}
