<?php
declare(strict_types=1);

namespace App\Policies;

use App\Models\FocusArea;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class FocusAreaPolicy extends AbstractPolicy
{
    use HandlesAuthorization;

    /**
     * @param User $user
     *
     * @return bool
     */
    public function list(User $user): bool
    {
        return self::roleIn($user, User::LI_ADMIN);
    }

    /**
     * @param User $user
     * @return bool
     */
    public function create(User $user): bool
    {
        return self::roleIn($user, User::LI_ADMIN);
    }

    /**
     * @param User $user
     * @param FocusArea $focusArea
     *
     * @return bool
     */
    public function show(User $user, FocusArea $focusArea): bool
    {
        return self::roleIn($user, User::LI_ADMIN);
    }

    /**
     * @param User $user
     * @param FocusArea $focusArea
     *
     * @return bool
     */
    public function edit(User $user, FocusArea $focusArea): bool
    {
        return self::roleIn($user, User::LI_ADMIN);
    }

    /**
     * @param User $user
     * @param FocusArea $focusArea
     *
     * @return bool
     */
    public function delete(User $user, FocusArea $focusArea): bool
    {
        return self::roleIn($user, User::LI_ADMIN);
    }
}
