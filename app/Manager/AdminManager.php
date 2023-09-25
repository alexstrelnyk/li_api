<?php
declare(strict_types=1);

namespace App\Manager;

use App\Factory\UserFactory;
use App\Models\User;

class AdminManager
{
    /**
     * @var UserFactory
     */
    private $userFactory;

    /**
     * @var UserManager
     */
    private $userManager;

    /**
     * AdminManager constructor.
     *
     * @param UserManager $userManager
     * @param UserFactory $userFactory
     */
    public function __construct(UserManager $userManager, UserFactory $userFactory)
    {
        $this->userFactory = $userFactory;
        $this->userManager = $userManager;
    }

    /**
     * @param string $email
     * @param string $firstName
     * @param string $lastName
     * @param int $permission
     *
     * @return User
     */
    public function create(string $email, string $firstName, string $lastName, int $permission): User
    {
        $user = $this->userFactory->createUser($email, $permission, $firstName, $lastName);
        $this->userManager->generateToken($user);
        $user->save();

        return $user;
    }
    /**
     * @param User $user
     * @param array $data
     */
    public function update(User $user, array $data): void
    {
        $user->fill($data);
        $user->save();
    }

    /**
     * @param User $user
     *
     * @return bool
     * @throws
     */
    public function delete(User $user): bool
    {
        return $this->userManager->delete($user);
    }
}
