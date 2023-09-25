<?php
declare(strict_types=1);

namespace App\Factory;

use App\Models\ContentItem;
use App\Models\Focus;
use App\Models\Topic;

class TopicFactory
{
    /**
     * @param Focus $focus
     *
     * @return Topic
     */
    public function create(Focus $focus): Topic
    {
        return new Topic(['focus_id' => $focus->id]);
    }

    /**
     * @param Focus $focus
     * @param ContentItem $contentItem
     *
     * @return Topic
     */
    public function createPracticeBased(Focus $focus, ContentItem $contentItem): Topic
    {
        $topic = new Topic();

        $topic->focus()->associate($focus);
        $topic->contentItem()->associate($contentItem);

        return $topic;
    }
}
