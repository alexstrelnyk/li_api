<?php
declare(strict_types=1);

use App\Models\Client;
use App\Models\ContentItem;
use App\Models\Focus;
use App\Models\FocusArea;
use App\Models\FocusAreaTopic;
use App\Models\Program;
use App\Models\Topic;
use Illuminate\Database\Seeder;

class ClientFocusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     * @throws Exception
     */
    public function run(): void
    {
        $clients = Client::all();
        $focuses = Focus::all();

        foreach ($clients as $client) {
            factory(Program::class, 1)->create(['client_id' => $client->id, 'status' => Program::STATUS_PUBLISHED])->each(static function (Program $program) use ($focuses) {
                foreach ($focuses as $focus) {
                    $learnTopics = Topic::ofFocus($focus)->get();
                    $practiceTopics = Topic::ofFocus($focus)->get();
                    factory(FocusArea::class, 1)->create([
                        'focus_id' => $focus->id,
                        'program_id' => $program->id,
                        'status' => FocusArea::STATUS_PUBLISHED
                    ])->each(static function (FocusArea $focusArea) use ($learnTopics, $focus) {
                        foreach ($learnTopics as $topic) {
                            factory(FocusAreaTopic::class, 1)->create([
                                'topic_id' => $topic->id,
                                'focus_area_id' => $focusArea->id,
                                'status' => FocusAreaTopic::STATUS_PUBLISHED
                            ])->each(static function (FocusAreaTopic $topicArea) use ($focus) {
                                $contentItemArticle = ContentItem::article()->ofFocus($focus)->doesntHave('topicAreas')->first();
                                $contentItemAudio = ContentItem::audio()->ofFocus($focus)->doesntHave('topicAreas')->first();
                                $contentItemVideo = ContentItem::video()->ofFocus($focus)->doesntHave('topicAreas')->first();
                                $topicArea->contentItems()->attach($contentItemArticle);
                                $topicArea->contentItems()->attach($contentItemAudio);
                                $topicArea->contentItems()->attach($contentItemVideo);
                            });
                        }
                    });

                    factory(FocusArea::class, 1)->create([
                        'focus_id' => $focus->id,
                        'program_id' => $program->id,
                        'status' => FocusArea::STATUS_PUBLISHED
                    ])->each(static function (FocusArea $focusArea) use ($practiceTopics) {
                        foreach ($practiceTopics as $topic) {
                            factory(FocusAreaTopic::class, 1)->create([
                                'topic_id' => $topic->id,
                                'focus_area_id' => $focusArea->id,
                                'status' => FocusAreaTopic::STATUS_PUBLISHED
                            ]);
                        }
                    });
                }
            });
        }

    }
}
