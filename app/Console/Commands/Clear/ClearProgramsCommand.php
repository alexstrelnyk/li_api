<?php

namespace App\Console\Commands\Clear;

use App\Models\Program;
use Illuminate\Console\Command;

class ClearProgramsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'clear:programs';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete not valid Programs';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        Program::all()->each(function (Program $program) {
            if ($program->client === null) {
                $this->output->writeln('Deleting program '.$program->id);
                $program->delete();
            }
        });
    }
}
