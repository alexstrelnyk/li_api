<?php
declare(strict_types=1);

use App\Models\User;
use Illuminate\Database\Seeder;

class LIUserSeeder extends Seeder
{
    public function run(): void
    {
        factory(User::class, 2)->create(['permission' => User::LI_ADMIN]);
        factory(User::class, 5)->create(['permission' => User::LI_CONTENT_EDITOR]);

        factory(User::class)->create(['permission' => User::LI_ADMIN, 'email' => 'admin@admin.com', 'token' => 'admin', 'password' => 'admin']);
        factory(User::class)->create(['permission' => User::LI_CONTENT_EDITOR, 'email' => 'editor@editor.com', 'token' => 'editor', 'password' => 'editor']);
    }
}
