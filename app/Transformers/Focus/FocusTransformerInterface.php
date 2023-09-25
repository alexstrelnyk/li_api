<?php
declare(strict_types=1);

namespace App\Transformers\Focus;

use App\Models\Focus;
use App\Transformers\TransformerInterface;

interface FocusTransformerInterface extends TransformerInterface
{
    public function transform(Focus $focus): array;
}
