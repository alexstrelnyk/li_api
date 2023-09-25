<?php
declare(strict_types=1);

namespace App\Services\SilScoreService\Types\Interfaces;

use App\Models\UserReflection;

interface ReflectionSilScore
{
    public function getReflection(): UserReflection;
}
