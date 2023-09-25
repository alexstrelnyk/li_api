<?php
declare(strict_types=1);

namespace App\Transformers;

use App\Models\ContentItemUserProgress;

class ContentItemProgressTransformer extends AbstractTransformer
{
    /**
     * A Fractal transformer.
     *
     * @param ContentItemUserProgress $contentItemUserProgress
     *
     * @return array
     */
    public function transform(ContentItemUserProgress $contentItemUserProgress): array
    {
        $completed = $contentItemUserProgress->primer
            + $contentItemUserProgress->content
            + ($contentItemUserProgress->contentItem->has_reflection ? $contentItemUserProgress->reflection : 0);

        return [
            'completed' => $completed,
            'total' => $contentItemUserProgress->contentItem->has_reflection ? 3 : 2
        ];
    }
}
