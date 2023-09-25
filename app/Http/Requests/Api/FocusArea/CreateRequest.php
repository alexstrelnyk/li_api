<?php
declare(strict_types=1);

namespace App\Http\Requests\Api\FocusArea;

use App\Http\Requests\ApiRequest;
use App\Models\FocusArea;
use App\Rules\StatusRule;

/**
 * Class CreateRequest
 * @package App\Http\Requests\Api\FocusArea
 */
class CreateRequest extends ApiRequest
{
    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            'program_id' => 'required|exists:programs,id',
            'focus_id' => 'required|exists:focuses,id',
            'status' => ['required', new StatusRule(new FocusArea())]
        ];
    }
}
