<?php
declare(strict_types=1);

namespace App\Transformers\JournalActivity;

use App\Models\UserReflection;
use App\Transformers\AbstractTransformer;

class JournalActivityTransformer extends AbstractTransformer implements JournalActivityTransformerInterface
{
    /**
     * @param UserReflection $userReflection
     * @return array
     */
    public function transform(UserReflection $userReflection): array
    {
        return [
            'id' => (int) $userReflection->id,
            'accent_color' => $userReflection->id && $userReflection->contentItem ? $userReflection->contentItem->focus->color : '#1ba0aa',
            'title' => $userReflection->id && $userReflection->contentItem ? $userReflection->contentItem->title : 'Your first Coaching Session',
            'description' => $userReflection->input,
            'date' => $this->date($userReflection->created_at)
        ];
    }
}
