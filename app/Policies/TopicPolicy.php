<?php
declare(strict_types=1);

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class TopicPolicy extends AbstractPolicy
{
    use HandlesAuthorization;

    /**
     * @return bool
     */
    public function list(): bool
    {
        return true;
    }

    /**
     * @param User $user
     *
     * @return bool
     */
    public function create(User $user): bool
    {
        return self::roleIn($user, [User::LI_ADMIN, User::LI_CONTENT_EDITOR, User::CLIENT_ADMIN]);
    }

    /**
     * @param User $user
     *
     * @return bool
     */
    public function show(User $user): bool
    {
        return self::roleIn($user, [User::LI_ADMIN, User::LI_CONTENT_EDITOR, User::CLIENT_ADMIN]);
    }

    /**
     * @param User $user
     *
     * @return bool
     */
    public function edit(User $user): bool
    {
        return self::roleIn($user, [User::LI_ADMIN, User::LI_CONTENT_EDITOR, User::CLIENT_ADMIN]);
    }

    /**
     * @param User $user
     *
     * @return bool
     */
    public function delete(User $user): bool
    {
        return self::roleIn($user, [User::LI_ADMIN, User::LI_CONTENT_EDITOR, User::CLIENT_ADMIN]);
    }

}
