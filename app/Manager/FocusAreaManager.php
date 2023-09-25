<?php

namespace App\Manager;

use App\Factory\FocusAreaFactory;
use App\Models\Focus;
use App\Models\FocusArea;
use App\Models\Program;
use App\Models\Topic;
use Exception;

/**
 * Class FocusAreaManager
 * @package App\Manager
 */
class FocusAreaManager
{

    /**
     * @var FocusAreaFactory
     */
    private $focusAreaFactory;

    /**
     * FocusAreaManager constructor.
     * @param FocusAreaFactory $focusAreaFactory
     */
    public function __construct(FocusAreaFactory $focusAreaFactory)
    {
        $this->focusAreaFactory = $focusAreaFactory;
    }

    /**
     * @param Program $program
     * @param Focus $focus
     * @param array $data
     * @return FocusArea
     */
    public function create(Program $program, Focus $focus, array $data): FocusArea
    {
        $focusArea = $this->focusAreaFactory->create();

        $focusArea->focus()->associate($focus);
        $focusArea->program()->associate($program);

        $focusArea->status = $data['status'];
        $focusArea->save();

        return $focusArea;
    }


    /**
     * @param FocusArea $focusArea
     * @param Topic $topic
     */
    public function addTopic(FocusArea $focusArea, Topic $topic): void
    {
        $focusArea->topics()->attach($topic->id);
    }

    /**
     * @param FocusArea $focusArea
     * @param Topic $topic
     */
    public function removeFocus(FocusArea $focusArea, Topic $topic): void
    {
        $focusArea->topics()->detach($topic->id);
    }

    /**
     * @param FocusArea $focusArea
     *
     * @return bool
     * @throws Exception
     */
    public function delete(FocusArea $focusArea): bool
    {
        return $focusArea->delete() ?? true;
    }

    /**
     * @param FocusArea $focusArea
     * @param array $validated
     */
    public function update(FocusArea $focusArea, array $validated): void
    {
        $focusArea->status = $validated['status'];

        $focusArea->save();
    }
}