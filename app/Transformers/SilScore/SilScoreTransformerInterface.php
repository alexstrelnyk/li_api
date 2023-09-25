<?php
declare(strict_types=1);

namespace App\Transformers\SilScore;

use App\Models\SilScore;
use App\Transformers\TransformerInterface;

interface SilScoreTransformerInterface extends TransformerInterface
{
    public function transform(SilScore $silScore): array;
}
