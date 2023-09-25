<?php
declare(strict_types=1);

namespace App\Transformers;

use App\Models\ContentItemUserProgress;

class ContentItemProgressMetaTransformer extends AbstractTransformer
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
        return [
            'primer_viewed' => (bool) $contentItemUserProgress->primer,
            'info_viewed' => (bool) $contentItemUserProgress->content,
            'reflection_viewed' => (bool) $contentItemUserProgress->reflection,
            'completed_at' => $contentItemUserProgress->completed_at ? $this->date($contentItemUserProgress->completed_at) : null
        ];
    }
}
