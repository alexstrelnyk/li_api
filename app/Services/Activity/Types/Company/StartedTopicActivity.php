<?php
declare(strict_types=1);

namespace App\Services\Activity\Types\Company;

use App\Models\Topic;
use App\Services\Activity\Types\AbstractActivity;
use App\Services\Activity\Types\StartedTopicActivityInterface;

class StartedTopicActivity extends AbstractActivity implements StartedTopicActivityInterface, CompanyActivityInterface
{
    /**
     * @return string
     */
    public function getText(): string
    {
        return $this->getUser()->name . ' started the topic: ['.$this->getPayloads()[Topic::class]->title.']';
    }

}
