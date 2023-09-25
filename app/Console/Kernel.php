<?php
declare(strict_types=1);

namespace App\Console;

use App\Console\Commands\AssignDefaultProgramToUserProgramsCommand;
use App\Console\Commands\AssignDefaultProgramToUserReflectionsCommand;
use App\Console\Commands\ChangeUserPasswordCommand;
use App\Console\Commands\Debug\IsSecureCommand;
use App\Console\Commands\EventsScheduledCommand;
use App\Console\Commands\MigrateFilesystemCommand;
use App\Console\Commands\MigrationDBCommand;
use App\Console\Commands\RewriteOnboardingReflectionsCommand;
use App\Console\Commands\Send\LastCompletedCoachingSessionCommand;
use App\Console\Commands\Send\SendConsecutiveDays4EngangedCommand;
use App\Console\Commands\Send\SendPushNotificationCommand;
use App\Console\Commands\Clear\ClearActivities;
use App\Console\Commands\Clear\ClearContentItemsCommand;
use App\Console\Commands\Clear\ClearProgramsCommand;
use App\Console\Commands\Clear\ClearReflectionsCommand;
use App\Console\Commands\Clear\ClearScheduledEvents;
use App\Console\Commands\Clear\ClearTopicAreasContentItems;
use App\Console\Commands\Clear\ClearUsersCommand;
use App\Console\Commands\CreateActivity;
use App\Console\Commands\Debug\DebugDevicesCommand;
use App\Console\Commands\Debug\DebugSASTokenCommand;
use App\Console\Commands\Debug\DebugScheduledEvents;
use App\Console\Commands\RegisterDevicesCommand;
use App\Console\Commands\Send\SendRandomTipsCommand;
use App\Console\Commands\ShowFilesystemCommand;
use App\Console\Commands\TestPush;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        CreateActivity::class,
        TestPush::class,
        SendPushNotificationCommand::class,
        ChangeUserPasswordCommand::class,
        ClearProgramsCommand::class,
        DebugDevicesCommand::class,
        RegisterDevicesCommand::class,
        ClearActivities::class,
        ClearContentItemsCommand::class,
        ClearReflectionsCommand::class,
        DebugSASTokenCommand::class,
        SendRandomTipsCommand::class,
        ClearScheduledEvents::class,
        ClearUsersCommand::class,
        ClearTopicAreasContentItems::class,
        DebugScheduledEvents::class,
        SendConsecutiveDays4EngangedCommand::class,
        MigrationDBCommand::class,
        EventsScheduledCommand::class,
        RewriteOnboardingReflectionsCommand::class,
        AssignDefaultProgramToUserProgramsCommand::class,
        AssignDefaultProgramToUserReflectionsCommand::class,
        IsSecureCommand::class,
        MigrateFilesystemCommand::class,
        ShowFilesystemCommand::class,
        LastCompletedCoachingSessionCommand::class
    ];

    /**
     * Define the application's command schedule.
     *
     * @param Schedule $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule): void
    {
        $schedule->command(SendPushNotificationCommand::class)->everyMinute()->sendOutputTo(storage_path('logs/push/events.log'));
        $schedule->command(SendRandomTipsCommand::class)->everyMinute()->sendOutputTo(storage_path('logs/push/tips.log'));
        $schedule->command(SendConsecutiveDays4EngangedCommand::class)->everyMinute()->sendOutputTo(storage_path('logs/push/consecutive_days.log'));
        $schedule->command(LastCompletedCoachingSessionCommand::class)->everyMinute()->sendOutputTo(storage_path('logs/push/last_completed_coaching_session.log'));
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
