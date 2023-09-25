<?php
declare(strict_types=1);

namespace App\Transformers\UserReflection;

use App\Models\UserReflection;
use App\Transformers\AbstractTransformer;
use Exception;

class UserReflectionTransformer extends AbstractTransformer implements UserReflectionTransformerInterface
{
    /**
     * @param UserReflection $userReflection
     *
     * @return array
     * @throws Exception
     */
    public function transform(UserReflection $userReflection): array
    {
        return [
            'id' => $userReflection->id,
            'user_id' => $userReflection->user_id,
            'content_item_id' => $userReflection->content_item_id,
            'input' => $userReflection->input,
            'skipped' => $userReflection->skipped,
            'created_at' => $this->date($userReflection->created_at),
        ];
    }
}
