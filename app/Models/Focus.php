<?php
declare(strict_types=1);

namespace App\Models;

use App\Models\Interfaces\AuditableInterface;
use App\Models\Interfaces\ModelStatusInterface;
use App\Models\Traits\ScopeOfUserTrait;
use App\Models\Traits\StatusModelTrait;
use Cviebrock\EloquentSluggable\Sluggable;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;
use Yajra\Auditable\AuditableTrait;

/**
 * App\Models\Focus
 *
 * @property-read Collection|Topic[] $topics
 * @method static Builder|Focus newModelQuery()
 * @method static Builder|Focus newQuery()
 * @method static Builder|Focus query()
 * @mixin Eloquent
 * @property int $id
 * @property string $title
 * @property string $slug
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property int|null $practice
 * @property int|null $status
 * @property string|null $color
 * @method static Builder|Focus whereColor($value)
 * @method static Builder|Focus whereCreatedAt($value)
 * @method static Builder|Focus whereId($value)
 * @method static Builder|Focus wherePractice($value)
 * @method static Builder|Focus whereSlug($value)
 * @method static Builder|Focus whereStatus($value)
 * @method static Builder|Focus whereTitle($value)
 * @method static Builder|Focus whereUpdatedAt($value)
 * @property int $image_id
 * @property-read Image $image
 * @method static Builder|Focus whereImageId($value)
 * @property string $image_url
 * @method static Builder|Focus whereImageUrl($value)
 * @property int|null $created_by
 * @property int|null $updated_by
 * @method static Builder|Focus findSimilarSlugs($attribute, $config, $slug)
 * @method static Builder|Focus whereCreatedBy($value)
 * @method static Builder|Focus whereUpdatedBy($value)
 * @property-read User|null $creator
 * @property-read string $created_by_name
 * @property-read string $updated_by_name
 * @property-read User|null $updater
 * @method static Builder|Focus owned()
 * @property-read Program $program
 * @property int $program_id
 * @method static Builder|Focus whereProgramId($value)
 * @property string|null $video_overview
 * @method static Builder|Focus whereVideoOverview($value)
 * @property-read int|null $topics_count
 * @property-read User|null $createdBy
 * @property-read User|null $updatedBy
 * @method static Builder|Focus ofUser(User $user)
 * @property-read Collection|ContentItem[] $contentItems
 * @property-read int|null $content_items_count
 * @method static Builder|Focus byProgram(Program $program)
 * @property-read Collection|Program[] $programs
 * @property-read int|null $programs_count
 * @property-read FocusArea $focusAreas
 * @property-read int|null $focus_areas_count
 * @method Builder|Focus published()
 */
class Focus extends Model implements ModelStatusInterface, AuditableInterface
{
    use StatusModelTrait;
    use Sluggable;
    use AuditableTrait;
    use ScopeOfUserTrait;

    /**
     * @var string
     */
    protected $table = 'focuses';



    /**
     * @var array
     */
    protected $fillable = [
        'title', 'status', 'program_id', 'color', 'image_url', 'video_overview'
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
    public function programs(): BelongsToMany
    {
        return $this->belongsToMany(Program::class);
    }

    /**
     * @return HasMany
     */
    public function topics(): HasMany
    {
        return $this->hasMany(Topic::class);
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
     * @return Builder
     */
    public function scopeByProgram(Builder $builder, Program $program): Builder
    {
        return $builder->whereHas('programs', static function (Builder $builder) use ($program) {
            $builder->where('id', $program->id);
        });
    }

    /**
     * Return the sluggable configuration array for this model.
     *
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
     * @return User|null
     */
    public function getCreatedBy(): ?User
    {
        return $this->createdBy;
    }

    /**
     * @return HasMany
     */
    public function contentItems(): HasMany
    {
        return $this->hasMany(ContentItem::class);
    }

    /**
     * @return HasMany
     */
    public function focusAreas(): HasMany
    {
        return $this->hasMany(FocusArea::class);
    }
}
