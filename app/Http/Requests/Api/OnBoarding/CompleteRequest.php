<?php
declare(strict_types=1);

namespace App\Http\Requests\Api\OnBoarding;

use App\Http\Requests\ApiRequest;

class CompleteRequest extends ApiRequest
{
    /**
     * @return string|null
     */
    public function getReflection(): ?string
    {
        return $this->get('reflection');
    }
}
