<?php
declare(strict_types=1);

namespace App\Factory;

use App\Models\ContentItem;
use App\Models\User;
use App\Models\UserFeedback;

class UserFeedbackFactory
{
    /**
     * @param User $user
     * @param ContentItem $contentItem
     * @param int $reaction
     *
     * @return UserFeedback
     */
    public function create(User $user, ContentItem $contentItem, int $reaction): UserFeedback
    {
        return new UserFeedback(['user_id' => $user->id, 'content_item_id' => $contentItem->id, 'reaction' => $reaction]);
    }
}
