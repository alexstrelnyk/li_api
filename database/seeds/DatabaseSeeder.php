<?php
declare(strict_types=1);

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run(): void
    {
        $this->call(LIUserSeeder::class);
        $this->call(ClientSeeder::class);
        $this->call(AppUserSeeder::class);
        $this->call(LIFocusSeeder::class);
        $this->call(ClientFocusSeeder::class);
    }
}
