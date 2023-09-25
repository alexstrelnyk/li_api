<?php
declare(strict_types=1);

namespace App\Models\Interfaces;

interface ConsecutiveDaysInterface
{
    public static function getConsecutiveDaysKey(): string;

    public static function getLastSeenKey(): string;
}
