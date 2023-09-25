<?php
declare(strict_types=1);

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserPolicy extends AbstractPolicy
{
    use HandlesAuthorization;
    
    /**
     * Determine whether the user can view any models.
     *
     * @param User $user
     *
     * @return bool
     */
    public function list(User $user): bool
    {
        return self::roleIn($user, [User::LI_ADMIN, User::CLIENT_ADMIN]);
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param User $user
     * @param User  $model
     *
     * @return bool
     */
    public function view(User $user, User $model): bool
    {
        return self::roleIn($user, User::LI_ADMIN)
            || (self::roleIn($user, User::CLIENT_ADMIN) && self::roleIn($model, User::APP_USER) && $model->client && $model->client_id === $user->client->id);
    }

    /**
     * Determine whether the user can create models.
     *
     * @param User  $user
     *
     * @return bool
     */
    public function create(User $user): bool
    {
        return self::roleIn($user, [User::LI_ADMIN, User::CLIENT_ADMIN]);
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  User $user
     * @param  User  $model
     *
     * @return bool
     */
    public function update(User $user, User $model): bool
    {
        return self::roleIn($user, User::LI_ADMIN)
            || (self::roleIn($user, User::CLIENT_ADMIN) && self::roleIn($model, User::APP_USER) && $model->client && $model->client_id === $user->client->id);
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param User $user
     * @param  User  $model
     *
     * @return bool
     */
    public function delete(User $user, User $model): bool
    {
        return self::roleIn($user, User::LI_ADMIN)
            || (self::roleIn($user, User::CLIENT_ADMIN) && self::roleIn($model, User::APP_USER) && $model->client && $model->client_id === $user->client->id);
    }

    /**
     * @param User $user
     *
     * @return bool
     */
    public function appAuth(User $user): bool
    {
        return self::roleIn($user, [User::APP_USER]);
    }
}
