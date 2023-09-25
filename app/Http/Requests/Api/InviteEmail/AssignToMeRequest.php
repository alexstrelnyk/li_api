<?php
declare(strict_types=1);

namespace App\Http\Requests\Api\InviteEmail;

use App\Http\Requests\ApiRequest;

class AssignToMeRequest extends ApiRequest
{
    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            'invite_email_id' => 'required|exists:invite_emails,id'
        ];
    }

    /**
     * @return array
     */
    public function messages(): array
    {
        return [
            'invite_email_id.exists' => 'Invite email not found',
        ];
    }
}
