<?php

namespace App\Transformers\Setting;

use App\Models\AboutMeSettings;
use Carbon\Carbon;
use League\Fractal\TransformerAbstract;

class SettingTransformer extends TransformerAbstract
{
    /**
     * @param AboutMeSettings $aboutMeSettings
     * @return array
     */
    public function transform(AboutMeSettings $aboutMeSettings): array
    {
        return [
            'tip_time' => $aboutMeSettings->user->tip_time,
            'event_practice_email' => $aboutMeSettings->event_practice_email,
            'event_practice_notification' => $aboutMeSettings->event_practice_notification,
            'random_acts_email' => $aboutMeSettings->random_acts_email,
            'random_acts_notification' => $aboutMeSettings->event_practice_notification,
            'login_streak_email' => $aboutMeSettings->login_streak_email,
            'login_streak_notification' => $aboutMeSettings->login_streak_notification,
            'share_activity' => $aboutMeSettings->share_activity
        ];
    }
}
