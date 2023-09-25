<?php
declare(strict_types=1);

namespace App\Models;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * Class GlobalSettings
 *
 * @package App\Models
 * @property int $id
 * @property string $key
 * @property string $value
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @method static Builder|SystemSettings newModelQuery()
 * @method static Builder|SystemSettings newQuery()
 * @method static Builder|SystemSettings query()
 * @method static Builder|SystemSettings whereCreatedAt($value)
 * @method static Builder|SystemSettings whereId($value)
 * @method static Builder|SystemSettings whereKey($value)
 * @method static Builder|SystemSettings whereUpdatedAt($value)
 * @method static Builder|SystemSettings whereValue($value)
 * @mixin Eloquent
 * @method static Builder|SystemSettings onlyAvailable()
 */
class SystemSettings extends Model
{
    /**
     * @var string
     */
    protected $table = 'global-settings';

    public const DEFAULTS = [
        'primer_notification_time' => 1,
        'content_notification_time' => 30,
        'reflection_notification_time' => 60
    ];

    /**
     * @var array
     */
    protected $fillable = [
        'key',
        'value'
    ];

    /**
     * @param string $key
     *
     * @return SystemSettings
     */
    public static function getByKey(string $key): SystemSettings
    {
        $settings = (new self())::where('key', $key)->first();

        if (!$settings instanceof SystemSettings) {
            $settings = new self(['key' => $key, 'value' => self::DEFAULTS[$key]]);
            $settings->save();
        }

        return $settings;
    }
}
