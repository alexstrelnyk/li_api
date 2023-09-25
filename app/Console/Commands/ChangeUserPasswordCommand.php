<?php
declare(strict_types=1);

namespace App\Console\Commands;

use App\Manager\UserManager;
use App\Models\User;
use Illuminate\Console\Command;

class ChangeUserPasswordCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'password:change {email} {password}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * @var UserManager
     */
    private $userManager;

    /**
     * ChangeUserPasswordCommand constructor.
     *
     * @param UserManager $userManager
     */
    public function __construct(UserManager $userManager)
    {
        parent::__construct();
        $this->userManager = $userManager;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        /** @var User $user */
        $user = User::where('email', $this->input->getArgument('email'))->firstOrFail();

        $this->userManager->setPassword($user, $this->input->getArgument('password'));

        $user->save();
    }
}
