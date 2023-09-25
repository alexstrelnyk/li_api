<?php
declare(strict_types=1);

namespace App\Factory;

use App\Models\Feedback;
use App\Models\User;

class FeedbackFactory
{
    /**
     * @param User $user
     * @param string $text
     *
     * @return Feedback
     */
    public function create(User $user, string $text): Feedback
    {
        return new Feedback(['user_id' => $user->id, 'text' => $text]);
    }
}
