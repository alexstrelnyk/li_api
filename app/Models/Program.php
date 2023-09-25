<?php
declare(strict_types=1);

namespace App\Models;

use App\Models\Interfaces\ModelStatusInterface;
use App\Models\Traits\ScopeOfUserTrait;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * App\Models\Program
 *
 * @property int $id
 * @property int $client_id
 * @property string $name
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Client $client
 * @property-read Collection|Focus[] $focuses
 * @method static Builder|Program newModelQuery()
 * @method static Builder|Program newQuery()
 * @method static Builder|Program query()
 * @method static Builder|Program whereClientId($value)
 * @method static Builder|Program whereCreatedAt($value)
 * @method static Builder|Program whereId($value)
 * @method static Builder|Program whereName($value)
 * @method static Builder|Program whereUpdatedAt($value)
 * @mixin Eloquent
 * @property-read int|null $focuses_count
 * @property int|null $status
 * @method static Builder|Program whereStatus($value)
 * @method static Builder|Program ofUser(User $user)
 * @property int|null $created_by
 * @property int|null $updated_by
 * @property-read User|null $createdBy
 * @method static Builder|Program whereCreatedBy($value)
 * @method static Builder|Program whereUpdatedBy($value)
 * @property-read Collection|FocusArea[] $focusAreas
 * @property-read int|null $focus_areas_count
 */
class Program extends Model implements ModelStatusInterface
{
    use ScopeOfUserTrait;



    /**
     * @var array
     */
    protected $fillable = [
        'status',
        'name',
        'client_id'
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
    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    /**
     * @return BelongsToMany
     */
    public function focuses(): BelongsToMany
    {
        return $this->belongsToMany(Focus::class);
    }

    /**
     * @return BelongsTo
     */
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * @return HasMany
     */
    public function focusAreas(): HasMany
    {
        return $this->hasMany(FocusArea::class);
    }
}
