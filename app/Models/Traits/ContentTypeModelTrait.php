<?php
declare(strict_types=1);

namespace App\Models\Traits;

use LogicException;

trait ContentTypeModelTrait
{
    /**
     * @return array
     */
    abstract public function getAvailableContentTypes(): array;

    /**
     * @param int $contentType
     */
    public function setContentTypeAttribute(int $contentType): void
    {
        if (in_array($contentType, $this::getAvailableContentTypes(), true)) {
            $this->attributes['content_type'] = $contentType;
        } else {
            throw new LogicException('Content type "'.$contentType.'" is wrong. Available types ['.implode(',', $this::getAvailableContentTypes()).']');
        }
    }
}
