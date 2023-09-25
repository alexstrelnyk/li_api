<?php
declare(strict_types=1);

namespace App\Transformers\Admin;

use App\Models\User;
use App\Transformers\AbstractTransformer;
use App\Transformers\TransformerInterface;
use Exception;

class AdminTransformer extends AbstractTransformer implements TransformerInterface
{
    /**
     * @param User $user
     *
     * @return array
     * @throws Exception
     */
    public function transform(User $user): array
    {
        return [
            'id' => $user->id,
            'email' => $user->email,
            'first_name' => $user->first_name,
            'last_name' => $user->last_name,
            'role' => $user->permission,
            'role_title' => $user->getPermissionTitle(),
            'created_at' => $this->date($user->created_at)
        ];
    }
}
