<?php
declare(strict_types=1);

namespace App\Console\Commands\Clear;

use App\Exceptions\ActivityNotFoundException;
use App\Models\Activity;
use App\Services\Activity\ActivityService;
use App\Services\Activity\Types\ActivityInterface;
use Exception;
use Illuminate\Console\Command;

class ClearActivities extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'clear:activities';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clear incomplete activities';

    /**
     * @var ActivityService
     */
    private $activityService;

    /**
     * Create a new command instance.
     *
     * @param ActivityService $activityService
     */
    public function __construct(ActivityService $activityService)
    {
        parent::__construct();
        $this->activityService = $activityService;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     * @throws ActivityNotFoundException
     * @throws Exception
     */
    public function handle()
    {
        $activities = Activity::all();
        foreach ($activities as $activity) {
            /** @var Activity $activity */
            try {
                $payloads = $this->activityService->preparePayloads(json_decode($activity->payloads, true), $activity);
            } catch (Exception $exception) {
                $activity->delete();
                $this->output->writeln('Activity ' . $activity->id . ' deleted');
                continue;
            }

            $activityClass = $this->activityService->getActivityClass($activity->type);

            $activityRealization = $this->activityService->getAssociations();

            if (empty($activity->messageMe)) {
                /** @var ActivityInterface $activityElem */
                $activityElem = new $activityRealization[$activityClass]['me'];

                $activityElem->setPayloads($payloads);
                $activityElem->setUser($activity->user);

                try {
                    $activity->messageMe = $activityElem->getText();
                    $activity->save();
                    $this->output->writeln('Activity ' . $activity->id . ' updated');
                } catch (Exception $exception) {
                    $activity->delete();
                    $this->output->writeln('Activity ' . $activity->id . ' deleted');
                }
            }

            if (empty($activity->messageCompany)) {
                $activityElem = new $activityRealization[$activityClass]['company'];

                $activityElem->setPayloads($payloads);
                $activityElem->setUser($activity->user);

                try {
                    $activity->messageCompany = $activityElem->getText();
                    $activity->save();
                    $this->output->writeln('Activity ' . $activity->id . ' updated');
                } catch (Exception $exception) {
                    $activity->delete();
                    $this->output->writeln('Activity ' . $activity->id . ' deleted');
                }
            }
        }
    }
}
