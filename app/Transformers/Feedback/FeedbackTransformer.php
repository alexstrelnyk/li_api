<?php
declare(strict_types=1);

namespace App\Transformers\Feedback;

use App\Models\Feedback;
use App\Transformers\AbstractTransformer;
use App\Transformers\TransformerInterface;

class FeedbackTransformer extends AbstractTransformer implements TransformerInterface
{
    /**
     * @param Feedback $feedback
     *
     * @return array
     */
    public function transform(Feedback $feedback): array
    {
        return [
            'id' => $feedback->id,
            'user_id' => $feedback->user_id,
            'text' => $feedback->text,
        ];
    }
}
