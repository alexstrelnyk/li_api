<?php
declare(strict_types=1);

namespace App\Models;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * App\Models\Feedback
 *
 * @property-read User $user
 * @method static Builder|Feedback newModelQuery()
 * @method static Builder|Feedback newQuery()
 * @method static Builder|Feedback query()
 * @mixin Eloquent
 * @property int $id
 * @property int $user_id
 * @property string $text
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @method static Builder|Feedback whereCreatedAt($value)
 * @method static Builder|Feedback whereId($value)
 * @method static Builder|Feedback whereText($value)
 * @method static Builder|Feedback whereUpdatedAt($value)
 * @method static Builder|Feedback whereUserId($value)
 */
class Feedback extends Model
{
    /**
     * @var array
     */
    protected $fillable = ['user_id', 'text'];



    /**
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
