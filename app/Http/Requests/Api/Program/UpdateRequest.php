<?php
declare(strict_types=1);

namespace App\Http\Requests\Api\Program;

use App\Http\Requests\ApiRequest;
use App\Models\Program;
use App\Rules\StatusRule;

/**
 * Class UpdateRequest
 * @package App\Http\Requests\Api\Program
 */
class UpdateRequest extends ApiRequest
{

    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            'status' => ['required', new StatusRule(new Program())],
            'name' => 'string',
            'client_id' => 'integer|required|exists:clients,id'
        ];
    }
}
