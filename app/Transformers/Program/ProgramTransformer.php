<?php
declare(strict_types=1);

namespace App\Transformers\Program;

use App\Models\Program;
use App\Transformers\AbstractTransformer;

class ProgramTransformer extends AbstractTransformer implements ProgramTransformerInterface
{
    public function transform(Program $program): array
    {
        return [
            'id' => $program->id,
            'name' => $program->name,
            'client_id' => $program->client_id,
            'status' => $program->status,
            'client_name' => $program->client->name,
            'count_focus_areas' => $program->focusAreas()->count(),
            'create_at' => $this->date($program->created_at),
            'updated_at' => $this->date($program->updated_at),
        ];
    }
}
