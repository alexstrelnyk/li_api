<?php
declare(strict_types=1);

namespace App\Models;

use App\Models\Interfaces\AuditableInterface;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Carbon;
use Yajra\Auditable\AuditableTrait;

/**
 * App\Models\Client
 *
 * @property int $id
 * @property string $name
 * @property int $content_model
 * @property int $primer_notice_timing
 * @property int $content_notice_timing
 * @property int $reflection_notice_timing
 * @property int|null $created_by
 * @property int|null $updated_by
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read User|null $creator
 * @property-read string $created_by_name
 * @property-read string $updated_by_name
 * @property-read User|null $updater
 * @method static Builder|Client newModelQuery()
 * @method static Builder|Client newQuery()
 * @method static Builder|Client owned()
 * @method static Builder|Client query()
 * @method static Builder|Client whereContentModel($value)
 * @method static Builder|Client whereContentNoticeTiming($value)
 * @method static Builder|Client whereCreatedAt($value)
 * @method static Builder|Client whereCreatedBy($value)
 * @method static Builder|Client whereId($value)
 * @method static Builder|Client whereName($value)
 * @method static Builder|Client wherePrimerNoticeTiming($value)
 * @method static Builder|Client whereReflectionNoticeTiming($value)
 * @method static Builder|Client whereUpdatedAt($value)
 * @method static Builder|Client whereUpdatedBy($value)
 * @mixin Eloquent
 * @property-read Collection|Program[] $programs
 * @property-read int|null $programs_count
 * @property-read User $user
 * @property-read Collection|User[] $users
 * @property-read int|null $users_count
 * @property string|null $deactivated_at
 * @method static Builder|Client whereDeactivatedAt($value)
 * @property-read Program $activeProgram
 */
class Client extends Model implements AuditableInterface
{
    use AuditableTrait;

    public const LI_CONTENT_ONLY = 1;
    public const BLANK = 2;
    public const MIXED_CONTENT = 3;


    public const DEFAULT_CLIENT_ID = 1;



    /**
     * @var array
     */
    protected $fillable = [
        'name',
        'content_model',
        'primer_notice_timing',
        'content_notice_timing',
        'reflection_notice_timing'
    ];

    /**
     * @var array
     */
    protected $dates = [
        'deactivated_at'
    ];

    public static function boot(): void
    {
        parent::boot();

        static::deleted(static function ($client) {
            /** @var User $user */
            foreach ($client->users as $user) {
                $user->client_id = null;
                $user->update();
            }
        });
    }

    /**
     * @return array
     */
    public static function getAvailableContentModels(): array
    {
        return [
            self::LI_CONTENT_ONLY,
            self::BLANK,
            self::MIXED_CONTENT
        ];
    }

    /**
     * @return HasMany
     */
    public function programs(): HasMany
    {
        return $this->hasMany(Program::class);
    }

    /**
     * @return HasMany
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    /**
     * @return HasOne
     */
    public function user(): HasOne
    {
        return $this->hasOne(User::class);
    }

    /**
     * @return HasOne
     */
    public function activeProgram(): HasOne
    {
        return $this->hasOne(Program::class);
    }

    /**
     * @return User|null
     */
    public function getCreatedBy(): ?User
    {
        return $this->createdBy;
    }
}
