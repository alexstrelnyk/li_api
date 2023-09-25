<?php
declare(strict_types=1);

namespace App\Providers;

use App\Models\FocusArea;
use App\Models\FocusAreaTopic;
use App\Models\Topic;
use App\Models\User;
use App\Models\Client;
use App\Models\ContentItem;
use App\Models\Focus;
use App\Models\InviteEmail;
use App\Policies\ClientPolicy;
use App\Policies\ContentItemPolicy;
use App\Policies\FocusAreaPolicy;
use App\Policies\FocusPolicy;
use App\Policies\InviteEmailPolicy;
use App\Policies\TopicAreaPolicy;
use App\Policies\TopicPolicy;
use App\Policies\UserPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        ContentItem::class => ContentItemPolicy::class,
        Focus::class => FocusPolicy::class,
        Topic::class => TopicPolicy::class,
        Client::class => ClientPolicy::class,
//        InviteEmail::class => InviteEmailPolicy::class,
        User::class => UserPolicy::class,
        FocusArea::class => FocusAreaPolicy::class,
        FocusAreaTopic::class => TopicAreaPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot(): void
    {
        $this->registerPolicies();
    }
}
