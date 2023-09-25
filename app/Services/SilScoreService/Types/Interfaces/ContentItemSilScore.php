<?php
declare(strict_types=1);

namespace App\Services\SilScoreService\Types\Interfaces;

use App\Models\ContentItem;

interface ContentItemSilScore
{
    public function getContentItem(): ContentItem;
}
