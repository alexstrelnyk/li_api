<?php
declare(strict_types=1);

namespace App\Http\Requests\Api\User;

use App\Http\Requests\ApiRequest;
use App\Models\User;
use App\Rules\PermissionRule;

class CreateRequest extends ApiRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'first_name' => 'required',
            'last_name' => 'required',
            'job_role' => 'required',
            'job_dept' => 'required',
            'email' => 'required|email|unique:users,email',
            'permission' => ['required', new PermissionRule(new User())],
            'password' => 'required|min:6',
            'client_id' => 'required_if:permission,'.implode(',', [User::CLIENT_ADMIN, User::APP_USER])
        ];
    }
}
