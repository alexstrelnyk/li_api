<?php
declare(strict_types=1);

namespace App\Services\ConsecutiveDaysService;

use App\Models\User;
use Illuminate\Support\Carbon;

class ConsecutiveDaysService implements ConsecutiveDaysServiceInterface
{
    /**
     * @param User $user
     *
     * @return int|null
     */
    public function getConsecutiveDays(User $user): ?int
    {
        return $user->getSequenceDays();
    }

    /**
     * @param User $user
     * @param int|null $daysCount
     *
     * @return User
     */
    public function addConsecutiveDays(User $user, ?int $daysCount = 1): User
    {
        $user->setSequenceDays($user->getSequenceDays() + $daysCount);

        return $user;
    }

    /**
     * @param User $user
     *
     * @return User
     */
    public function reset(User $user): User
    {
        $user->setSequenceDays(1);

        return $user;
    }

    /**
     * @param User $user
     *
     * @return User
     */
    public function processConsecutiveDays(User $user): User
    {
        if ($this->checkDiff($user) === 1) {
            $this->addConsecutiveDays($user);
        } else {
            $this->reset($user);
        }

        $user->setLastSeen(Carbon::now());
        $user->update();

        return $user;
    }

    /**
     * @param User $user
     *
     * @return int
     */
    public function checkDiff(User $user): int
    {
        $now = Carbon::now();
        $diff = 0;

        if ($user->getLastSeen()) {
            $diff = $user->getLastSeen()->startOfDay()->diffInDays($now->startOfDay());
        }

        return $diff;
    }
}
