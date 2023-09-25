<?php
declare(strict_types=1);

namespace App\Transformers\InviteEmail;

use App\Models\InviteEmail;
use App\Transformers\AbstractTransformer;

class InviteEmailTransformer extends AbstractTransformer implements InviteEmailTransformerInterface
{
    public function transform(InviteEmail $inviteEmail): array
    {
        return [
            'id' => $inviteEmail->id,
            'email' => $inviteEmail->email,
            'status' => $inviteEmail->status,
            'magic_link_token' => $inviteEmail->magic_link_token,
            'created_at' => $this->date($inviteEmail->created_at),
            'updated_at' => $this->date($inviteEmail->updated_at),
        ];
    }
}
