<?php
declare(strict_types=1);

namespace App\Http\Requests\Api\Admin;

use App\Http\Requests\ApiRequest;
use App\Models\Admin;
use App\Rules\PermissionRule;

class CreateRequest extends ApiRequest
{
    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            'email' => 'required|email|unique:users,email',
            'first_name' => 'required',
            'last_name' => 'required',
            'role' => ['required', new PermissionRule(new Admin())]
        ];
    }

    /**
     * @return string
     */
    public function getFirstName(): string
    {
        return $this->get('first_name');
    }

    /**
     * @return string
     */
    public function getLastName(): string
    {
        return $this->get('last_name');
    }

    /**
     * @return string
     */
    public function getEmail(): string
    {
        return $this->get('email');
    }

    /**
     * @return int
     */
    public function getRole(): int
    {
        return (int) $this->get('role');
    }
}
