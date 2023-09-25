<?php
declare(strict_types=1);

namespace App\Transformers\Topic;

use App\Models\Topic;
use App\Transformers\AbstractTransformer;
use App\Transformers\ContentItem\ContentItemTransformerInterface;
use Exception;

class TopicTransformer extends AbstractTransformer implements TopicTransformerInterface
{
    /**
     * @var ContentItemTransformerInterface
     */
    private $contentItemTransformer;

    /**
     * TopicTransformer constructor.
     *
     * @param ContentItemTransformerInterface $contentItemTransformer
     */
    public function __construct(ContentItemTransformerInterface $contentItemTransformer)
    {
        $this->contentItemTransformer = $contentItemTransformer;
    }

    /**
     * @param Topic $topic
     *
     * @return array
     * @throws Exception
     */
    public function transform(Topic $topic): array
    {
        return [
            'id' => $topic->id,
            'title' => $topic->title,
            'slug' => $topic->slug,
            'focus_id' => $topic->focus_id,
            'introduction' => $topic->introduction,
            'has_practice' => (bool) $topic->has_practice,
            'content_item' => $topic->has_practice && $topic->contentItem ? fractal($topic->contentItem, $this->contentItemTransformer)->toArray()['data'] : null,
            'calendar_prompt_text' => $topic->calendar_prompt_text,
            'status' => $topic->status,
            'created_by' => $topic->created_by,
            'created_at' => $this->date($topic->created_at),
            'updated_at' => $this->date($topic->updated_at),
        ];
    }
}
