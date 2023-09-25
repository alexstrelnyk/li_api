<?php
declare(strict_types=1);

namespace App\Models\Traits;

use App\Models\Interfaces\ModelStatusInterface;
use Illuminate\Database\Eloquent\Builder;
use LogicException;

trait StatusModelTrait
{
    /**
     * @return array
     */
    abstract public function getAvailableStatuses(): array;

    /**
     * @param int $status
     */
    public function setStatusAttribute(int $status): void
    {
        if (in_array($status, $this->getAvailableStatuses(), true)) {
            $this->attributes['status'] = $status;
        } else {
            throw new LogicException('Status '.$status.' is wrong. Available statuses ['.implode(',', $this->getAvailableStatuses()).']');
        }
    }

    /**
     * @param Builder $builder
     *
     * @return Builder
     */
    public function scopePublished(Builder $builder): Builder
    {
        return $builder->where('status', ModelStatusInterface::STATUS_PUBLISHED);
    }
}
