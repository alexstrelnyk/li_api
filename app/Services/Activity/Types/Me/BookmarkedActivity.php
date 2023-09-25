<?php
declare(strict_types=1);

namespace App\Services\Activity\Types\Me;

use App\Models\ContentItem;
use App\Services\Activity\Types\AbstractActivity;
use App\Services\Activity\Types\BookmarkedActivityInterface;

class BookmarkedActivity extends AbstractActivity implements BookmarkedActivityInterface, MeActivityInterface
{

    public function getText(): string
    {
        return 'You bookmarked ['.$this->getPayloads()[ContentItem::class]->title.']';
    }
}