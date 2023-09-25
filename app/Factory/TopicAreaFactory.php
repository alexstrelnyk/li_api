<?php
declare(strict_types=1);

namespace App\Factory;

use App\Models\FocusArea;
use App\Models\FocusAreaTopic;
use App\Models\Topic;

class TopicAreaFactory
{
    /**
     * @param FocusArea $focusArea
     * @param Topic $topic
     *
     * @return FocusAreaTopic
     */
    public function create(FocusArea $focusArea, Topic $topic): FocusAreaTopic
    {
        $topicArea = new FocusAreaTopic();
        $topicArea->status = 3;
        $topicArea->focusArea()->associate($focusArea);
        $topicArea->topic()->associate($topic);

        return $topicArea;
    }
}
