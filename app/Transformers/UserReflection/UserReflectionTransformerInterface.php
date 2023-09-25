<?php
declare(strict_types=1);

namespace App\Transformers\UserReflection;

use App\Models\UserReflection;
use App\Transformers\TransformerInterface;

interface UserReflectionTransformerInterface extends TransformerInterface
{
    public function transform(UserReflection $reflection): array;
}
