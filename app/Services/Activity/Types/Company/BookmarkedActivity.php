<?php
declare(strict_types=1);

namespace App\Services\Activity\Types\Company;

use App\Models\ContentItem;
use App\Services\Activity\Types\AbstractActivity;
use App\Services\Activity\Types\BookmarkedActivityInterface;

class BookmarkedActivity extends AbstractActivity implements BookmarkedActivityInterface, CompanyActivityInterface
{

    public function getText(): string
    {
        return $this->getUser()->name . ' bookmarked ['.$this->getPayloads()[ContentItem::class]->title.']';
    }
}