<?php
declare(strict_types=1);

use App\Models\ContentItem;
use App\Models\Focus;
use App\Models\Topic;
use App\Models\User;
use Illuminate\Database\Seeder;

class LIFocusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     * @throws Exception
     */
    public function run(): void
    {
        /** @var User $liCreator */
        $liCreator = User::where('permission', User::LI_ADMIN)->first();
        Auth::setUser($liCreator);

        $contentTypes = [ContentItem::ARTICLE_CONTENT_TYPE, ContentItem::AUDIO_CONTENT_TYPE, ContentItem::VIDEO_CONTENT_TYPE];

        factory(Focus::class, 5)->create()->each(static function (Focus $focus) use ($contentTypes) {

            foreach ($contentTypes as $contentType) {
                $focus->contentItems()->saveMany(
                    factory(ContentItem::class, random_int(5, 10))->create([
                        'content_type' => $contentType,
                        'focus_id' => $focus->id
                    ])
                );
            }

            // Learn-based Topics
            $focus->topics()->saveMany(
                factory(Topic::class, 3)->create([
                    'focus_id' => $focus->id,
                    'has_practice' => false,
                ])
            );

            // Practice-based Topics
            $focus->topics()->saveMany(
                factory(Topic::class, 2)->create([
                    'focus_id' => $focus->id,
                    'has_practice' => true,
                ])->each(static function (Topic $topic) use ($focus, $contentTypes) {
                    $topic->contentItem()->associate(factory(ContentItem::class)->create([
                        'focus_id' => $focus->id,
                        'content_type' => $contentTypes[array_rand($contentTypes)],
                        'is_practice' => true
                    ]));
                })
            );
        });
    }
}
