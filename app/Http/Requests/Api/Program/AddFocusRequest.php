<?php
declare(strict_types=1);

namespace App\Http\Requests\Api\Program;

use App\Http\Requests\ApiRequest;

class AddFocusRequest extends ApiRequest
{
    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            'focus_id' => 'required|exists:focuses,id'
        ];
    }

    /**
     * @return array
     */
    public function messages(): array
    {
        return [
            'focus_id.exists' => 'Focus with passed ID not found'
        ];
    }

    /**
     * @return int
     */
    public function getFocusId(): int
    {
        return $this->get('focus_id');
    }
}
