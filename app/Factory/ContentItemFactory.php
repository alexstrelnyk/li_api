<?php
declare(strict_types=1);

namespace App\Factory;

use App\Models\ContentItem;
use App\Models\Focus;

class ContentItemFactory
{
    /**
     * @param Focus $focus
     *
     * @return ContentItem
     */
    public function create(Focus $focus): ContentItem
    {
        $contentItem = new ContentItem();
        $contentItem->focus()->associate($focus);

        return $contentItem;
    }
}
