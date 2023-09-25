<?php
declare(strict_types=1);

namespace App\Transformers\TopicArea;

use App\Models\FocusAreaTopic;
use App\Transformers\AbstractTransformer;
use App\Transformers\Topic\TopicTransformerInterface;

class TopicAreaTransformer extends AbstractTransformer implements TopicAreaTransformerInterface
{
    /**
     * @var TopicTransformerInterface
     */
    private $topicTransformer;

    /**
     * TopicAreaTransformer constructor.
     *
     * @param TopicTransformerInterface $topicTransformer
     */
    public function __construct(TopicTransformerInterface $topicTransformer)
    {
        $this->topicTransformer = $topicTransformer;
    }

    /**
     * @param FocusAreaTopic $topicArea
     *
     * @return array
     */
    public function transform(FocusAreaTopic $topicArea): array
    {
        return [
            'id' => $topicArea->id,
            'focus_area_id' => $topicArea->focus_area_id,
            'topic_id' => $topicArea->topic_id,
            'topic' => fractal($topicArea->topic, $this->topicTransformer)->toArray()['data']
        ];
    }
}
