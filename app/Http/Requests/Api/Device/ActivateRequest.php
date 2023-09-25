<?php
declare(strict_types=1);

namespace App\Http\Requests\Api\Device;

use App\Http\Requests\ApiRequest;

class ActivateRequest extends ApiRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'device_id' => 'required',
            'type' => 'required|in:apple,fcm'
        ];
    }

    /**
     * @return string
     */
    public function getDeviceId(): string
    {
        return $this->get('device_id');
    }

    /**
     * @return string|null
     */
    public function getDeviceToken(): ?string
    {
        return $this->get('device_token');
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->get('type');
    }
}
