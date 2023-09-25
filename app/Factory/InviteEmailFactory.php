<?php
declare(strict_types=1);

namespace App\Factory;

use App\Models\InviteEmail;
use App\Services\MagicLinkTokenGenerator\MagicLinkTokenGeneratorInterface;

class InviteEmailFactory
{
    /**
     * @var MagicLinkTokenGeneratorInterface
     */
    private $magicLinkTokenGenerator;

    /**
     * InviteEmailFactory constructor.
     *
     * @param MagicLinkTokenGeneratorInterface $magicLinkTokenGenerator
     */
    public function __construct(MagicLinkTokenGeneratorInterface $magicLinkTokenGenerator)
    {
        $this->magicLinkTokenGenerator = $magicLinkTokenGenerator;
    }

    /**
     * @param string $email
     *
     * @return InviteEmail
     */
    public function create(string $email): InviteEmail
    {
        $inviteEmail = new InviteEmail(['email' => $email]);
        $inviteEmail->token = $this->magicLinkTokenGenerator->generate();
        $inviteEmail->status = InviteEmail::STATUS_PENDING;
        return $inviteEmail;
    }
}
