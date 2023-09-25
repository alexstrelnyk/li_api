<?php

namespace App\Console\Commands;

use App\Models\Client;
use App\Models\ContentItem;
use App\Models\ContentItemArea;
use App\Models\Focus;
use App\Models\FocusArea;
use App\Models\FocusAreaTopic;
use App\Models\Interfaces\AuditableInterface;
use App\Models\Program;
use App\Models\Topic;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Schema;

class MigrationDBCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:migrate {sourceConnection} {destinationConnection}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
        $models = [
//            (new User())->newQuery()->whereNull('client_id'),
//            (new Client())->newQuery(),
//            (new User())->newQuery()->whereNotNull('client_id'),
//            (new Focus())->newQuery(),
//            (new Topic())->newQuery()->whereNull('content_item_id'),
//            (new ContentItem())->newQuery()->whereNull('topic_id'),
//            (new Topic())->newQuery()->whereNotNull('content_item_id'),
//            (new ContentItem())->newQuery()->whereNotNull('topic_id'),
            (new Program())->newQuery(),
            (new FocusArea())->newQuery(),
            (new FocusAreaTopic())->newQuery(),
            (new ContentItemArea())->newQuery()
        ];

        Schema::disableForeignKeyConstraints();

        $sourceConnectionName = $this->input->getArgument('sourceConnection');
        $destinationConnectionName = $this->input->getArgument('destinationConnection');

        Config::set('database.default', $destinationConnectionName);

        /** @var static Model $model */
        foreach ($models as $model) {
            $model->get()->each(static function ($model) {
                if (!$model instanceof ContentItemArea) {
                    $model->delete();
                }
            });
        }

        /** @var static Model $model */
        foreach ($models as $model) {
            Config::set('database.default', $sourceConnectionName);

            $models = $model->get();

            Config::set('database.default', $destinationConnectionName);

//            $model::get()->each(static function ($model) {
//                $model->delete();
//            });

            $models->each(static function ($model) use ($destinationConnectionName) {
                /** @var Client $model */

                $model->setConnection($destinationConnectionName);
                $model->exists = false;
                if ($model instanceof AuditableInterface){
                    $model->updated_by = 1;
                    $model->created_by = 1;
                }

                if ($model instanceof Topic) {
                    if ($model->info_image === null) {
                        $model->info_image = '';
                    }
                }

                $model->save();
            });
        }

        Schema::enableForeignKeyConstraints();
    }
}
