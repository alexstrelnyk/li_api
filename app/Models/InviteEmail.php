<?php
declare(strict_types=1);

namespace App\Models;

use App\Models\Interfaces\StoreMagicTokenInterface;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

/**
 * App\Models\InviteEmail
 *
 * @method static Builder|InviteEmail newModelQuery()
 * @method static Builder|InviteEmail newQuery()
 * @method static Builder|InviteEmail query()
 * @mixin Eloquent
 * @property int $id
 * @property string $email
 * @property int $status
 * @property string|null $magic_link_token
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @method static Builder|InviteEmail whereCreatedAt($value)
 * @method static Builder|InviteEmail whereEmail($value)
 * @method static Builder|InviteEmail whereId($value)
 * @method static Builder|InviteEmail whereMagicLinkToken($value)
 * @method static Builder|InviteEmail whereStatus($value)
 * @method static Builder|InviteEmail whereUpdatedAt($value)
 * @property mixed $token
 */
class InviteEmail extends Model implements StoreMagicTokenInterface
{
    /**
     * @var int
     */
    public const MAGIC_LINK_TOKEN_LENGTH = 32;

    public const STATUS_PENDING = 1;
    public const STATUS_SENT = 2;
    public const STATUS_CONFIRMED = 3;



    /**
     * @var string
     */
    protected $table = 'invite_emails';

    protected $fillable = ['email'];

    /**
     * @return void
     */
    public function generateMagicLinkToken(): void
    {
        $this->attributes['magic_link_token'] = Str::random(self::MAGIC_LINK_TOKEN_LENGTH);
    }

    public function setStatus(int $status): void
    {
        $this->attributes['status'] = $status;
    }

    /**
     * @param string $email
     * @return Model|InviteEmail|null
     */
    public static function findByEmail(string $email): ?InviteEmail
    {
        return self::query()->where('email', $email)->first();
    }

    /**
     * @param string $token
     *
     * @return InviteEmail|null
     */
    public static function findByMagicLinkToken(string $token): ?InviteEmail
    {
        return self::query()->where('magic_link_token', $token)->first();
    }

    /**
     * @param $token
     */
    public function setTokenAttribute($token)
    {
        $this->attributes['magic_link_token'] = $token;
    }

    /**
     * @return string|null
     */
    public function getTokenAttribute(): ?string
    {
        return $this->magic_link_token;
    }

    /**
     * @return string
     */
    public function getMagicToken(): string
    {
        return $this->token;
    }
}
