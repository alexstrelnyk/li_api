<?php
declare(strict_types=1);

namespace App\Services\ConsecutiveDaysService;

use App\Models\User;

interface ConsecutiveDaysServiceInterface
{
    /**
     * @param User $user
     *
     * @return int|null
     */
    public function getConsecutiveDays(User $user): ?int;
}
