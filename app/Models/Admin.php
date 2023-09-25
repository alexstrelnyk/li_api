<?php
declare(strict_types=1);

namespace App\Models;

use App\Models\Interfaces\ModelPermissionInterface;
use App\Models\Traits\PermissionTrait;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Admin
 *
 * @property-write mixed $permission
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Admin newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Admin newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Admin query()
 * @mixin \Eloquent
 */
class Admin extends Model implements ModelPermissionInterface
{
    use PermissionTrait;

    /**
     * @return array
     */
    public static function getAvailablePermissions(): array
    {
        return [
            self::LI_ADMIN,
            self::LI_CONTENT_EDITOR
        ];
    }
}
