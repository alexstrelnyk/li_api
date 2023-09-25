<?php
declare(strict_types=1);

namespace App\Models\Traits;

use LogicException;

trait PermissionTrait
{
    /**
     * @return array
     */
    abstract public function getAvailablePermissions(): array;

    /**
     * @param int $permission
     */
    public function setPermissionAttribute(int $permission): void
    {
        if (in_array($permission, $this->getAvailablePermissions(), true)) {
            $this->attributes['permission'] = $permission;
        } else {
            throw new LogicException('Status '.$permission.' is wrong. Available statuses ['.implode(',', $this->getAvailablePermissions()).']');
        }
    }
}
