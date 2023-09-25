<?php
declare(strict_types=1);

namespace App\Transformers\ContentItem\Focus;

use App\Models\Focus;
use App\Transformers\AbstractTransformer;
use Exception;

class CutFocusTransformer extends AbstractTransformer implements CutFocusTransformerInterface
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
            'title' => $focus->title
        ];
    }
}
