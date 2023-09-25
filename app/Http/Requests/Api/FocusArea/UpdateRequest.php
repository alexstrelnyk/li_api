<?php
declare(strict_types=1);

namespace App\Http\Requests\Api\FocusArea;

use App\Http\Requests\ApiRequest;
use App\Models\FocusArea;
use App\Rules\StatusRule;

class UpdateRequest extends ApiRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'status' => ['required', new StatusRule(new FocusArea())]
        ];
    }
}
