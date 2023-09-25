<?php
declare(strict_types=1);

namespace App\Transformers\InviteEmail;

use App\Models\InviteEmail;
use App\Transformers\TransformerInterface;

interface InviteEmailTransformerInterface extends TransformerInterface
{
    /**
     * @param InviteEmail $inviteEmail
     *
     * @return array
     */
    public function transform(InviteEmail $inviteEmail): array;
}