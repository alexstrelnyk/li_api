<?php

namespace App\Services\Activity\Types\Me;


use App\Models\ContentItem;
use App\Services\Activity\Types\AbstractActivity;
use App\Services\Activity\Types\CompletedSessionsActivityInterface;


class CompletedCompanySessionsActivity extends AbstractActivity implements CompletedSessionsActivityInterface, MeActivityInterface
{
    /**
     * @return string
     */
    public function getText(): string
    {
        return 'You completed all coaching sessions in "['.$this->getPayloads()[ContentItem::class]->title.']"';
    }

}