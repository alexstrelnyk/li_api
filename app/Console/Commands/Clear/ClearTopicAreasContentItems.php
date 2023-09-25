<?php
declare(strict_types=1);

namespace App\Console\Commands\Clear;

use App\Models\ContentItem;
use App\Models\FocusAreaTopic;
use DB;
use Illuminate\Console\Command;

class ClearTopicAreasContentItems extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'clear:topic-areas-content-items';

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
        foreach (DB::table('focus_areas_topics_content_items')->get() as $row) {
            if (FocusAreaTopic::find($row->focus_area_topics_id) === null) {
                DB::table('focus_areas_topics_content_items')->where('focus_area_topics_id', $row->focus_area_topics_id)->delete();
                $this->output->writeln(sprintf('Row with Topic Area ID %s deleted', $row->focus_area_topics_id));
            }

            if (ContentItem::find($row->content_item_id) === null) {
                DB::table('focus_areas_topics_content_items')->where('topic_id', $row->content_item_id)->delete();
                $this->output->writeln(sprintf('Row with Content Item ID %s deleted', $row->content_item_id));
            }
        }
    }
}
