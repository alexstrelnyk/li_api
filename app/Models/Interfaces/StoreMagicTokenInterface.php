<?php
declare(strict_types=1);

namespace App\Models\Interfaces;

interface StoreMagicTokenInterface
{
    /**
     * @return string
     */
    public function getMagicToken(): string;
}
