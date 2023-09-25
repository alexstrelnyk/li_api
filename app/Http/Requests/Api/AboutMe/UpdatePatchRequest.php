<?php
declare(strict_types=1);

namespace App\Http\Requests\Api\AboutMe;

use App\Http\Requests\ApiRequest;

class UpdatePatchRequest extends ApiRequest
{
    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            'first_name' => 'string',
            'last_name' => 'string',
            'job_dept' => 'string',
            'job_role' => 'string',
            'phone' => 'string'
        ];
    }
}