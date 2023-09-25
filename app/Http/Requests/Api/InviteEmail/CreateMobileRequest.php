<?php
declare(strict_types=1);

namespace App\Http\Requests\Api\InviteEmail;

use App\Http\Requests\ApiRequest;

class CreateMobileRequest extends ApiRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'email' => 'required|email|unique:invite_emails,email'
        ];
    }
}
