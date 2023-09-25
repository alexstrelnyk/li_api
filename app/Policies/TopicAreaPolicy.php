<?php
declare(strict_types=1);

namespace App\Policies;

use App\Models\FocusAreaTopic;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class TopicAreaPolicy extends AbstractPolicy
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
     *
     * @return bool
     */
    public function create(User $user): bool
    {
        return self::roleIn($user, User::LI_ADMIN);
    }

    /**
     * @param User $user
     * @param FocusAreaTopic $topicArea
     *
     * @return bool
     */
    public function show(User $user, FocusAreaTopic $topicArea): bool
    {
        return self::roleIn($user, User::LI_ADMIN);
    }

    /**
     * @param User $user
     * @param FocusAreaTopic $topicArea
     *
     * @return bool
     */
    public function edit(User $user, FocusAreaTopic $topicArea): bool
    {
        return self::roleIn($user, User::LI_ADMIN);
    }

    /**
     * @param User $user
     * @param FocusAreaTopic $topicArea
     *
     * @return bool
     */
    public function delete(User $user, FocusAreaTopic $topicArea): bool
    {
        return self::roleIn($user, User::LI_ADMIN);
    }
}
