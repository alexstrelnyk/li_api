<?php
declare(strict_types=1);

use App\Models\Client;
use App\Models\User;
use Illuminate\Database\Seeder;

class AppUserSeeder extends Seeder
{
    public function run(): void
    {
        $clients = Client::all();
        factory(User::class, 20)->create(['permission' => User::APP_USER, 'client_id' => $clients->random()->first()->id]);
        factory(User::class)->create(['permission' => User::APP_USER, 'client_id' => $clients->random()->first()->id, 'token' => 'user', 'email' => 'user@user.com']);
    }
}
