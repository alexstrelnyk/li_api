<?php

namespace App\Models;

use App\Models\Interfaces\ModelStatusInterface;
use App\Models\Traits\StatusModelTrait;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Support\Carbon;

/**
 * App\Models\FocusArea
 *
 * @method static Builder|FocusArea newModelQuery()
 * @method static Builder|FocusArea newQuery()
 * @method static Builder|FocusArea query()
 * @mixin Eloquent
 * @property-read Collection|ContentItem[] $contentItems
 * @property-read int|null $content_items_count
 * @property-read Focus $focus
 * @property-read Collection|Topic[] $topics
 * @property-read int|null $topics_count
 * @property-read User $creator
 * @property-read string $created_by_name
 * @property-read string $updated_by_name
 * @property mixed $status
 * @property-read User $updater
 * @method static Builder|FocusArea owned()
 * @property int $id
 * @property int $program_id
 * @property int $focus_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Program $program
 * @method static Builder|FocusArea whereCreatedAt($value)
 * @method static Builder|FocusArea whereFocusId($value)
 * @method static Builder|FocusArea whereId($value)
 * @method static Builder|FocusArea whereProgramId($value)
 * @method static Builder|FocusArea whereStatus($value)
 * @method static Builder|FocusArea whereUpdatedAt($value)
 * @property-read Collection|FocusAreaTopic[] $topicAreas
 * @property-read int|null $topic_areas_count
 * @method static Builder|FocusArea ofFocus(Focus $focus)
 * @method static Builder|FocusArea ofProgram(Program $program)
 * @method static Builder|FocusArea published()
 */
class FocusArea extends Model implements ModelStatusInterface
{
    use StatusModelTrait;

    /**
     * @var string
     */
    protected $table = 'focus_area';



    /**
     * @var array
     */
    protected $fillable = [
        'program_id',
        'focus_id',
        'status'
    ];

    /**
     * @return BelongsTo
     */
    public function program(): BelongsTo
    {
        return $this->belongsTo(Program::class);
    }

    /**
     * @return BelongsToMany
     */
    public function topics(): BelongsToMany
    {
        return $this->belongsToMany(Topic::class, 'focus_areas_topics');
    }

    /**
     * @return HasMany
     */
    public function topicAreas(): HasMany
    {
        return $this->hasMany(FocusAreaTopic::class);
    }

    /**
     * @return BelongsTo
     */
    public function focus(): BelongsTo
    {
        return $this->belongsTo(Focus::class);
    }

    /**
     * @return HasManyThrough
     */
    public function contentItems(): HasManyThrough
    {
        return $this->hasManyThrough(ContentItem::class, FocusAreaTopic::class);
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

    /**
     * @param Builder $builder
     * @param Program $program
     *
     * @return Builder
     */
    public function scopeOfProgram(Builder $builder, Program $program): Builder
    {
        return $builder->where($this->program()->getForeignKeyName(), $program->id);
    }

    /**
     * @param Builder $builder
     * @param Focus $focus
     *
     * @return Builder
     */
    public function scopeOfFocus(Builder $builder, Focus $focus): Builder
    {
        return $builder->where($this->focus()->getForeignKeyName(), $focus->id);
    }
}
