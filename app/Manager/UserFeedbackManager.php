<?php
declare(strict_types=1);

namespace App\Manager;

use App\Factory\UserFeedbackFactory;
use App\Models\ContentItem;
use App\Models\User;
use App\Models\UserFeedback;
use Auth;

class UserFeedbackManager
{
    /**
     * @var UserFeedbackFactory
     */
    private $userFeedbackFactory;

    /**
     * UserFeedbackManager constructor.
     * @param UserFeedbackFactory $userFeedbackFactory
     */
    public function __construct(UserFeedbackFactory $userFeedbackFactory)
    {
        $this->userFeedbackFactory = $userFeedbackFactory;
    }

    /**
     * @param ContentItem $contentItem
     * @param int $reaction
     * @param string|null $response
     *
     * @param User|null $user
     *
     * @return UserFeedback
     */
    public function createFeedback(ContentItem $contentItem, bool $reaction, ?string $response = null, ?User $user = null): UserFeedback
    {
        $feedback = $this->userFeedbackFactory->create($user ?? Auth::user(), $contentItem, $reaction);

        if ($response) {
            $feedback->response = $response;
        }

        $feedback->save();

        return $feedback;
    }

    /**
     * @param UserFeedback $feedback
     * @param bool $reaction
     * @param string|null $response
     *
     * @return UserFeedback
     */
    public function update(UserFeedback $feedback, bool $reaction, ?string $response = null): UserFeedback
    {
        $feedback->reaction = $reaction;

        if (!$reaction && $response) {
            $feedback->response = $response;
        }

        $feedback->save();

        return $feedback;
    }
}
