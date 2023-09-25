<?php
declare(strict_types=1);

namespace App\Models;

use App\Models\Interfaces\ModelStatusInterface;
use App\Models\Traits\ScopeOfUserTrait;
use App\Models\Traits\StatusModelTrait;
use Cviebrock\EloquentSluggable\Sluggable;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;
use Yajra\Auditable\AuditableTrait;

/**
 * App\Models\Topic
 *
 * @property int $id
 * @property string $title
 * @property string $slug
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property int $focus_id
 * @property int|null $status
 * @property int|null $has_practice
 * @property string|null $introduction
 * @property string|null $calendar_prompt_text
 * @property int|null $created_by
 * @property int|null $updated_by
 * @property int|null $content_item_id
 * @property-read ContentItem|null $contentItem
 * @property-read Collection|ContentItem[] $contentItems
 * @property-read int|null $content_items_count
 * @property-read User|null $createdBy
 * @property-read User|null $creator
 * @property-read Focus $focus
 * @property-read Collection|FocusArea[] $focusAreas
 * @property-read int|null $focus_areas_count
 * @property-read string $created_by_name
 * @property-read string $updated_by_name
 * @property-read Collection|ScheduleTopic[] $scheduleTopics
 * @property-read int|null $schedule_topics_count
 * @property-read Collection|FocusAreaTopic[] $topicAreas
 * @property-read int|null $topic_areas_count
 * @property-read User|null $updatedBy
 * @property-read User|null $updater
 * @method static Builder|Topic byFocus(Focus $focus)
 * @method static Builder|Topic findSimilarSlugs($attribute, $config, $slug)
 * @method static Builder|Topic newModelQuery()
 * @method static Builder|Topic newQuery()
 * @method static Builder|Topic ofAppUser(Focus $focus, User $user)
 * @method static Builder|Topic ofUser(User $user)
 * @method static Builder|Topic owned()
 * @method static Builder|Topic published()
 * @method static Builder|Topic query()
 * @method static Builder|Topic whereCalendarPromptText($value)
 * @method static Builder|Topic whereContentItemId($value)
 * @method static Builder|Topic whereCreatedAt($value)
 * @method static Builder|Topic whereCreatedBy($value)
 * @method static Builder|Topic whereFocusId($value)
 * @method static Builder|Topic whereHasPractice($value)
 * @method static Builder|Topic whereId($value)
 * @method static Builder|Topic whereIntroduction($value)
 * @method static Builder|Topic whereSlug($value)
 * @method static Builder|Topic whereStatus($value)
 * @method static Builder|Topic whereTitle($value)
 * @method static Builder|Topic whereUpdatedAt($value)
 * @method static Builder|Topic whereUpdatedBy($value)
 * @mixin Eloquent
 * @property string $info_image
 * @method static Builder|Topic ofFocus(Focus $focus)
 * @method static Builder|Topic whereInfoImage($value)
 * @method static Builder|Topic learnBased()
 * @method static Builder|Topic practiceBased()
 */
class Topic extends Model implements ModelStatusInterface
{
    use StatusModelTrait;
    use Sluggable;
    use AuditableTrait;
    use ScopeOfUserTrait;

    /**
     * @var string
     */
    protected $table = 'topics';



    /**
     * @var array
     */
    protected $casts = [
        'has_reflection' => 'boolean'
    ];

    /**
     * @var array
     */
    protected $fillable = [
        'title',
        'focus_id',
        'content_item_id',
        'introduction',
        'calendar_prompt_text',
        'has_practice',
        'status'
    ];

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

    /**
     * @return BelongsTo
     */
    public function focus(): BelongsTo
    {
        return $this->belongsTo(Focus::class);
    }

    /**
     * @return array
     */
    public function sluggable(): array
    {
        return [
            'slug' => [
                'source' => 'title'
            ]
        ];
    }

    /**
     * @return HasMany
     */
    public function contentItems(): HasMany
    {
        return $this->hasMany(ContentItem::class);
    }

    /**
     * @return BelongsTo
     */
    public function contentItem(): BelongsTo
    {
        return $this->belongsTo(ContentItem::class);
    }

    /**
     * @return HasMany|ScheduleTopic
     */
    public function scheduleTopics(): HasMany
    {
        return $this->hasMany(ScheduleTopic::class);
    }

    /**
     * @return BelongsTo
     */
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * @return BelongsTo
     */
    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * @param Builder $builder
     * @param Focus $focus
     *
     * @return Builder
     */
    public function scopeByFocus(Builder $builder, Focus $focus): Builder
    {
        return $builder->where('focus_id', $focus->id);
    }

    /**
     * @return HasMany
     */
    public function focusAreas(): HasMany
    {
        return $this->hasMany(FocusArea::class, 'focus_id');
    }

    /**
     * @param Builder $builder
     * @param Focus $focus
     * @param User $user
     *
     * @return Builder
     */
    public function scopeOfAppUser(Builder $builder, Focus $focus, User $user): Builder
    {
        $client = $user->client;
        $program = $client->activeProgram;

        return $builder->whereHas('focusAreas', static function (Builder $builder) use ($focus, $program) {
            $builder->where('focus_id', $focus->id)->where('program_id', $program->id);
        });
    }

    /**
     * @return HasMany
     */
    public function topicAreas(): HasMany
    {
        return $this->hasMany(FocusAreaTopic::class);
    }

    /**
     * @param Builder $builder
     * @param Focus $focus
     *
     * @return Builder
     */
    public function scopeOfFocus(Builder $builder, Focus $focus): Builder
    {
        return $builder->where('focus_id', $focus->id);
    }

    /**
     * @param Builder $builder
     *
     * @return Builder
     */
    public function scopeLearnBased(Builder $builder): Builder
    {
        return $builder->where('has_practice', false);
    }

    /**
     * @param Builder $builder
     *
     * @return Builder
     */
    public function scopePracticeBased(Builder $builder): Builder
    {
        return $builder->where('has_practice', true);
    }
}
