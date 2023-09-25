<?php
declare(strict_types=1);

namespace App\Transformers\AboutMe;

use App\Models\ContentItemUserProgress;
use App\Models\User;
use App\Services\SilScoreService\SilScoreService;
use App\Transformers\AbstractTransformer;
use Storage;

class AboutMeTransformer extends AbstractTransformer implements AboutMeTransformerInterface
{

    /**
     * @var SilScoreService
     */
    private $silScoreActivityService;

    /**
     * SilScoreTransformer constructor.
     *
     * @param SilScoreService $silScoreActivityService
     */
    public function __construct(SilScoreService $silScoreActivityService)
    {
        $this->silScoreActivityService = $silScoreActivityService;
    }


    /**
     * @param User $user
     * @return array
     */
    public function transform(User $user): array
    {
        $silScoreProgress = $this->silScoreActivityService->getSilScoreProgress($user);

        $sessionsCompletedCount = ContentItemUserProgress::where('completed', true)
            ->where('user_id', $user->id)->count();

        return [
            'image' =>  $user->avatar ? Storage::disk('avatars')->url($user->avatar) : null,
            'first_name' => $user->first_name,
            'last_name' => $user->last_name,
            'sil_score' => $user->sil_score,
            'sil_score_progress' => $silScoreProgress,
            'sessions_completed_count' => $sessionsCompletedCount,
            'events_scheduled_count' => $user->scheduledEvents()->count(),
            'reflections_written_count' => $user->reflections()->count(),
            'department' => $user->job_dept,
            'role' => $user->job_role,
            'email' => $user->email,
            'phone' => $user->phone,
        ];
    }
}