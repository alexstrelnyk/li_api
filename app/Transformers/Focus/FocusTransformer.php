<?php
declare(strict_types=1);

namespace App\Transformers\Focus;

use App\Models\Focus;
use App\Transformers\AbstractTransformer;
use Exception;

class FocusTransformer extends AbstractTransformer implements FocusTransformerInterface
{
    /**
     * @param Focus $focus
     *
     * @return array
     * @throws Exception
     */
    public function transform(Focus $focus): array
    {
        return [
            'id' => $focus->id,
            'title' => $focus->title,
            'slug' => $focus->slug,
            'program_id' => $focus->program_id,
            'accent_color' => $focus->color,
            'video_overview' => $focus->video_overview,
            'status' => $focus->status,
            'image_url' => $focus->image_url,
            'created_at' => $this->date($focus->created_at),
            'updated_at' => $this->date($focus->updated_at),
            'created_by' => $focus->created_by,
            'count_sessions' => $focus->contentItems()->count(),
            'topic_count' => $focus->topics()->count()
        ];
    }
}
