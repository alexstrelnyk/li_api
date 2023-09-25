<?php
declare(strict_types=1);

namespace App\Http\Requests\Api\AboutMe;

use App\Http\Requests\ApiRequest;

class UpdateRequest extends ApiRequest
{
    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            'job_dept' => 'required',
            'job_role' => 'required',
            'phone' => 'required'
        ];
    }
}