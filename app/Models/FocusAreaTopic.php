<?php
declare(strict_types=1);

namespace App\Models;

use App\Models\Interfaces\ModelStatusInterface;
use App\Models\Traits\StatusModelTrait;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Carbon;

/**
 * App\Models\FocusAreaTopic
 *
 * @property int $focus_area_id
 * @property int $topic_id
 * @method static Builder|FocusAreaTopic newModelQuery()
 * @method static Builder|FocusAreaTopic newQuery()
 * @method static Builder|FocusAreaTopic query()
 * @method static Builder|FocusAreaTopic whereFocusAreaId($value)
 * @method static Builder|FocusAreaTopic whereTopicId($value)
 * @mixin Eloquent
 * @property int $id
 * @property int $status
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Collection|ContentItem[] $contentItems
 * @property-read int|null $content_items_count
 * @property-read FocusArea $focusArea
 * @property-read Topic $topic
 * @method static Builder|FocusAreaTopic whereCreatedAt($value)
 * @method static Builder|FocusAreaTopic whereId($value)
 * @method static Builder|FocusAreaTopic whereStatus($value)
 * @method static Builder|FocusAreaTopic whereUpdatedAt($value)
 * @method static Builder|FocusAreaTopic published()
 */
class FocusAreaTopic extends Model implements ModelStatusInterface
{
    use StatusModelTrait;

    /**
     * @var string
     */
    protected $table = 'focus_areas_topics';



    /**
     * @var array
     */
    protected $fillable = [
        'focus_area_id',
        'topic_id',
        'status'
    ];

    /**
     * @return BelongsToMany|ContentItem
     */
    public function contentItems(): BelongsToMany
    {
        return $this->belongsToMany(ContentItem::class, 'focus_areas_topics_content_items', 'focus_area_topics_id');
    }

    /**
     * @return BelongsTo
     */
    public function focusArea(): BelongsTo
    {
        return $this->belongsTo(FocusArea::class);
    }

    /**
     * @return BelongsTo
     */
    public function topic(): BelongsTo
    {
        return $this->belongsTo(Topic::class);
    }

    /**
     * @return array
     */
    public static function getAvailableStatuses(): array
    {
        return [
            self::STATUS_DRAFT,
            self::STATUS_REVIEW,
            self::STATUS_PUBLISHED,
        ];
    }
}
