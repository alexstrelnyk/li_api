<?php
declare(strict_types=1);

namespace App\Services\Activity\Types\Me;

use App\Models\ContentItem;
use App\Services\Activity\Types\AbstractActivity;
use App\Services\Activity\Types\WatchedActivityInterface;


class WatchedActivity extends AbstractActivity implements WatchedActivityInterface, MeActivityInterface
{
    /**
     * @return string
     */
    public function getText(): string
    {
        return 'You watched ['.$this->getPayloads()[ContentItem::class]->title.']';
    }
}
