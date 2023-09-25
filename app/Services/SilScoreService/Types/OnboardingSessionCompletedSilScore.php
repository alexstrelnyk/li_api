<?php
declare(strict_types=1);

namespace App\Services\SilScoreService\Types;

class OnboardingSessionCompletedSilScore extends AbstractSilScore
{
    public const TYPE = 'onboarding-completed';
    public const POINTS = 2;

    /**
     * @var int
     */
    protected $points = 2;
}
