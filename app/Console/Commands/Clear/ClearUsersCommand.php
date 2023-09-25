<?php
declare(strict_types=1);

namespace App\Console\Commands\Clear;

use App\Models\User;
use Illuminate\Console\Command;

class ClearUsersCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'clear:users';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Change invalid data in users table description';

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
        User::all()->each(static function (User $user) {
            $user->tip_time = '07:00:00';
            $user->save();
        });
    }
}
