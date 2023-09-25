<?php

namespace App\Manager;


use App\Factory\TopicAreaFactory;
use App\Models\ContentItem;
use App\Models\FocusArea;
use App\Models\FocusAreaTopic;
use App\Models\Topic;

class FocusAreaTopicManager
{

    /**
     * @var TopicAreaFactory
     */
    private $focusAreaTopicFactory;


    /**
     * FocusAreaTopicManager constructor.
     *
     * @param TopicAreaFactory $focusAreaTopicFactory
     */
    public function __construct(TopicAreaFactory $focusAreaTopicFactory)
    {
        $this->focusAreaTopicFactory = $focusAreaTopicFactory;
    }

    /**
     * @param FocusArea $focusArea
     * @param Topic $topic
     * @param array $data
     * @return FocusAreaTopic
     */
    public function create(FocusArea $focusArea, Topic $topic, array $data): FocusAreaTopic
    {
        $focusAreaTopic = $this->focusAreaTopicFactory->create();

        $focusAreaTopic->focusArea()->associate($focusArea);
        $focusAreaTopic->topic()->associate($topic);

        $focusAreaTopic->status = $data['status'];

        $focusAreaTopic->save();

        return $focusAreaTopic;
    }

    /**
     * @param FocusAreaTopic $focusAreaTopic
     * @param ContentItem $contentItem
     */
    public function addContentItem(FocusAreaTopic $focusAreaTopic, ContentItem $contentItem)
    {
        $focusAreaTopic->contentItems()->attach($contentItem->id);
    }

    /**
     * @param FocusAreaTopic $focusAreaTopic
     * @param ContentItem $contentItem
     */
    public function removeContentItem(FocusAreaTopic $focusAreaTopic, ContentItem $contentItem)
    {
        $focusAreaTopic->contentItems()->detach($contentItem->id);
    }
}