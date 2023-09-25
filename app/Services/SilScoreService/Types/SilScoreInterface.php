<?php
declare(strict_types=1);

namespace App\Services\SilScoreService\Types;

interface SilScoreInterface
{
    /**
     * @return string
     */
    public static function getType(): string;

    /**
     * @return int
     */
    public static function getPoints(): int;
}
