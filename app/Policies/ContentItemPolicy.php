<?php
declare(strict_types=1);

namespace App\Policies;

use App\Models\User;
use App\Models\ContentItem;
use Illuminate\Auth\Access\HandlesAuthorization;

class ContentItemPolicy extends AbstractPolicy
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
     * Determine whether the user can view the content item.
     *
     * @param User $user
     * @param ContentItem $contentItem
     *
     * @return mixed
     */
    public function view(User $user, ContentItem $contentItem): bool
    {
        /*
         * It that content created by not a client or that content owned by his client
         */
        return true;
    }

    /**
     * Determine whether the user can create content items.
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
     * Determine whether the user can update the content item.
     *
     * @param  User  $user
     * @param ContentItem $contentItem
     *
     * @return mixed
     */
    public function update(User $user, ContentItem $contentItem): bool
    {
        return self::roleIn($user, User::LI_ADMIN);
    }

    /**
     * Determine whether the user can delete the content item.
     *
     * @param  User  $user
     * @param ContentItem $contentItem
     *
     * @return mixed
     */
    public function delete(User $user, ContentItem $contentItem): bool
    {
        return self::roleIn($user, User::LI_ADMIN);
    }

    /**
     * @param User $user
     * @param ContentItem $contentItem
     *
     * @return bool
     */
    public function complete(User $user, ContentItem $contentItem): bool
    {
        return self::roleIn($user, User::APP_USER);
    }

    /**
     * @param User $user
     * @param ContentItem $contentItem
     *
     * @return bool
     */
    public function viewed(User $user, ContentItem $contentItem): bool
    {
        return self::roleIn($user, User::APP_USER);
    }

    /**
     * @param User $user
     * @param ContentItem $contentItem
     *
     * @return bool
     */
    protected function editDelete(User $user, ContentItem $contentItem): bool
    {
        return ($contentItem->createdBy->client_id !== null && self::roleIn($user, User::CLIENT_ADMIN))
            || ($contentItem->createdBy->client_id === null && self::roleIn($user, User::$liRoles));
    }
}
