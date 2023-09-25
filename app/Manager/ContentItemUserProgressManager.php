<?php
declare(strict_types=1);

namespace App\Manager;

use App\Factory\ContentItemUserProgressFactory;
use App\Models\ContentItem;
use App\Models\ContentItemUserProgress;
use App\Models\Program;
use App\Models\User;
use Carbon\Carbon;
use Exception;
use LogicException;

class ContentItemUserProgressManager
{
    public const PRIMER_VIEWED_TYPE = 'Primer';
    public const CONTENT_VIEWED_TYPE = 'Content';
    public const REFLECTION_VIEWED_TYPE = 'Reflection';

    /**
     * @var ContentItemUserProgressFactory
     */
    private $contentItemUserProgressFactory;

    /**
     * ContentItemUserProgressManager constructor.
     *
     * @param ContentItemUserProgressFactory $contentItemUserProgressFactory
     */
    public function __construct(ContentItemUserProgressFactory $contentItemUserProgressFactory)
    {
        $this->contentItemUserProgressFactory = $contentItemUserProgressFactory;
    }

    /**
     * @param ContentItem $contentItem
     * @param User $user
     *
     * @param Program|null $program
     *
     * @return ContentItemUserProgress
     */
    public function create(ContentItem $contentItem, User $user, ?Program $program = null): ContentItemUserProgress
    {
        $contentItemUserProgress = $this->contentItemUserProgressFactory->create($contentItem, $user, $program);
        $contentItemUserProgress->save();

        return $contentItemUserProgress;
    }

    /**
     * @param ContentItemUserProgress $contentItemUserProgress
     *
     * @return bool
     */
    public function calculateCompleted(ContentItemUserProgress $contentItemUserProgress): bool
    {
        $booleans = [
            'primer',
            'content'
        ];

        if ($contentItemUserProgress->contentItem->has_reflection) {
            $booleans[] = 'reflection';
        }

        $result = true;

        foreach ($booleans as $boolean) {
            $result = $result && $contentItemUserProgress->{$boolean};
        }

        return $result;
    }

    /**
     * @param ContentItemUserProgress $contentItemUserProgress
     */
    public function complete(ContentItemUserProgress $contentItemUserProgress): void
    {
        $contentItemUserProgress->primer = true;
        $contentItemUserProgress->content = true;

        if ($contentItemUserProgress->contentItem->has_reflection) {
            $contentItemUserProgress->reflection = true;
        }
        $contentItemUserProgress->completed = true;
        $contentItemUserProgress->completed_at = Carbon::now();
        $contentItemUserProgress->save();
    }

    /**
     * @param ContentItemUserProgress $contentItemUserProgress
     */
    public function reset(ContentItemUserProgress $contentItemUserProgress): void
    {
        $contentItemUserProgress->primer = false;
        $contentItemUserProgress->content = false;
        $contentItemUserProgress->reflection = false;
        $contentItemUserProgress->save();
    }

    /**
     * @param ContentItemUserProgress $contentItemUserProgress
     *
     * @throws Exception
     */
    public function delete(ContentItemUserProgress $contentItemUserProgress): void
    {
        $contentItemUserProgress->delete();
    }

    /**
     * @param ContentItemUserProgress $contentItemUserProgress
     * @param string $type
     */
    public function setViewed(ContentItemUserProgress $contentItemUserProgress, string $type): void
    {
        switch ($type) {
            case self::PRIMER_VIEWED_TYPE:
                $contentItemUserProgress->primer = true;
                break;
            case self::CONTENT_VIEWED_TYPE:
                $contentItemUserProgress->content = true;
                break;
            case self::REFLECTION_VIEWED_TYPE:
                $contentItemUserProgress->reflection = true;
                break;
            default:
                throw new LogicException(sprintf('Could not resolve "%s" type', $type));
        }

        $contentItemUserProgress->save();
    }
}
