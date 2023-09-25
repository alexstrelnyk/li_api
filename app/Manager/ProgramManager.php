<?php
declare(strict_types=1);

namespace App\Manager;

use App\Factory\ProgramFactory;
use App\Models\Client;
use App\Models\Focus;
use App\Models\FocusArea;
use App\Models\Interfaces\ModelStatusInterface;
use App\Models\Program;

/**
 * Class ProgramManager
 * @package App\Manager
 */
class ProgramManager
{
    /**
     * @var ProgramFactory
     */
    private $programFactory;

    /**
     * @var FocusAreaManager
     */
    private $focusAreaManager;

    /**
     * ProgramManager constructor.
     *
     * @param ProgramFactory $programFactory
     * @param FocusAreaManager $focusAreaManager
     */
    public function __construct(ProgramFactory $programFactory, FocusAreaManager $focusAreaManager)
    {
        $this->programFactory = $programFactory;
        $this->focusAreaManager = $focusAreaManager;
    }

    /**
     * @param Client $client
     * @param string|null $name
     * @param int|null $status
     *
     * @return Program
     */
    public function createProgram(Client $client, ?string $name = null, ?int $status = Program::STATUS_DRAFT): Program
    {
        $program = $this->programFactory->create($client);

        if (!$name) {
            $countOfPrograms = $client->programs->count();
            $name = 'Program ' . ($countOfPrograms + 1);
        }

        if (!$status) {
            $status = Program::STATUS_DRAFT;
        }

        $program->name = $name;
        $program->status = $status;

        $program->save();

        return $program;
    }

    /**
     * @param Program $program
     * @param array $data
     * @return Program
     */
    public function update(Program $program, array $data): Program
    {
        $client = Client::findOrFail($data['client_id']);

        $program->client()->associate($client);

        $program->fill($data);

        $program->update();

        return $program;
    }

    /**
     * @param Program $program
     * @param Focus $focus
     */
    public function addFocus(Program $program, Focus $focus): void
    {
        $this->focusAreaManager->create($program, $focus, ['status' => ModelStatusInterface::STATUS_DRAFT]);
    }

    /**
     * @param Program $program
     * @param Focus $focus
     */
    public function removeFocus(Program $program, Focus $focus): void
    {
        FocusArea::ofProgram($program)->ofFocus($focus)->get()->each(static function (FocusArea $focusArea) {
            $focusArea->delete();
        });
    }
}
