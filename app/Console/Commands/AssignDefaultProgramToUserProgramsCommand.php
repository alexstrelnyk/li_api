<?php

namespace App\Console\Commands;

use App\Models\ContentItemUserProgress;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Builder;

class AssignDefaultProgramToUserProgramsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'clear:user-progresses';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Assign default program to user progress';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        ContentItemUserProgress::whereHas('user', static function (Builder $builder) {
            $builder->whereHas('client', static function (Builder $builder) {
                $builder->whereHas('programs');
            });
        })->each(static function (ContentItemUserProgress $progress) {
            $user = $progress->user;
            $client = $user->client;
            $program = $client->activeProgram;

            $progress->program()->associate($program);
            $progress->save();
        });

        ContentItemUserProgress::where('program_id', 0)->orWhereNull('program_id')->delete();

        return 0;
    }
}
