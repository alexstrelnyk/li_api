<?php
declare(strict_types=1);

namespace App\Transformers\SilScore;

use App\Models\SilScore;
use League\Fractal\TransformerAbstract;

class SilScoreTransformer extends TransformerAbstract implements SilScoreTransformerInterface
{
    /**
     * @param SilScore $silScore
     *
     * @return array
     */
    public function transform(SilScore $silScore): array
    {
        return [
            'sil_score' => $silScore->user->sil_score,
            'bonus_points' => $silScore->points,
        ];
    }
}
