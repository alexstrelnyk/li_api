<?php
declare(strict_types=1);

namespace App\Console\Commands\Clear;

use App\Models\ContentItem;
use App\Models\Topic;
use App\Models\User;
use Auth;
use Illuminate\Console\Command;
use Illuminate\Contracts\Auth\Authenticatable;

class ClearContentItemsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'clear:content-items';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete not valid ContentItems';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        /** @var Authenticatable $user */
        $user = User::where('permission', User::LI_ADMIN)->first();
        Auth::setUser($user);

        ContentItem::all()->each(function (ContentItem $contentItem) {
            if ($contentItem->focus === null) {
                $this->output->writeln('Deleting Content item '.$contentItem->id);
                $contentItem->delete();
            }

            if ($contentItem->topic instanceof Topic && $contentItem->topic->has_practice && $contentItem->is_practice !== true) {
                $contentItem->is_practice = true;
                $contentItem->save();
                $this->output->writeln('Content item '.$contentItem->id.' changed to Practice-based');
            } elseif ($contentItem->focusAreaTopics()->count() > 0 && $contentItem->is_practice !== false) {
                $contentItem->is_practice = false;
                $contentItem->save();
                $this->output->writeln('Content item '.$contentItem->id.' changed to Learn-based');
            }

            if ($contentItem->is_practice && !$contentItem->topic instanceof Topic) {
                $contentItem->is_practice = false;
                $contentItem->save();
                $this->output->writeln('Content set as learn based');
            }

            if ($contentItem->is_practice && $contentItem->topic instanceof Topic && !$contentItem->topic->has_practice) {
                $contentItem->topic->contentItem()->dissociate();
                $contentItem->save();
                $this->output->writeln('Content Item detached');
            }
        });
    }
}
