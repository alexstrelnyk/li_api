<?php
declare(strict_types=1);

namespace App\Manager;

use App\Models\User;
use App\Notifications\LastCompletedCoachingSessionNotification;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;

class NotificationManager
{
    public function sendLastCoachingSessionNotifications(): void
    {
        $notification5 = new LastCompletedCoachingSessionNotification(5);
        $notification15 = new LastCompletedCoachingSessionNotification(15);
        $notification30 = new LastCompletedCoachingSessionNotification(30, 'You haven\'t reflected in a while');

        $users5 = User::whereHas('progresses', static function (Builder $builder) {
            $builder->whereDate('completed_at', '>=', Carbon::now()->subDays(15))
                ->whereDate('completed_at', '<', Carbon::now()->subDays(5));
        })->whereDoesntHave('notifications', static function (Builder $builder) use ($notification5) {
            $builder->where('current_type', $notification5->getType());
        })->get();

        $users15 = User::whereHas('progresses', static function (Builder $builder) {
            $builder->whereDate('completed_at', '>=', Carbon::now()->subDays(30))
                ->whereDate('completed_at', '<', Carbon::now()->subDays(15));
        })->whereDoesntHave('notifications', static function (Builder $builder) use ($notification15) {
            $builder->where('current_type', $notification15->getType());
        })->get();

        $users30 = User::whereHas('progresses', static function (Builder $builder) {
            $builder->whereDate('completed_at', '<', Carbon::now()->subDays(30));
        })->whereDoesntHave('notifications', static function (Builder $builder) use ($notification30) {
            $builder->where('current_type', $notification30->getType());
        })->get();

        $users5->each(static function (User $user) use ($notification5) {
            $user->notify($notification5);
        });

        $users15->each(static function (User $user) use ($notification15) {
            $user->notify($notification15);
        });

        $users30->each(static function (User $user) use ($notification30) {
            $user->notify($notification30);
        });
    }
}
