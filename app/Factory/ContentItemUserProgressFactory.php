<?php
declare(strict_types=1);

namespace App\Factory;

use App\Models\ContentItem;
use App\Models\ContentItemUserProgress;
use App\Models\Program;
use App\Models\User;

class ContentItemUserProgressFactory
{
    /**
     * @param ContentItem $contentItem
     *
     * @param User $user
     *
     * @param Program|null $program
     *
     * @return ContentItemUserProgress
     */
    public function create(ContentItem $contentItem, User $user, ?Program $program = null): ContentItemUserProgress
    {
        $contentItemUserProgress = new ContentItemUserProgress();
        $contentItemUserProgress->contentItem()->associate($contentItem);
        $contentItemUserProgress->program()->associate($program);
        $contentItemUserProgress->user()->associate($user);

        return $contentItemUserProgress;
    }
}
