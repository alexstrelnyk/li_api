<?php

namespace App\Services\Activity\Types\Me;

use App\Models\ContentItem;
use App\Services\Activity\Types\AbstractActivity;
use App\Services\Activity\Types\WrittenReflectionActivityInterface;

class WrittenReflectionActivity extends AbstractActivity implements WrittenReflectionActivityInterface, MeActivityInterface
{

    /**
     * @return string
     */
    public function getText(): string
    {
        return 'You wrote a reflection for ['.$this->getPayloads()[ContentItem::class]->title.']';
    }
}