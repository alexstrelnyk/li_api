<?php
declare(strict_types=1);

namespace App\Manager;

use App\Models\User;

class OnBoardingManager
{
    /**
     * @var UserManager
     */
    private $userManager;

    /**
     * OnBoardingManager constructor.
     *
     * @param UserManager $userManager
     */
    public function __construct(UserManager $userManager)
    {
        $this->userManager = $userManager;
    }

    /**
     * @param User $user
     */
    public function complete(User $user): void
    {
        $this->userManager->onBoarded($user);
    }
}
