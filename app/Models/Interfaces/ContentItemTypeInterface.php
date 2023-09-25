<?php
declare(strict_types=1);

namespace App\Models\Interfaces;

interface ContentItemTypeInterface
{
    /**
     * Available types
     */
    public const ARTICLE_CONTENT_TYPE = 1;
    public const AUDIO_CONTENT_TYPE = 2;
    public const VIDEO_CONTENT_TYPE = 3;
    public const TIP_CONTENT_TYPE = 4;

    /**
     * @param int $type
     */
    public function setType(int $type): void;

    /**
     * @return array
     */
    public static function getAvailableTypes(): array;
}
