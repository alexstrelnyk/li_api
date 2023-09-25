<?php
declare(strict_types=1);

namespace App\Transformers\TopicOfUser;

use App\Models\FocusAreaTopic;
use App\Models\Topic;
use App\Models\User;
use App\Transformers\ContentItem\ContentItemTransformerInterface;
use App\Transformers\ScheduleTopic\ScheduleTopicTransformerInterface;
use App\Transformers\Topic\TopicTransformer;
use Carbon\Carbon;
use Exception;
use Illuminate\Database\Eloquent\Builder;

class TopicOfUserTransformer extends TopicTransformer
{
    /**
     * @var User
     */
    protected $user;

    /**
     * @var ScheduleTopicTransformerInterface
     */
    private $scheduleTopicTransformer;

    /**
     * TopicOfUserTransformer constructor.
     *
     * @param ScheduleTopicTransformerInterface $scheduleTopicTransformer
     * @param ContentItemTransformerInterface $contentItemTransformer
     * @param User $user
     */
    public function __construct(ScheduleTopicTransformerInterface $scheduleTopicTransformer, ContentItemTransformerInterface $contentItemTransformer, User $user)
    {
        $this->user = $user;
        parent::__construct($contentItemTransformer);
        $this->scheduleTopicTransformer = $scheduleTopicTransformer;
    }

    /**
     * @param Topic $topic
     *
     * @return array
     * @throws Exception
     */
    public function transform(Topic $topic): array
    {
        $scheduleTopic = $topic->scheduleTopics()
            ->ofUser($this->user)
            ->whereDate('occurs_at', '>=', Carbon::now())
            ->orderBy('created_at', 'desc')
            ->first();

        $progress = null;

        $client = $this->user->client;
        $program = $client->activeProgram;

        $topicArea = FocusAreaTopic::where('topic_id', $topic->id)->whereHas('focusArea', static function (Builder $builder) use ($program) {
            $builder->where('program_id', $program->id);
        })->first();

        if ($topicArea instanceof FocusAreaTopic) {
            $progress['completed'] = $topicArea->contentItems()->completed($this->user)->count();
            $progress['total'] = $topicArea->contentItems()->count();
        }

        return array_merge(parent::transform($topic), [
            'schedule_topic' => $scheduleTopic ? fractal($scheduleTopic, $this->scheduleTopicTransformer)->toArray()['data'] : null,
            'progress' => $progress
        ]);
    }
}
