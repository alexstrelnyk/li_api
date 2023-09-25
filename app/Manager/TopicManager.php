<?php
declare(strict_types=1);

namespace App\Manager;

use App\Exceptions\ActivityNotFoundException;
use App\Factory\TopicFactory;
use App\Models\Client;
use App\Models\ContentItem;
use App\Models\Focus;
use App\Models\ScheduleTopic;
use App\Models\Topic;
use App\Models\User;
use App\Services\Activity\ActivityService;
use App\Services\Activity\Types\StartedTopicActivityInterface;
use DB;
use Exception;
use Illuminate\Support\Collection;
use LogicException;

class TopicManager implements ManagerInterface
{
    /**
     * @var TopicFactory
     */
    private $topicFactory;

    /**
     * @var ActivityService
     */
    private $activityService;

    /**
     * TopicManager constructor.
     *
     * @param TopicFactory $topicFactory
     * @param ActivityService $activityService
     */
    public function __construct(TopicFactory $topicFactory, ActivityService $activityService)
    {
        $this->topicFactory = $topicFactory;
        $this->activityService = $activityService;
    }

    /**
     * @return Topic
     */
    public static function getModel(): Topic
    {
        return new Topic();
    }

    /**
     * @param Client $client
     *
     * @return Collection
     */
    public function createSorting(Client $client): Collection
    {
        $inserts = [];
        Topic::ofUser($client->user)->get()->each(static function (Topic $topic, $key) use (&$inserts, $client) {
            $inserts[] = ['client_id' => $client->id, 'topic_id' => $topic->id, 'order' => $key];
        });

        DB::table('topic_order')->insert($inserts);

        return $this->getSorting($client);
    }

    /**
     * @param Client $client
     *
     * @return Collection
     */
    public function getSorting(Client $client): Collection
    {
        return DB::table('topic_order')->where('client_id', $client->id)->get();
    }

    /**
     * @param Client $client
     * @param array $orders
     */
    public function setNewOrder(Client $client, array $orders): void
    {
        $topics = Topic::whereIn('id', array_keys($orders))->get();

        foreach ($topics as $topic) {
            $this->setOrder($topic, $client, $orders[$topic->id]);
        }
    }

    /**
     * @param Topic $topic
     * @param Client $client
     * @param int $order
     *
     * @return bool
     */
    public function setOrder(Topic $topic, Client $client, int $order): bool
    {
        DB::table('topic_order')->where('client_id', $client->id)->where('topic_id', $topic->id)->delete();
        return DB::table('topic_order')->insert(['client_id' => $client->id,'topic_id' => $topic->id, 'order' => $order]);
    }

    /**
     * @param Topic $topic
     * @param $data
     */
    public function update(Topic $topic, $data): void
    {
        $topic->fill($data);

        if ($topic->has_practice) {
            $contentItem = ContentItem::findOrFail($data['content_item_id']);
            $topic->contentItem()->associate($contentItem);
        }

        $topic->save();
    }

    /**
     * @param Topic $topic
     *
     * @return bool
     * @throws Exception
     */
    public function delete(Topic $topic): bool
    {
        return $topic->delete() ?? true;
    }

    /**
     * @param Focus $focus
     * @param array $data
     *
     * @return Topic
     */
    public function createLearnBased(Focus $focus, array $data): Topic
    {
        $topic = $this->topicFactory->create($focus);
        $topic->fill($data);
        $topic->save();

        return $topic;
    }

    /**
     * @param Focus $focus
     * @param ContentItem $contentItem
     * @param array $data
     *
     * @return Topic
     */
    public function createPracticeBased(Focus $focus, ContentItem $contentItem, array $data): Topic
    {
        if ($contentItem->is_practice !== true) {
            throw new LogicException('Content Item is not for performance-based topic');
        }

        $topic = $this->topicFactory->createPracticeBased($focus, $contentItem);
        $topic->fill($data);
        $topic->save();

        return $topic;
    }

    /**
     * @param Topic $topic
     * @param User $user
     *
     * @throws ActivityNotFoundException
     */
    public function startTopic(Topic $topic, User $user): void
    {
        $this->activityService->createActivity(
            StartedTopicActivityInterface::TYPE,
            $user,
            [['class' => Topic::class, 'class_id' => $topic->id]]
        );
    }

    /**
     * @param Topic $topic
     * @param User $user
     *
     * @throws Exception
     */
    public function removeEvent(Topic $topic, User $user): void
    {
        if ($topic->has_practice) {
            $scheduledEvent = $topic->scheduleTopics()->where('user_id', $user->id)->first();

            if ($scheduledEvent instanceof ScheduleTopic) {
                $scheduledEvent->delete();
            }
        }
    }
}
