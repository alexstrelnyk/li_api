<?php
declare(strict_types=1);

namespace App\Manager;

use App\Models\ContentItem;
use App\Models\User;

class TipManager
{
    /**
     * @param ContentItem $contentItem
     * @param User $user
     */
    public function setViewed(ContentItem $contentItem, User $user): void
    {
        $user->viewedTips()->attach($contentItem);
    }
}
