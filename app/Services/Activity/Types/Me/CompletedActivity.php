<?php

namespace App\Services\Activity\Types\Me;

use App\Services\Activity\Types\AbstractActivity;
use App\Services\Activity\Types\CompletedActivityInterface;

class CompletedActivity extends AbstractActivity implements CompletedActivityInterface, MeActivityInterface
{

    /**
     * @return string
     */
    public function getText(): string
    {
        return 'You completed onboarding and have unlocked all campaigns';
    }

}