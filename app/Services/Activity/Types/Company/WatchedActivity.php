<?php
declare(strict_types=1);

namespace App\Services\Activity\Types\Company;

use App\Models\ContentItem;
use App\Services\Activity\Types\AbstractActivity;
use App\Services\Activity\Types\WatchedActivityInterface;


class WatchedActivity extends AbstractActivity implements WatchedActivityInterface, CompanyActivityInterface
{
    /**
     * @return string
     */
    public function getText(): string
    {
        return $this->getUser()->name . ' watched ['.$this->getPayloads()[ContentItem::class]->title.']';
    }
}
