<?php
declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\ContentItemUserProgress;
use App\Models\UserReflection;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Builder;

class AssignDefaultProgramToUserReflectionsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'clear:user-reflections';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle(): int
    {
        UserReflection::whereHas('user', static function (Builder $builder) {
            $builder->whereHas('client', static function (Builder $builder) {
                $builder->whereHas('programs');
            });
        })->whereHas('contentItem')->each(static function (UserReflection $reflection) {
            $user = $reflection->user;
            $client = $user->client;
            $program = $client->activeProgram;

            $progress = ContentItemUserProgress::ofUserAndItemAndProgram($user, $reflection->contentItem, $program)->first();

            if ($progress instanceof ContentItemUserProgress) {
                $reflection->program()->associate($program);
                $reflection->userProgress()->associate($progress);
                $reflection->save();
            } else {
                $reflection->delete();
            }
        });

        UserReflection::where('program_id', 0)
            ->orWhereNull('program_id')
            ->orWhere('user_progress_id', 0)
            ->orWhereNull('user_progress_id')
            ->delete();

        return 0;
    }
}
