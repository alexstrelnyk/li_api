<?php
declare(strict_types=1);

namespace App\Http\Requests\Api\User;

use App\Http\Requests\ApiRequest;
use App\Rules\Base64Rule;

class ImportRequest extends ApiRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'base_64' => ['required', new Base64Rule()]
        ];
    }
}
