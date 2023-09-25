<?php
declare(strict_types=1);

namespace App\Http\Requests\Api\User;

use App\Http\Requests\ApiRequest;
use App\Rules\Base64Rule;

class AvatarRequest extends ApiRequest
{


    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'image' => ['required', new Base64Rule()]
        ];
    }
}
