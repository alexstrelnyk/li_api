<?php
declare(strict_types=1);

use App\Models\Client;
use App\Models\User;
use Illuminate\Database\Seeder;

class ClientSeeder extends Seeder
{
    public function run(): void
    {
        $creator = User::where('permission', User::LI_ADMIN)->inRandomOrder()->firstOrFail();
        factory(Client::class, 1)->create(['created_by' => $creator->id, 'updated_by' => $creator->id])->each(static function (Client $client) {
            $client->user()->save(factory(User::class)->create(['permission' => User::CLIENT_ADMIN]));

            factory(User::class)->create(['email' => 'DJZT44@gmail.com', 'client_id' => $client->id]);
            factory(User::class)->create(['email' => 'bogdan999ivanov@gmail.com', 'client_id' => $client->id]);
        });
    }
}
