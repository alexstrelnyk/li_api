<?php
declare(strict_types=1);

namespace App\Models;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * App\Models\ContentItemUserProgress
 *
 * @property int $id
 * @property int $user_id
 * @property int $content_item_id
 * @property bool $primer
 * @property bool $content
 * @property bool $reflection
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string|null $completed_at
 * @property-read ContentItem $contentItem
 * @property-read User $user
 * @method static Builder|ContentItemUserProgress newModelQuery()
 * @method static Builder|ContentItemUserProgress newQuery()
 * @method static Builder|ContentItemUserProgress ofUserAndItem(User $user, ContentItem $contentItem)
 * @method static Builder|ContentItemUserProgress query()
 * @method static Builder|ContentItemUserProgress whereCompleted($value)
 * @method static Builder|ContentItemUserProgress whereCompletedAt($value)
 * @method static Builder|ContentItemUserProgress whereContent($value)
 * @method static Builder|ContentItemUserProgress whereContentItemId($value)
 * @method static Builder|ContentItemUserProgress whereCreatedAt($value)
 * @method static Builder|ContentItemUserProgress whereId($value)
 * @method static Builder|ContentItemUserProgress wherePrimer($value)
 * @method static Builder|ContentItemUserProgress whereReflection($value)
 * @method static Builder|ContentItemUserProgress whereUpdatedAt($value)
 * @method static Builder|ContentItemUserProgress whereUserId($value)
 * @mixin Eloquent
 * @property int $completed
 * @property int $program_id
 * @property-read Program $program
 * @method static Builder|ContentItemUserProgress ofUserAndItemAndProgram(User $user, ContentItem $contentItem, Program $program)
 * @method static Builder|ContentItemUserProgress whereProgramId($value)
 * @property int|null $reflection_id
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ContentItemUserProgress whereReflectionId($value)
 */
class ContentItemUserProgress extends Model
{
    /**
     * @var array
     */
    protected $fillable = ['user_id', 'content_item_id'];

    /**
     * @var array
     */
    protected $casts = [
        'primer' => 'boolean',
        'content' => 'boolean',
        'reflection' => 'boolean'
    ];

    /**
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return BelongsTo
     */
    public function contentItem(): BelongsTo
    {
        return $this->belongsTo(ContentItem::class);
    }

    /**
     * @return BelongsTo
     */
    public function program(): BelongsTo
    {
        return $this->belongsTo(Program::class);
    }

    /**
     * @return BelongsTo
     */
    public function reflection(): BelongsTo
    {
        return $this->belongsTo(UserReflection::class);
    }

    /**
     * @param Builder $builder
     * @param User $user
     * @param ContentItem $contentItem
     *
     * @return Builder
     */
    public function scopeOfUserAndItem(Builder $builder, User $user, ContentItem $contentItem): Builder
    {
        return $builder->where('user_id', $user->id)
            ->where('content_item_id', $contentItem->id);
    }

    /**
     * @param Builder $builder
     * @param User $user
     * @param ContentItem $contentItem
     * @param Program $program
     *
     * @return Builder
     */
    public function scopeOfUserAndItemAndProgram(Builder $builder, User $user, ContentItem $contentItem, Program $program): Builder
    {
        return $builder->where('user_id', $user->id)
            ->where('content_item_id', $contentItem->id)
            ->where('program_id', $program->id);
    }
}
