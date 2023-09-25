<?php
declare(strict_types=1);

namespace App\Services\SilScoreService\Types;

use App\Models\ContentItem;
use App\Services\SilScoreService\Types\Interfaces\ContentItemSilScore;

class BookmarkedSilScore extends AbstractSilScore implements ContentItemSilScore
{
    public const TYPE = 'bookmarked';
    public const POINTS = 1;

    /**
     * @var ContentItem
     */
    private $contentItem;

    /**
     * BookmarkedSilScore constructor.
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
