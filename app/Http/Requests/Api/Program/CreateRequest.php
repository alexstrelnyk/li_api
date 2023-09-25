<?php
declare(strict_types=1);

namespace App\Http\Requests\Api\Program;

use App\Http\Requests\ApiRequest;
use App\Models\Program;
use App\Rules\StatusRule;

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
            'client_id' => 'required|exists:clients,id',
            'status' => ['required', new StatusRule(new Program())]
        ];
    }

    /**
     * @return array
     */
    public function messages(): array
    {
        return [
            'client_id.exists' => 'Client with passed ID not found'
        ];
    }

    /**
     * @return int
     */
    public function getClientId(): int
    {
        return (int) $this->get('client_id');
    }

    /**
     * @return string|null
     */
    public function getName(): ?string
    {
        return $this->get('name');
    }

    /**
     * @return int|null
     */
    public function getStatus(): ?int
    {
        return $this->get('status');
    }
}
