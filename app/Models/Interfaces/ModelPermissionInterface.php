<?php
declare(strict_types=1);

namespace App\Models\Interfaces;

interface ModelPermissionInterface
{
    public const LI_ADMIN = 1;
    public const LI_CONTENT_EDITOR = 2;
    public const CLIENT_ADMIN = 3;
    public const APP_USER = 4;

    /**
     * @return array
     */
    public static function getAvailablePermissions(): array;
}
