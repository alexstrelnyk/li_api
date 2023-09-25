<?php
declare(strict_types=1);

namespace App\Policies;

use App\Models\User;
use App\Models\InviteEmail;
use Illuminate\Auth\Access\HandlesAuthorization;

class InviteEmailPolicy extends AbstractPolicy
{
    use HandlesAuthorization;

    /**
     * @param User $user
     * @param InviteEmail $inviteEmail
     *
     * @return bool
     */
    public function assign(User $user, InviteEmail $inviteEmail): bool
    {
        return self::roleIn($user, User::LI_ADMIN);
    }

    /**
     * @param User $user
     * @param InviteEmail $inviteEmail
     *
     * @return bool
     */
    public function assignToMe(User $user, InviteEmail $inviteEmail): bool
    {
        return self::roleIn($user, User::CLIENT_ADMIN);
    }

    /**
     * @param User $user
     *
     * @return bool
     */
    public function viewAny(User $user): bool
    {
        return self::roleIn($user, [User::LI_ADMIN, User::CLIENT_ADMIN]);
    }
}
