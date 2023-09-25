<?php
declare(strict_types=1);

use App\Models\Client;
use App\Models\Program;
use Illuminate\Database\Seeder;

class ProgramSeeder extends Seeder
{
    public function run(): void
    {
        factory(Program::class, 5)->create(['client_id' => Client::inRandomOrder()->first()->id]);
    }
}
