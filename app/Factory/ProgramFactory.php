<?php
declare(strict_types=1);

namespace App\Factory;

use App\Models\Client;
use App\Models\Program;

class ProgramFactory
{
    /**
     * @param Client $client
     *
     * @return Program
     */
    public function create(Client $client): Program
    {
        $program = new Program();
        $program->client()->associate($client);

        return $program;
    }
}
