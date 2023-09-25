<?php
declare(strict_types=1);

namespace App\Policies;

use App\Models\Client;
use App\Models\User;
use App\Models\Focus;
use Illuminate\Auth\Access\HandlesAuthorization;

class FocusPolicy extends AbstractPolicy
{
    use HandlesAuthorization;
    
    /**
     * Determine whether the user can view any foci.
     *
     * @param User $user
     *
     * @return mixed
     */
    public function list(User $user)
    {
        return ($user->permission === User::APP_USER && $user->client) || self::roleIn($user, [User::LI_ADMIN, User::CLIENT_ADMIN]);
    }

    /**
     * Determine whether the user can view the focus.
     *
     * @param User $user
     * @param Focus $focus
     *
     * @return bool
     */
    public function view(User $user, Focus $focus): bool
    {
        return !self::roleIn($user, User::LI_CONTENT_EDITOR) && self::canRead($user, $focus);
    }

    /**
     * Determine whether the user can create focus.
     *
     * @param User  $user
     *
     * @return mixed
     */
    public function create(User $user): bool
    {
        return self::roleIn($user, User::LI_ADMIN)
            || (self::roleIn($user, User::CLIENT_ADMIN)
                && $user->client
                && self::contentModelIn($user, [Client::BLANK, Client::MIXED_CONTENT]));
    }

    /**
     * Determine whether the user can update the focus.
     *
     * @param  User  $user
     * @param Focus $focus
     *
     * @return mixed
     */
    public function update(User $user, Focus $focus): bool
    {
        return (self::ownedByHisClient($user, $focus) && self::roleIn($user, User::CLIENT_ADMIN))
            || (self::ownedByLi($focus) && self::roleIn($user, User::LI_ADMIN));
    }

    /**
     * Determine whether the user can delete the focus.
     *
     * @param  User  $user
     * @param Focus $focus
     *
     * @return mixed
     */
//    public function delete(User $user, Focus $focus): bool
//    {
//        return (self::ownedByHisClient($user, $focus) && self::roleIn($user, User::CLIENT_ADMIN))
//            || (self::ownedByLi($focus) && self::roleIn($user, User::LI_ADMIN));
//    }

    public function delete(User $user, Focus $focus): bool
    {
        return self::roleIn($user, User::LI_ADMIN);
    }
}
