<?php

namespace App\Factory;

use App\Models\ContentItem;
use App\Models\SilScore;
use App\Models\User;
use App\Models\UserReflection;

/**
 * Class SilScoreActivityFactory
 * @package App\Factory
 */
class SilScoreFactory
{

    /**
     * @param User $user
     * @param string $type
     * @param int $points
     * @return SilScore
     */
    public function create(User $user, string $type, int $points): SilScore
    {
        return new SilScore([
            'user_id' => $user->id,
            'type' => $type,
            'points' => $points
        ]);
    }

    /**
     * @param User $user
     * @param ContentItem $contentItem
     * @param string $type
     * @param int $points
     *
     * @return SilScore
     */
    public function createForContentItem(User $user, ContentItem $contentItem, string $type, int $points): SilScore
    {
        $silScore = new SilScore(['points' => $points, 'type' => $type]);
        $silScore->user()->associate($user);
        $silScore->contentItem()->associate($contentItem);

        return $silScore;
    }

    /**
     * @param User $user
     * @param UserReflection $reflection
     * @param string $type
     * @param int $points
     *
     * @return SilScore
     */
    public function createForReflection(User $user, UserReflection $reflection, string $type, int $points): SilScore
    {
        $silScore = new SilScore(['points' => $points, 'type' => $type]);
        $silScore->user()->associate($user);
        $silScore->reflection()->associate($reflection);
        $silScore->contentItem()->associate($reflection->contentItem);

        return $silScore;
    }
}
