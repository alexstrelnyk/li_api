<?php
declare(strict_types=1);

namespace App\Factory;

use App\Models\Client;
use App\Models\User;
use App\Services\MagicLinkTokenGenerator\MagicLinkTokenGeneratorInterface;

class UserFactory
{
    /**
     * @var MagicLinkTokenGeneratorInterface
     */
    private $magicLinkTokenGenerator;

    /**
     * UserFactory constructor.
     *
     * @param MagicLinkTokenGeneratorInterface $magicLinkTokenGenerator
     */
    public function __construct(MagicLinkTokenGeneratorInterface $magicLinkTokenGenerator)
    {
        $this->magicLinkTokenGenerator = $magicLinkTokenGenerator;
    }

    /**
     * @param string $email
     * @param string $firstName
     * @param string $lastName
     *
     * @param Client|null $client
     *
     * @return User
     */
    public function create(string $email, string $firstName = '', string $lastName = '', ?Client $client = null): User
    {
        return new User(['email' => $email, 'first_name' => $firstName, 'last_name' => $lastName, 'client_id' => $client->id ?? null]);
    }

    /**
     * @param string $email
     * @param string $firstName
     * @param string $lastName
     *
     * @return User
     */
    public function createAppUser(string $email, string $firstName = '', string $lastName = ''): User
    {
        $user = $this->create($email, $firstName, $lastName);
        $user->permission = User::APP_USER;

        return $user;
    }

    /**
     * @param string $email
     * @param int $permission
     *
     * @param string $firstName
     * @param string $lastName
     *
     * @return User
     */
    public function createUser(string $email, int $permission, string $firstName = '', string $lastName = ''): User
    {
        $user = $this->create($email, $firstName, $lastName);
        $user->permission = $permission;

        return $user;
    }

    /**
     * @param string $email
     * @param string $firstName
     * @param string $lastName
     *
     * @return User
     */
    public function createAppUserWithMagicLinkToken(string $email, string $firstName = '', string $lastName = ''): User
    {
        $user = $this->createAppUser($email, $firstName, $lastName);
        $user->magic_token = $this->magicLinkTokenGenerator->generate();

        return $user;
    }
}
