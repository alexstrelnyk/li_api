<?php
declare(strict_types=1);

namespace App\Console\Commands\Clear;

use App\Models\UserReflection;
use Illuminate\Console\Command;

class ClearReflectionsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'clear:reflections';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete not valid Reflections';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        UserReflection::all()->each(function (UserReflection $userReflection) {
            if ($userReflection->contentItem === null || $userReflection->user) {
                $this->output->writeln('Deleting reflection '.$userReflection->id);
                $userReflection->delete();
            }
        });
    }
}
