<?php
declare(strict_types=1);

namespace App\Manager;

use App\Factory\FeedbackFactory;
use App\Mail\FeedbackEmail;
use App\Models\Feedback;
use App\Models\User;
use Illuminate\Contracts\Mail\Mailer;

class FeedbackManager
{
    /**
     * @var FeedbackFactory
     */
    private $feedbackFactory;

    /**
     * @var Mailer
     */
    private $mailer;

    /**
     * FeedbackManager constructor.
     *
     * @param FeedbackFactory $feedbackFactory
     * @param Mailer $mailer
     */
    public function __construct(FeedbackFactory $feedbackFactory, Mailer $mailer)
    {
        $this->feedbackFactory = $feedbackFactory;
        $this->mailer = $mailer;
    }

    /**
     * @param string $text
     *
     * @return Feedback
     */
    public function createFeedback(User $user, string $text): Feedback
    {
        $feedback = $this->feedbackFactory->create($user, $text);

        $feedback->save();

        $this->sendFeedbackToSupport($feedback);

        return $feedback;
    }

    /**
     * @param Feedback $feedback
     */
    public function sendFeedbackToSupport(Feedback $feedback): void
    {
        $this->mailer->send(new FeedbackEmail($feedback));
    }
}
