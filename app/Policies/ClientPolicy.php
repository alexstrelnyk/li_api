<?php
declare(strict_types=1);

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ClientPolicy extends AbstractPolicy
{
    use HandlesAuthorization;
    
    /**
     * Determine whether the user can view any clients.
     *
     * @param User $user
     *
     * @return mixed
     */
    public function list(User $user): bool
    {
        return self::roleIn($user, User::LI_ADMIN);
    }

    /**
     * Determine whether the user can view the client.
     *
     * @param User $user
     *
     * @return mixed
     */
    public function view(User $user): bool
    {
        return self::roleIn($user, User::LI_ADMIN);
    }

    /**
     * Determine whether the user can create clients.
     *
     * @param User  $user
     *
     * @return mixed
     */
    public function create(User $user): bool
    {
        return self::roleIn($user, User::LI_ADMIN);
    }

    /**
     * Determine whether the user can update the client.
     *
     * @param User $user
     *
     * @return mixed
     */
    public function update(User $user): bool
    {
        return self::roleIn($user, User::LI_ADMIN);
    }

    /**
     * Determine whether the user can delete the client.
     *
     * @param User $user
     *
     * @return mixed
     */
    public function delete(User $user): bool
    {
        return self::roleIn($user, User::LI_ADMIN);
    }
}
