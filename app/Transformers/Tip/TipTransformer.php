<?php
declare(strict_types=1);

namespace App\Transformers\Tip;

use App\Models\ContentItem;
use App\Models\Topic;
use App\Transformers\AbstractTransformer;

class TipTransformer extends AbstractTransformer
{
    /**
     * A Fractal transformer.
     *
     * @param ContentItem $contentItem
     *
     * @return array
     */
    public function transform(ContentItem $contentItem): array
    {
        return [
            'focus_id' => $contentItem->focus_id,
            'topic_id' => $contentItem->tipTopic->id,
            'image' => $contentItem->focus->image_url,
            'name' => $contentItem->focus->title,
            'title' => $contentItem->tipTopic instanceof Topic ? $contentItem->tipTopic->title : null,
            'tip' => $contentItem->info_quick_tip,
            'accent_color' => $contentItem->focus->color,
        ];
    }
}
