<?php
declare(strict_types=1);

namespace App\Services\SilScoreService\Types;

use App\Models\UserReflection;
use App\Services\SilScoreService\Types\Interfaces\ReflectionSilScore;

class ReflectionCompletedSilScore extends AbstractSilScore implements ReflectionSilScore
{
    public const TYPE = 'reflection-completed';
    public const POINTS = 1;

    /**
     * @var UserReflection
     */
    private $reflection;

    /**
     * ReflectionCompletedSilScore constructor.
     *
     * @param UserReflection $reflection
     */
    public function __construct(UserReflection $reflection)
    {

        $this->reflection = $reflection;
    }

    /**
     * @return UserReflection
     */
    public function getReflection(): UserReflection
    {
        return $this->reflection;
    }
}
