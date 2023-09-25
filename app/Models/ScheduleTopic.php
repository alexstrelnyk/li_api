<?php
declare(strict_types=1);

namespace App\Models;

use DateTime;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * App\Models\ScheduleTopic
 *
 * @property int $id
 * @property float $user_id
 * @property float $topic_id
 * @property Carbon $occurs_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Topic $topic
 * @property-read User $user
 * @method static Builder|ScheduleTopic newModelQuery()
 * @method static Builder|ScheduleTopic newQuery()
 * @method static Builder|ScheduleTopic notOccurs()
 * @method static Builder|ScheduleTopic ofTopic(Topic $topic)
 * @method static Builder|ScheduleTopic ofUser(User $user)
 * @method static Builder|ScheduleTopic query()
 * @method static Builder|ScheduleTopic whereCreatedAt($value)
 * @method static Builder|ScheduleTopic whereId($value)
 * @method static Builder|ScheduleTopic whereOccursAt($value)
 * @method static Builder|ScheduleTopic whereTopicId($value)
 * @method static Builder|ScheduleTopic whereUpdatedAt($value)
 * @method static Builder|ScheduleTopic whereUserId($value)
 * @mixin Eloquent
 * @method static Builder|ScheduleTopic ofUserAndTopic(User $user, Topic $topic)
 * @property string|null $sent_at
 * @method static Builder|ScheduleTopic whereSentAt($value)
 * @property string|null $primer_sent_at
 * @property string|null $content_sent_at
 * @property string|null $reflection_sent_at
 * @method static Builder|ScheduleTopic whereContentSentAt($value)
 * @method static Builder|ScheduleTopic wherePrimerSentAt($value)
 * @method static Builder|ScheduleTopic whereReflectionSentAt($value)
 * @method static Builder|ScheduleTopic contentNotSent()
 * @method static Builder|ScheduleTopic contentSent()
 * @method static Builder|ScheduleTopic primerNotSent()
 * @method static Builder|ScheduleTopic primerSent()
 * @method static Builder|ScheduleTopic reflectionNotSent()
 * @method static Builder|ScheduleTopic reflectionSent()
 * @method static Builder|ScheduleTopic contentPracticeBasedItemHasReflection()
 * @method static Builder|ScheduleTopic laterThan(DateTime $dateTime)
 */
class ScheduleTopic extends Model
{
    /**
     * @var string
     */
    protected $table = 'scheduled_events';

    /**
     * @var array
     */
    protected $fillable = [
        'user_id',
        'topic_id',
        'content_item_id'
    ];

    /**
     * @var array
     */
    protected $dates = [
        'occurs_at',
        'sent_at'
    ];

    /**
     * @param Builder $builder
     * @param User $user
     * @param Topic $topic
     *
     * @return Builder
     */
    public function scopeOfUserAndTopic(Builder $builder, User $user, Topic $topic): Builder
    {
        return $builder->where('user_id', $user->id)->where('topic_id', $topic->id);
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
    public function topic(): BelongsTo
    {
        return $this->belongsTo(Topic::class);
    }

    /**
     * @param Builder $builder
     *
     * @return Builder
     */
    public function scopeNotOccurs(Builder $builder): Builder
    {
        return $builder->where('occurs_at', null);
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
     * @param Topic $topic
     *
     * @return Builder
     */
    public function scopeOfTopic(Builder $builder, Topic $topic): Builder
    {
        return $builder->where('topic_id', $topic->id);
    }

    public function scopePrimerSent(Builder $builder): Builder
    {
        return $builder->whereNotNull('primer_sent_at');
    }

    public function scopePrimerNotSent(Builder $builder): Builder
    {
        return $builder->whereNull('primer_sent_at');
    }

    public function scopeContentSent(Builder $builder): Builder
    {
        return $builder->whereNotNull('content_sent_at');
    }

    public function scopeContentNotSent(Builder $builder): Builder
    {
        return $builder->whereNull('content_sent_at');
    }

    public function scopeReflectionSent(Builder $builder): Builder
    {
        return $builder->whereNotNull('reflection_sent_at');
    }

    public function scopeReflectionNotSent(Builder $builder): Builder
    {
        return $builder->whereNull('reflection_sent_at');
    }

    public function scopeContentPracticeBasedItemHasReflection(Builder $builder): Builder
    {
        return $builder->whereHas('topic', static function (Builder $builder) {
            $builder->whereHas('contentItem', static function (Builder $builder) {
                /** @var ContentItem $builder */
                $builder->withReflection();
            });
        });
    }

    /**
     * @param Builder $builder
     * @param DateTime $dateTime
     *
     * @return Builder
     */
    public function scopeLaterThan(Builder $builder, DateTime $dateTime): Builder
    {
        return $builder->where('occurs_at', '<=', $dateTime);
    }
}
