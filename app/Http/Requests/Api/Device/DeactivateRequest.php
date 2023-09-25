<?php
declare(strict_types=1);

namespace App\Http\Requests\Api\Device;

use App\Http\Requests\ApiRequest;

class DeactivateRequest extends ApiRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'device_id' => 'required'
        ];
    }

    /**
     * @return string
     */
    public function getDeviceId(): string
    {
        return $this->get('device_id');
    }
}
