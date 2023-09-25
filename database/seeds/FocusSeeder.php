<?php
declare(strict_types=1);

use App\Models\ContentItem;
use App\Models\Focus;
use App\Models\Program;
use App\Models\ScheduleTopic;
use App\Models\Topic;
use App\Models\User;
use App\Models\UserReflection;
use Illuminate\Database\Seeder;

class FocusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        factory(Focus::class, 10)->create()->each(static function (Focus $focus) {
            /* @var Program $program */
            $program = Program::inRandomOrder()->first();
            $program->focuses()->attach($focus->id);
            $focus->topics()->saveMany(factory(Topic::class, random_int(2, 5))->create(['focus_id' => $focus->id])->each(static function (Topic $topic) {
                $topic->contentItems()->saveMany(factory(ContentItem::class, random_int(2, 4))->create(['focus_id' => $topic->focus_id, 'topic_id' => $topic->id])->each(static function (ContentItem $contentItem) {
                    /* @var User $user */
                    $user = User::inRandomOrder()->first();
                    if (random_int(0, 1)) {
                        factory(UserReflection::class)->create(['content_item_id' => $contentItem->id, 'user_id' => $user->id]);
                    }
                    if (random_int(0, 1)) {
                        factory(ScheduleTopic::class)->create(['user_id' => $user->id, 'topic_id' => $contentItem->topic_id, 'content_item_id' => $contentItem->id]);
                    }
                }));
            }));
        });
    }
}
