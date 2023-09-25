<?php

namespace App\Transformers\FocusArea;

use App\Models\FocusArea;
use App\Transformers\TransformerInterface;

/**
 * Interface FocusAreaTransformerInterface
 * @package App\Transformers\FocusArea
 */
interface FocusAreaTransformerInterface extends TransformerInterface
{
    public function transform(FocusArea $focusArea): array;
}