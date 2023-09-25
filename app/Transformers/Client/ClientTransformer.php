<?php
declare(strict_types=1);

namespace App\Transformers\Client;

use App\Models\Client;
use App\Transformers\AbstractTransformer;

class ClientTransformer extends AbstractTransformer implements ClientTransformerInterface
{
    public function transform(Client $client): array
    {
        return [
            'id' => $client->id,
            'name' => $client->name,
            'content_model' => $client->content_model,
            'primer_notice_timing' => $client->primer_notice_timing,
            'content_notice_timing' => $client->content_notice_timing,
            'reflection_notice_timing' => $client->reflection_notice_timing,
            'active' => ! (bool) $client->deactivated_at,
            'count_users' => $client->users()->count(),
            'created_at' => $this->date($client->created_at),
            'updated_at' => $this->date($client->updated_at),
            'created_by' => $client->created_by
        ];
    }
}
