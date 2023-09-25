<?php
declare(strict_types=1);

namespace App\Http\Requests\Api\Client;

use App\Http\Requests\ApiRequest;
use App\Models\Client;

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
            'name' => 'required',
            'content_model' => 'required|in:'.implode(',', Client::getAvailableContentModels()),
            'primer_notice_timing' => 'required|numeric',
            'content_notice_timing' => 'required|numeric',
            'reflection_notice_timing' => 'required|numeric',
        ];
    }
}
