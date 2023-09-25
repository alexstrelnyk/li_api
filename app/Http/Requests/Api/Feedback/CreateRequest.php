<?php
declare(strict_types=1);

namespace App\Http\Requests\Api\Feedback;

use App\Http\Requests\ApiRequest;

class CreateRequest extends ApiRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'text' => 'required'
        ];
    }
}
