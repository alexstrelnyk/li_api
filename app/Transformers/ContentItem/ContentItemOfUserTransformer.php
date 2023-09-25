<?php
declare(strict_types=1);

namespace App\Transformers\ContentItem;

use App\Models\ContentItem;
use App\Models\ContentItemUserProgress;
use App\Models\User;
use App\Transformers\ContentItemProgressMetaTransformer;
use App\Transformers\ContentItemProgressTransformer;
use Exception;

class ContentItemOfUserTransformer extends ContentItemTransformer
{
    /**
     * @var User
     */
    protected $user;

    /**
     * @var ContentItemProgressTransformer
     */
    private $progressTransformer;

    /**
     * @var ContentItemProgressMetaTransformer
     */
    private $progressMetaTransformer;

    /**
     * ContentItemOfUserTransformer constructor.
     *
     * @param User $user
     * @param ContentItemProgressTransformer $progressTransformer
     * @param ContentItemProgressMetaTransformer $progressMetaTransformer
     */
    public function __construct(User $user, ContentItemProgressTransformer $progressTransformer, ContentItemProgressMetaTransformer $progressMetaTransformer)
    {
        $this->user = $user;
        $this->progressTransformer = $progressTransformer;
        $this->progressMetaTransformer = $progressMetaTransformer;
    }

    /**
     * @param ContentItem $contentItem
     *
     * @return array
     * @throws Exception
     */
    public function transform(ContentItem $contentItem): array
    {
        $contentItemUserProgress = ContentItemUserProgress::ofUserAndItem($this->user, $contentItem)->first();

        return array_merge(parent::transform($contentItem), [
            'content_type' => $contentItem->getTypeSlug(),
            'meta' => $contentItemUserProgress ? fractal($contentItemUserProgress, $this->progressMetaTransformer)->toArray()['data'] : null,
            'progress' => $contentItemUserProgress ? fractal($contentItemUserProgress, $this->progressTransformer)->toArray()['data'] : null,
        ]);
    }
}
