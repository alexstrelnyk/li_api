<?php
declare(strict_types=1);

namespace App\Transformers\JournalActivity;

use App\Models\UserReflection;
use App\Transformers\TransformerInterface;

interface JournalActivityTransformerInterface extends TransformerInterface
{
    public function transform(UserReflection $userReflection): array;
}
