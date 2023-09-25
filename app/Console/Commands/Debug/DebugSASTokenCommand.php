<?php
declare(strict_types=1);

namespace App\Console\Commands\Debug;

use App\Services\Azure\SASTokenGenerator\SasTokenGenerator;
use Illuminate\Console\Command;

class DebugSASTokenCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'debug:sas';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * @var SasTokenGenerator
     */
    private $sasTokenGenerator;

    /**
     * Create a new command instance.
     *
     * @param SasTokenGenerator $sasTokenGenerator
     */
    public function __construct(SasTokenGenerator $sasTokenGenerator)
    {
        parent::__construct();
        $this->sasTokenGenerator = $sasTokenGenerator;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $token = $this->sasTokenGenerator->generate('https://li-notice.servicebus.windows.net/li-notificationhub/registrations?api-version=2015-01');

        $this->output->writeln($token);
    }
}
