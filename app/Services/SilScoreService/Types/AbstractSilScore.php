<?php
declare(strict_types=1);

namespace App\Services\SilScoreService\Types;

abstract class AbstractSilScore implements SilScoreInterface
{
    /**
     * @return string
     */
    public static function getType(): string
    {
        return static::TYPE;
    }

    /**
     * @return int
     */
    public static function getPoints(): int
    {
        return static::POINTS;
    }
}
