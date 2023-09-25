<?php
declare(strict_types=1);

namespace App\Http\Requests\Api\Focus;

use App\Http\Requests\ApiRequest;
use App\Models\Focus;
use App\Rules\HexColorRule;
use App\Rules\StatusRule;

class UpdatePatchRequest extends ApiRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'program_id' => 'integer',
            'title' => 'string',
            'has_practice' => 'boolean',
            'status' => [new StatusRule(new Focus())],
            'accent_color' => new HexColorRule(),
            'image_url' => 'url'
        ];
    }

    /**
     * @return array
     */
    public function messages(): array
    {
        return [
            'title.required' => 'Title is missing',
            'slug.required' => 'Slug is missing'
        ];
    }
}
