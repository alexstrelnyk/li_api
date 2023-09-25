<?php

use Illuminate\Database\Seeder;
use App\Models\AboutMeSettings;

class AboutMeSettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $users = \App\Models\User::inRandomOrder()->limit(\App\Models\User::count()/2)->get();
        foreach ($users as $user) {
            factory(AboutMeSettings::class)->create(['user_id' => $user->id]);
        }
    }
}
