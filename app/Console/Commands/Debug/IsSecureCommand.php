<?php
declare(strict_types=1);

namespace App\Console\Commands\Debug;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Request;

class IsSecureCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'debug:is-secure';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check https detection';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->output->writeln(sprintf('Is secure: %s', Request::isSecure()));
        $this->output->writeln(sprintf('Is from trusted proxy: %s', Request::isFromTrustedProxy()));
        return 0;
    }
}
