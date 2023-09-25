<?php

namespace App\Services\Activity\Types\Company;

use App\Models\ContentItem;
use App\Services\Activity\Types\AbstractActivity;
use App\Services\Activity\Types\WrittenReflectionActivityInterface;

class WrittenReflectionActivity extends AbstractActivity implements WrittenReflectionActivityInterface, CompanyActivityInterface
{

    /**
     * @return string
     */
    public function getText(): string
    {
        return $this->getUser()->name . ' wrote a reflection for ['.$this->getPayloads()[ContentItem::class]->title.']';
    }

}