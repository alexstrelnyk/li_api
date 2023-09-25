<?php
declare(strict_types=1);

namespace App\Http\Requests\Api\UserReflection;

use App\Http\Requests\ApiRequest;

class CreateRequest extends ApiRequest
{
    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            'content_item_id' => 'required|exists:content_items,id',
            'skipped' => 'boolean'
        ];
    }

    /**
     * @return array
     */
    public function messages(): array
    {
        return [
            'content_item_id.exists' => 'Content Item with passed id is not found'
        ];
    }

    /**
     * @return int
     */
    public function getContentItemId(): int
    {
        return (int) $this->get('content_item_id');
    }

    /**
     * @return string|null
     */
    public function getInput(): ?string
    {
        return $this->get('input');
    }

    /**
     * @return bool
     */
    public function getSkipped(): bool
    {
        return (bool) $this->get('skipped', false);
    }
}
