<?php
declare(strict_types=1);

namespace App\Transformers\TopicArea;

use App\Models\FocusAreaTopic;
use App\Transformers\TransformerInterface;

interface TopicAreaTransformerInterface extends TransformerInterface
{
    public function transform(FocusAreaTopic $topicArea): array;
}
