<?php
declare(strict_types=1);

namespace App\Http\Requests\Api\InviteEmail;

use App\Http\Requests\ApiRequest;

class SendInviteRequest extends ApiRequest
{
    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            'email' => 'required|email',
        ];
    }

    /**
     * @return string
     */
    public function getEmail(): string
    {
        return $this->get('email');
    }
}
