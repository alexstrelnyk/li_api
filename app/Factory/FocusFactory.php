<?php
declare(strict_types=1);

namespace App\Factory;

use App\Models\Focus;

class FocusFactory
{
    /**
     * @return Focus
     */
    public function create(): Focus
    {
        return new Focus();
    }
}
