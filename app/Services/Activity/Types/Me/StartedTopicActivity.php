<?php
declare(strict_types=1);

namespace App\Services\Activity\Types\Me;

use App\Models\Topic;
use App\Services\Activity\Types\AbstractActivity;
use App\Services\Activity\Types\StartedTopicActivityInterface;

class StartedTopicActivity extends AbstractActivity implements StartedTopicActivityInterface, MeActivityInterface
{
    /**
     * @return string
     */
    public function getText(): string
    {
        return 'You started the topic: ['.$this->getPayloads()[Topic::class]->title.']';
    }
}
