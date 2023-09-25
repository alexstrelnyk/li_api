<?php
declare(strict_types=1);

namespace App\Manager;

use App\Factory\UserFactory;
use App\Models\InviteEmail;
use App\Models\User;
use App\Services\MagicLinkTokenGenerator\MagicLinkTokenGeneratorInterface;
use Exception;

class InviteEmailManager implements ManagerInterface
{
    protected static $model = InviteEmail::class;

    /**
     * @var MagicLinkTokenGeneratorInterface
     */
    private $magicLinkTokenGenerator;

    /**
     * @var UserManager
     */
    private $userManager;

    /**
     * InviteEmailManager constructor.
     *
     * @param MagicLinkTokenGeneratorInterface $magicLinkTokenGenerator
     * @param UserManager $userManager
     */
    public function __construct(MagicLinkTokenGeneratorInterface $magicLinkTokenGenerator, UserManager $userManager)
    {
        $this->magicLinkTokenGenerator = $magicLinkTokenGenerator;
        $this->userManager = $userManager;
    }

    /**
     * @param InviteEmail $inviteEmail
     *
     * @throws Exception
     */
    public function delete(InviteEmail $inviteEmail): void
    {
        $inviteEmail->delete();
    }

    /**
     * @return InviteEmail
     */
    public static function getModel(): InviteEmail
    {
        return new self::$model();
    }

    /**
     * @param InviteEmail $inviteEmail
     */
    public function generateMagicToken(InviteEmail $inviteEmail): void
    {
        $inviteEmail->token = $this->magicLinkTokenGenerator->generate();
    }

    /**
     * @param InviteEmail $inviteEmail
     *
     * @return User
     */
    public function createUserFromInviteEmail(InviteEmail $inviteEmail): User
    {
        return $this->userManager->create($inviteEmail->email, '', '', '', '', '', User::APP_USER);
    }
}
