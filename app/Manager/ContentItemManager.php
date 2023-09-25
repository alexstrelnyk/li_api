<?php
declare(strict_types=1);

namespace App\Manager;

use App\Exceptions\ActivityNotFoundException;
use App\Factory\ContentItemFactory;
use App\Models\ContentItem;
use App\Models\ContentItemUserProgress;
use App\Models\Focus;
use App\Models\Program;
use App\Models\Topic;
use App\Models\User;
use App\Models\UserFeedback;
use App\Services\Activity\ActivityService;
use App\Services\Activity\Types\WatchedActivityInterface;
use Exception;
use LogicException;

class ContentItemManager
{
    public const PRIMER_VIEWED_TYPE = 'Primer';
    public const CONTENT_VIEWED_TYPE = 'Content';
    public const REFLECTION_VIEWED_TYPE = 'Reflection';

    /**
     * @var ContentItemUserProgressManager
     */
    private $contentItemUserProgressManager;

    /**
     * @var ContentItemFactory
     */
    private $contentItemFactory;

    /**
     * @var UserFeedbackManager
     */
    private $userFeedbackManager;

    /**
     * @var TopicManager
     */
    private $topicManager;

    /**
     * @var ActivityService
     */
    private $activityService;

    /**
     * ContentItemManager constructor.
     *
     * @param ContentItemUserProgressManager $contentItemUserProgressManager
     * @param UserFeedbackManager $userFeedbackManager
     * @param ContentItemFactory $contentItemFactory
     * @param TopicManager $topicManager
     * @param ActivityService $activityService
     */
    public function __construct(
        ContentItemUserProgressManager $contentItemUserProgressManager,
        UserFeedbackManager $userFeedbackManager,
        ContentItemFactory $contentItemFactory,
        TopicManager $topicManager,
        ActivityService $activityService
    ) {
        $this->contentItemUserProgressManager = $contentItemUserProgressManager;
        $this->contentItemFactory = $contentItemFactory;
        $this->userFeedbackManager = $userFeedbackManager;
        $this->topicManager = $topicManager;
        $this->activityService = $activityService;
    }

    /**
     * @param Focus $focus
     * @param array $data
     *
     * @return ContentItem
     */
    public function create(Focus $focus, array $data): ContentItem
    {
        $contentItem = $this->contentItemFactory->create($focus);
        $contentItem->fill($data);

        $contentItem->save();

        return $contentItem;
    }

    /**
     * @param User $user
     * @param ContentItem $contentItem
     *
     * @param Program|null $program
     *
     * @return ContentItemUserProgress
     */
    private function getContentItemUserProgress(User $user, ContentItem $contentItem, ?Program $program = null): ContentItemUserProgress
    {
        $client = $user->client;
        $contentItemUserProgress = ContentItemUserProgress::ofUserAndItemAndProgram($user, $contentItem, $program ?? $client->activeProgram)->first();

        if (!$contentItemUserProgress instanceof ContentItemUserProgress) {
            $contentItemUserProgress = $this->contentItemUserProgressManager->create($contentItem, $user, $program ?? $client->activeProgram);
        }

        return $contentItemUserProgress;
    }

    /**
     * @param ContentItem $contentItem
     * @param User $user
     *
     * @return ContentItemUserProgress
     * @throws ActivityNotFoundException
     */
    public function start(ContentItem $contentItem, User $user): ContentItemUserProgress
    {
        $topic = $contentItem->getTopic();

        if (!$topic instanceof Topic) {
            throw new LogicException('Content Item has no related Topic');
        }

        $this->topicManager->startTopic($topic, $user);

        $userProgress = $this->getContentItemUserProgress($user, $contentItem);
        $this->contentItemUserProgressManager->setViewed($userProgress, ContentItemUserProgressManager::PRIMER_VIEWED_TYPE);

        return $userProgress;
    }

    /**
     * @param ContentItem $contentItem
     * @param User $user
     * @param string $type
     */
    public function setViewed(ContentItem $contentItem, User $user, string $type): void
    {
        $progress = $this->getContentItemUserProgress($user, $contentItem);
        $this->contentItemUserProgressManager->setViewed($progress, $type);
    }

    /**
     * @return array
     */
    public static function getAvailableViewedTypes(): array
    {
        return [self::PRIMER_VIEWED_TYPE, self::CONTENT_VIEWED_TYPE, self::REFLECTION_VIEWED_TYPE];
    }

    /**
     * @param ContentItem $contentItem
     * @param User $user
     *
     * @throws Exception
     */
    public function resetProgress(ContentItem $contentItem, User $user): void
    {
        $contentItemUserProgress = $this->getContentItemUserProgress($user, $contentItem);
        $this->contentItemUserProgressManager->delete($contentItemUserProgress);
    }

    /**
     * @param User $user
     *
     * @param ContentItem $contentItem
     *
     * @throws Exception
     */
    public function complete(User $user, ContentItem $contentItem): void
    {
        $contentItemUserProgress = $this->getContentItemUserProgress($user, $contentItem);

        if ($contentItemUserProgress instanceof ContentItemUserProgress) {
            $this->contentItemUserProgressManager->complete($contentItemUserProgress);

            if ($contentItem->topic instanceof Topic) {
                $this->topicManager->removeEvent($contentItem->topic, $user);
            }
        } else {
            throw new LogicException('Content item was never started');
        }
    }

    /**
     * @param ContentItem $contentItem
     * @return bool
     * @throws Exception
     */
    public function delete(ContentItem $contentItem): bool
    {
        return $contentItem->delete() ?? true;
    }

    /**
     * @param ContentItem $contentItem
     * @param array $data
     * @return ContentItem
     */
    public function update(ContentItem $contentItem, array $data): ContentItem
    {
        $focus = Focus::findOrFail($data['focus_id']);

        $contentItem->focus()->associate($focus);
        $contentItem->fill($data);

        $contentItem->update();

        return $contentItem;
    }

    /**
     * @param ContentItem $contentItem
     *
     * @return UserFeedback
     */
    public function likeContentItem(ContentItem $contentItem): UserFeedback
    {
        return $this->userFeedbackManager->createFeedback($contentItem, true);
    }

    /**
     * @param ContentItem $contentItem
     *
     * @param string $text
     *
     * @return UserFeedback
     */
    public function dislikeContentItem(ContentItem $contentItem, string $text): UserFeedback
    {
        return $this->userFeedbackManager->createFeedback($contentItem, false, $text);
    }

    /**
     * @param ContentItem $contentItem
     * @param User $user
     *
     * @throws ActivityNotFoundException
     */
    public function watched(ContentItem $contentItem, User $user): void
    {
        $progress = $this->getContentItemUserProgress($user, $contentItem);
        $this->contentItemUserProgressManager->setViewed($progress, ContentItemUserProgressManager::CONTENT_VIEWED_TYPE);

        $this->activityService->createActivity(WatchedActivityInterface::TYPE, $user, [['class' => ContentItem::class, 'class_id' => $contentItem->id]]);
    }

    /**
     * @param ContentItem $contentItem
     * @param bool $value
     */
    public function setIsPractice(ContentItem $contentItem, ?bool $value = null): void
    {
        $contentItem->is_practice = $value;
        $contentItem->save();
    }
}
