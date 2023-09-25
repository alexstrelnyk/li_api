<?php

namespace App\Services\Activity\Types\Company;


use App\Models\ContentItem;
use App\Services\Activity\Types\AbstractActivity;
use App\Services\Activity\Types\CompletedSessionsActivityInterface;


class CompletedCompanySessionsActivity extends AbstractActivity implements CompletedSessionsActivityInterface, CompanyActivityInterface
{
    /**
     * @return string
     */
    public function getText(): string
    {
        return $this->getUser()->name . ' completed all coaching sessions in "['.$this->getPayloads()[ContentItem::class]->title.']"';
    }

}