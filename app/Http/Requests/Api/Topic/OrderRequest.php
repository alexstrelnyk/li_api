<?php
declare(strict_types=1);

namespace App\Http\Requests\Api\Topic;

use App\Http\Requests\ApiRequest;

class OrderRequest extends ApiRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'order' => 'required|array'
        ];
    }
}
