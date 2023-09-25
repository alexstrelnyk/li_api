<?php
declare(strict_types=1);

namespace App\Mangers;

use App\Manager\ContentItemManager;
use App\Models\ContentItem;
use App\Models\Focus;
use App\Models\User;

class FocusManager
{
    /**
     * @var ContentItemManager
     */
    private $contentItemManager;

    /**
     * FocusManager constructor.
     *
     * @param ContentItemManager $contentItemManager
     */
    public function __construct(ContentItemManager $contentItemManager)
    {
        $this->contentItemManager = $contentItemManager;
    }

    /**
     * @param Focus $focus
     * @param User $user
     */
    public function resetProgress(Focus $focus, User $user): void
    {
        $focus->contentItems->each(function (ContentItem $contentItem) use ($user) {
            $this->contentItemManager->resetProgress($contentItem, $user);
        });
    }
}
