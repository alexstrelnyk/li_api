<?php
declare(strict_types=1);

namespace App\Http\Requests\Api\Admin;

use App\Models\Admin;
use App\Rules\PermissionRule;
use Illuminate\Validation\Rule;

class UpdateRequest extends CreateRequest
{
    /**
     * @return array
     */
    public function rules(): array
    {
        $admin = $this->route('admin');

        return [
            'email' => ['required', 'email', Rule::unique('users', 'email')->ignore($admin->id),],
            'first_name' => 'required',
            'last_name' => 'required',
            'role' => ['required', new PermissionRule(new Admin())]
        ];
    }
}
