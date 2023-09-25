<?php
declare(strict_types=1);

namespace App\Models;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * Class SilScoreActivity
 *
 * @package App\Models
 * @property int $id
 * @property string $type
 * @property int $points
 * @property int $user_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read User $user
 * @method static Builder|SilScore newModelQuery()
 * @method static Builder|SilScore newQuery()
 * @method static Builder|SilScore query()
 * @method static Builder|SilScore whereCreatedAt($value)
 * @method static Builder|SilScore whereId($value)
 * @method static Builder|SilScore wherePoints($value)
 * @method static Builder|SilScore whereType($value)
 * @method static Builder|SilScore whereUpdatedAt($value)
 * @method static Builder|SilScore whereUserId($value)
 * @mixin Eloquent
 * @property string|null $date_at
 * @method static Builder|SilScore whereDateAt($value)
 * @property int|null $content_item_id
 * @property int|null $reflection_id
 * @property-read ContentItem|null $contentItem
 * @property-read UserReflection|null $reflection
 * @method static Builder|SilScore ofContentItem(ContentItem $contentItem)
 * @method static Builder|SilScore ofType($type)
 * @method static Builder|SilScore ofUser(User $user)
 * @method static Builder|SilScore whereContentItemId($value)
 * @method static Builder|SilScore whereReflectionId($value)
 * @method static Builder|SilScore ofUserOfContentItem(User $user, ContentItem $contentItem)
 * @method static Builder|SilScore ofUserOfReflection(User $user, UserReflection $reflection)
 */
class SilScore extends Model
{
    /**
     * @var string
     */
    protected $table = 'sil_score';

    /**
     * @var array
     */
    protected $fillable = [
        'user_id',
        'type',
        'points'
    ];

    /**
     * @var array
     */
    protected $dates = [
        'created_at'
    ];

    /**
     * @param Builder $builder
     * @param User $user
     * @param UserReflection $reflection
     *
     * @return Builder
     */
    public function scopeOfUserOfReflection(Builder $builder, User $user, UserReflection $reflection): Builder
    {
        return $builder->where('user_id', $user->id)->where('reflection_id', $reflection->id);
    }

    /**
     * @param Builder $builder
     * @param User $user
     * @param ContentItem $contentItem
     *
     * @return Builder
     */
    public function scopeOfUserOfContentItem(Builder $builder, User $user, ContentItem $contentItem): Builder
    {
        return $builder->where('user_id', $user->id)->where('content_item_id', $contentItem->id);
    }

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
    public function reflection(): BelongsTo
    {
        return $this->belongsTo(UserReflection::class, 'reflection_id');
    }

    /**
     * @param Builder $builder
     * @param User $user
     *
     * @return Builder
     */
    public function scopeOfUser(Builder $builder, User $user): Builder
    {
        return $builder->where('user_id', $user->id);
    }

    /**
     * @param Builder $builder
     * @param ContentItem $contentItem
     *
     * @return Builder
     */
    public function scopeOfContentItem(Builder $builder, ContentItem $contentItem): Builder
    {
        return $builder->where('content_item_id', $contentItem->id);
    }

    /**
     * @param Builder $builder
     * @param string $type
     *
     * @return Builder
     */
    public function scopeOfType(Builder $builder, string $type): Builder
    {
        return $builder->where('type', $type);
    }
}
