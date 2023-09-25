<?php
declare(strict_types=1);

namespace App\Models;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * App\Models\AboutMeSettings
 *
 * @property int $id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property int $event_practice_email
 * @property int $event_practice_notification
 * @property int $random_acts_email
 * @property int $random_acts_notification
 * @property int $login_streak_email
 * @property int $login_streak_notification
 * @property int $user_id
 * @property-read User $user
 * @method static Builder|AboutMeSettings newModelQuery()
 * @method static Builder|AboutMeSettings newQuery()
 * @method static Builder|AboutMeSettings query()
 * @method static Builder|AboutMeSettings whereCreatedAt($value)
 * @method static Builder|AboutMeSettings whereEventPracticeEmail($value)
 * @method static Builder|AboutMeSettings whereEventPracticeNotification($value)
 * @method static Builder|AboutMeSettings whereId($value)
 * @method static Builder|AboutMeSettings whereLoginStreakEmail($value)
 * @method static Builder|AboutMeSettings whereLoginStreakNotification($value)
 * @method static Builder|AboutMeSettings whereRandomActsEmail($value)
 * @method static Builder|AboutMeSettings whereRandomActsNotification($value)
 * @method static Builder|AboutMeSettings whereTipTime($value)
 * @method static Builder|AboutMeSettings whereUpdatedAt($value)
 * @method static Builder|AboutMeSettings whereUserId($value)
 * @mixin Eloquent
 * @property bool $share_activity
 * @method static Builder|AboutMeSettings whereShareActivity($value)
 */
class AboutMeSettings extends Model
{
    public const ABOUT_ME_SETTINGS_DEFAULTS = [
        'event_practice_email' => false,
        'event_practice_notification' => true,
        'random_acts_email' => false,
        'random_acts_notification' => false,
        'login_streak_email' => false,
        'login_streak_notification' => false,
        'share_activity' => true
    ];

    /**
     * @var array
     */
    protected $casts = [
        'event_practice_email' => 'boolean',
        'event_practice_notification' => 'boolean',
        'random_acts_email' => 'boolean',
        'random_acts_notification' => 'boolean',
        'login_streak_email' => 'boolean',
        'login_streak_notification' => 'boolean',
        'share_activity' => 'boolean'
    ];

    /**
     * @var array
     */
    protected $attributes = self::ABOUT_ME_SETTINGS_DEFAULTS;

    /**
     * @var array
     */
    protected $fillable = [
        'tip_time',
        'event_practice_email',
        'event_practice_notification',
        'random_acts_email',
        'random_acts_notification',
        'login_streak_email',
        'login_streak_notification',
        'share_activity'
    ];

    /**
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
