<?php
declare(strict_types=1);

namespace App\Models\Interfaces;
use Illuminate\Support\Carbon;

/**
 * Interface SilScoreInterface
 * @package App\Models\Interfaces
 */
interface SilScoreInterface
{

    /**
     * Sil score calculating variants
     */
    public const SESSION_COMPLETED_SIL_SCORE = 2;
    public const EVENT_SCHEDULED_SIL_SCORE = 4;
    public const FOCUS_EXPLORED_SIL_SCORE = 4;
    public const REFLECTION_COMPLETED_SIL_SCORE = 3;
    public const REFLECTION_SKIPPED_SIL_SCORE = 2;
    public const REFLECTION_NOT_SKIPPED_SIL_SCORE = 2;
    public const ACCESS_CONTENT_ALL_FOCUS_AREAS_BONUS_SIL_SCORE = 10;
    public const CONSECUTIVE_DAYS_Of_USE_BONUS_SIL_SCORE = 5;


    /**
     * @param Carbon $date
     */
    public function setLastSeen(Carbon $date): void;

    /**
     * @return Carbon|null
     */
    public function getLastSeen(): ?Carbon;

    /**
     * @param int $daysCount
     */
    public function setSequenceDays(int $daysCount): void;

    /**
     * @return int|null
     */
    public function getSequenceDays(): ?int;
}