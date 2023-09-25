<?php
declare(strict_types=1);

namespace App\Transformers\ContentItem;

use App\Models\ContentItem;
use App\Models\Topic;
use App\Models\User;
use App\Transformers\AbstractTransformer;

class ContentItemTransformer extends AbstractTransformer implements ContentItemTransformerInterface
{
    /**
     * @param ContentItem $contentItem
     *
     * @return array
     */
    public function transform(ContentItem $contentItem): array
    {
        $fields = [
            'id',
            'focus_id',
            'title',
            'primer_title',
            'primer_content',
            'reading_time',
            'info_quick_tip',
            'info_content_image',
            'info_full_content',
            'info_video_uri',
            'info_audio_uri',
            'info_source_title',
            'info_source_link',
            'has_reflection',
            'reflection_help_text',
            'status',
            'created_by'
        ];

        $topic = $contentItem->getTopic();

        return array_merge($this->getBodyFields($contentItem, $fields), [
            'author' => $contentItem->creator instanceof User ? $contentItem->creator->author_name : null,
            'is_practice' => $contentItem->is_practice === null ? null : (bool) $contentItem->is_practice,
            'focus_name' => $contentItem->focus ? $contentItem->focus->title : null,
            'topic_id' =>  $topic instanceof Topic ? $topic->id : null,
            'content_type' => $contentItem->content_type,
            'created_at' => $this->date($contentItem->created_at),
            'updated_at' => $this->date($contentItem->updated_at),
        ]);
    }
}
