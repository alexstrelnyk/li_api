<?php
declare(strict_types=1);

namespace App\Models\Traits;

use App\Models\Client;
use App\Models\Interfaces\ModelStatusInterface;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;

trait ScopeOfUserTrait
{
    /**
     * @param Builder $builder
     * @param User $user
     * @return Builder
     */
    public function scopeOfUser(Builder $builder, User $user): Builder
    {
        if ($user->permission === User::CLIENT_ADMIN || $user->permission === User::APP_USER) {
            $client = $user->client;
            if ($client instanceof Client && $client->deactivated_at === null) {
                $userAppCondition = $user->permission === User::APP_USER;

                if ($client->content_model === Client::LI_CONTENT_ONLY || $client->content_model === Client::MIXED_CONTENT) {
                    $builder->whereHas('createdBy', static function (Builder $builder) use ($userAppCondition) {
                        $builder->whereIn('permission', User::$liRoles)
                        ->when($userAppCondition, function (Builder $query) {
                            return $query->where('status', '=', ModelStatusInterface::STATUS_PUBLISHED);
                        });
                    });
                }

                if ($client->content_model === Client::MIXED_CONTENT || $client->content_model === Client::BLANK) {
                    $builder->orWhereHas('createdBy', static function (Builder $builder) use ($client, $userAppCondition) {
                        $builder->where('client_id', $client->id)
                        ->when($userAppCondition, function (Builder $query) {
                            return $query->where('status', '=', ModelStatusInterface::STATUS_PUBLISHED);
                        });
                    });
                }
            }
        }

        return $builder;
    }
}