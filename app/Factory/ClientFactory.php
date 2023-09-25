<?php
declare(strict_types=1);

namespace App\Factory;

use App\Models\Client;

class ClientFactory
{
    /**
     * @return Client
     */
    public function create(): Client
    {
        return new Client();
    }
}
