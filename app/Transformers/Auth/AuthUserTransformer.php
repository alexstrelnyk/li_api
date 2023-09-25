<?php

namespace App\Transformers\Auth;

use App\Models\User;
use App\Transformers\AbstractTransformer;

/**
 * Class AuthUserTransformer
 * @package App\Transformers\Auth
 */
class AuthUserTransformer extends AbstractTransformer implements AuthUserTransformerInterface
{

    /**
     * @param User $user
     * @return array
     */
    public function transform(User $user) : array
    {
        return [
            'id' => $user->id,
            'name' => $user->name,
            'first_name' => $user->first_name,
            'last_name' => $user->last_name,
            'email' => $user->email,
            'client_id' => $user->client_id,
            'job_role' => $user->job_role,
            'job_dept' => $user->job_dept,
            'created_at' => $this->date($user->created_at),
            'updated_at' => $this->date($user->updated_at),
            'token' => $user->token,
            'role' => $user->permission,
            'avatar' => $user->avatar
        ];
    }
}