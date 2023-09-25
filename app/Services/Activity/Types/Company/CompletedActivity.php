<?php

namespace App\Services\Activity\Types\Company;

use App\Services\Activity\Types\AbstractActivity;
use App\Services\Activity\Types\CompletedActivityInterface;

class CompletedActivity extends AbstractActivity implements CompletedActivityInterface, CompanyActivityInterface
{

    /**
     * @return string
     */
    public function getText(): string
    {
        return $this->getUser()->name . ' completed onboarding and has unlocked all campaigns';
    }

}