<?php
declare(strict_types=1);

namespace App\Transformers\FocusArea;

use App\Models\FocusArea;
use App\Transformers\AbstractTransformer;
use App\Transformers\Focus\FocusTransformerInterface;

class FocusAreaTransformer extends AbstractTransformer implements FocusAreaTransformerInterface
{

    /**
     * @var FocusTransformerInterface
     */
    private $focusTransformer;

    /**
     * FocusAreaTransformer constructor.
     * @param FocusTransformerInterface $focusTransformer
     */
    public function __construct(FocusTransformerInterface $focusTransformer)
    {
        $this->focusTransformer = $focusTransformer;
    }

    /**
     * @param FocusArea $focusArea
     * @return array
     */
    public function transform(FocusArea $focusArea): array
    {
        return [
            'id' => $focusArea->id,
            'program_id' => $focusArea->program_id,
            'focus_id' => $focusArea->focus_id,
            'created_at' => $this->date($focusArea->created_at),
            'updated_at' => $this->date($focusArea->updated_at),
            'title' => $focusArea->focus->title,
            'count_topics' => $focusArea->topicAreas()->count(),
            'count_sessions' => 0, // TODO need fix
            'status' => $focusArea->status,
            'focus' => fractal($focusArea->focus, $this->focusTransformer)->toArray()['data']
        ];
    }
}
