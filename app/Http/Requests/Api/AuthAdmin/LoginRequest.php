<?php

namespace App\Http\Requests\Api\AuthAdmin;

use App\Http\Requests\ApiRequest;

/**
 * Class LoginRequest
 * @package App\Http\Requests\Api\AuthAdmin
 */
class LoginRequest extends ApiRequest
{
    /**
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            'email' => 'required|email',
            'password' => 'required|string'
        ];
    }
}