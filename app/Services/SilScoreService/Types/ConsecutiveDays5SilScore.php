<?php
declare(strict_types=1);

namespace App\Services\SilScoreService\Types;

use App\Models\ContentItem;
use App\Services\SilScoreService\Types\Interfaces\ContentItemSilScore;

class ConsecutiveDays5SilScore extends AbstractSilScore implements ContentItemSilScore
{
    public const TYPE = 'consecutive-days-5';

    public const POINTS = 5;

    /**
     * @var int
     */
    protected $points = 2;

    /**
     * @var ContentItem
     */
    protected $contentItem;

    /**
     * SessionCompletedSilScore constructor.
     *
     * @param ContentItem $contentItem
     */
    public function __construct(ContentItem $contentItem)
    {
        $this->contentItem = $contentItem;
    }

    /**
     * @return ContentItem
     */
    public function getContentItem(): ContentItem
    {
        return $this->contentItem;
    }
}
