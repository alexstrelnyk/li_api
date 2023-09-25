<?php
declare(strict_types=1);

namespace App\Manager;

use App\Factory\TopicAreaFactory;
use App\Models\ContentItem;
use App\Models\FocusArea;
use App\Models\FocusAreaTopic;
use App\Models\Topic;
use Exception;

class TopicAreaManager
{
    /**
     * @var TopicAreaFactory
     */
    private $topicAreaFactory;

    /**
     * @var ContentItemManager
     */
    private $contentItemManager;

    /**
     * TopicAreaManager constructor.
     *
     * @param TopicAreaFactory $topicAreaFactory
     * @param ContentItemManager $contentItemManager
     */
    public function __construct(TopicAreaFactory $topicAreaFactory, ContentItemManager $contentItemManager)
    {
        $this->topicAreaFactory = $topicAreaFactory;
        $this->contentItemManager = $contentItemManager;
    }

    /**
     * @param FocusArea $focusArea
     * @param Topic $topic
     *
     * @return FocusAreaTopic
     */
    public function create(FocusArea $focusArea, Topic $topic): FocusAreaTopic
    {
        $topicArea = $this->topicAreaFactory->create($focusArea, $topic);
        $topicArea->save();

        return $topicArea;
    }

    /**
     * @param FocusAreaTopic $focusAreaTopic
     *
     * @return bool
     * @throws Exception
     */
    public function delete(FocusAreaTopic $focusAreaTopic): bool
    {
        return $focusAreaTopic->delete() ?? true;
    }

    /**
     * @param FocusAreaTopic $focusAreaTopic
     * @param ContentItem $contentItem
     */
    public function attachContentItem(FocusAreaTopic $focusAreaTopic, ContentItem $contentItem): void
    {
        $focusAreaTopic->contentItems()->attach($contentItem->id);
        $this->contentItemManager->setIsPractice($contentItem, false);
    }

    /**
     * @param FocusAreaTopic $focusAreaTopic
     * @param ContentItem $contentItem
     */
    public function detachContentItem(FocusAreaTopic $focusAreaTopic, ContentItem $contentItem): void
    {
        $focusAreaTopic->contentItems()->detach($contentItem->id);
        $this->contentItemManager->setIsPractice($contentItem, null);
    }
}
