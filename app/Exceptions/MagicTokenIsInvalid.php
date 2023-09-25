<?php
declare(strict_types=1);

namespace App\Exceptions;

use Exception;

class MagicTokenIsInvalid extends Exception
{
    protected $message = 'The provided magic token is invalid';
}
