<?php
declare(strict_types=1);

namespace App\Providers;

use App\Models\User;
use App\Transformers\AboutMe\AboutMeTransformer;
use App\Transformers\AboutMe\AboutMeTransformerInterface;
use App\Transformers\Activity\ActivityTransformerInterface;
use App\Transformers\Auth\AuthUserTransformer;
use App\Transformers\Auth\AuthUserTransformerInterface;
use App\Transformers\Client\ClientTransformer;
use App\Transformers\Client\ClientTransformerInterface;
use App\Transformers\ContentItem\ContentItemTransformer;
use App\Transformers\ContentItem\ContentItemTransformerInterface;
use App\Transformers\ContentItem\ContentItemOfUserTransformer;
use App\Transformers\ContentItem\Focus\CutFocusTransformer;
use App\Transformers\ContentItem\Focus\CutFocusTransformerInterface;
use App\Transformers\ContentItemProgressMetaTransformer;
use App\Transformers\ContentItemProgressTransformer;
use App\Transformers\FocusArea\FocusAreaTransformer;
use App\Transformers\FocusArea\FocusAreaTransformerInterface;
use App\Transformers\GlobalSettings\GlobalSettingsTransformer;
use App\Transformers\GlobalSettings\GlobalSettingsTransformerInterface;
use App\Transformers\TopicArea\TopicAreaTransformer;
use App\Transformers\TopicArea\TopicAreaTransformerInterface;
use App\Transformers\UserFeedback\UserFeedbackTransformer;
use App\Transformers\UserFeedback\UserFeedbackTransformerInterface;
use App\Transformers\Focus\FocusTransformer;
use App\Transformers\Focus\FocusTransformerInterface;
use App\Transformers\Focus\MobileFocusTransformer;
use App\Transformers\InviteEmail\InviteEmailTransformer;
use App\Transformers\InviteEmail\InviteEmailTransformerInterface;
use App\Transformers\JournalActivity\JournalActivityTransformer;
use App\Transformers\JournalActivity\JournalActivityTransformerInterface;
use App\Transformers\Program\ProgramTransformer;
use App\Transformers\Program\ProgramTransformerInterface;
use App\Transformers\ScheduleTopic\ScheduleTopicTransformer;
use App\Transformers\ScheduleTopic\ScheduleTopicTransformerInterface;
use App\Transformers\SilScore\SilScoreTransformer;
use App\Transformers\SilScore\SilScoreTransformerInterface;
use App\Transformers\Topic\TopicTransformer;
use App\Transformers\Topic\TopicTransformerInterface;
use App\Transformers\TopicOfUser\TopicOfUserTransformer;
use App\Transformers\User\UserTransformer;
use App\Transformers\User\UserTransformerInterface;
use App\Transformers\UserReflection\UserReflectionTransformer;
use App\Transformers\UserReflection\UserReflectionTransformerInterface;
use Auth;
use Illuminate\Foundation\Application;
use Illuminate\Support\ServiceProvider;

class TransformersServiceProvider extends ServiceProvider
{
    protected const MOBILE_APPLICATION = 'app';
    protected const ADMIN_APPLICATION = 'admin';

    /**
     * @var array
     */
    protected $associations = [
        FocusTransformerInterface::class => [
            self::MOBILE_APPLICATION => MobileFocusTransformer::class,
            self::ADMIN_APPLICATION => FocusTransformer::class,
        ],
        TopicTransformerInterface::class => [
            self::MOBILE_APPLICATION => TopicOfUserTransformer::class,
            self::ADMIN_APPLICATION => TopicTransformer::class,
        ],
        ContentItemTransformerInterface::class => [
            self::MOBILE_APPLICATION => ContentItemOfUserTransformer::class,
            self::ADMIN_APPLICATION => ContentItemTransformer::class
        ],
        ClientTransformerInterface::class => [
            self::MOBILE_APPLICATION => ClientTransformer::class,
            self::ADMIN_APPLICATION => ClientTransformer::class
        ],
        UserTransformerInterface::class => [
            self::MOBILE_APPLICATION => UserTransformer::class,
            self::ADMIN_APPLICATION => UserTransformer::class

        ],
        ScheduleTopicTransformerInterface::class => [
            self::MOBILE_APPLICATION => ScheduleTopicTransformer::class,
            self::ADMIN_APPLICATION => ScheduleTopicTransformer::class
        ],
        UserReflectionTransformerInterface::class => [
            self::MOBILE_APPLICATION => UserReflectionTransformer::class,
            self::ADMIN_APPLICATION => UserReflectionTransformer::class
        ],
        ProgramTransformerInterface::class => [
            self::MOBILE_APPLICATION => ProgramTransformer::class,
            self::ADMIN_APPLICATION => ProgramTransformer::class
        ],
        InviteEmailTransformerInterface::class => [
            self::MOBILE_APPLICATION => InviteEmailTransformer::class,
            self::ADMIN_APPLICATION => InviteEmailTransformer::class
        ],
        ActivityTransformerInterface::class => [
            self::MOBILE_APPLICATION => ActivityTransformerInterface::class,
            self::ADMIN_APPLICATION => ActivityTransformerInterface::class
        ],
        SilScoreTransformerInterface::class => [
            self::MOBILE_APPLICATION => SilScoreTransformer::class,
            self::ADMIN_APPLICATION => SilScoreTransformer::class
        ],
        AboutMeTransformerInterface::class => [
            self::MOBILE_APPLICATION => AboutMeTransformer::class,
            self::ADMIN_APPLICATION => AboutMeTransformer::class
        ],
        JournalActivityTransformerInterface::class => [
            self::MOBILE_APPLICATION => JournalActivityTransformer::class,
            self::ADMIN_APPLICATION => JournalActivityTransformer::class
        ],
        UserFeedbackTransformerInterface::class => [
            self::MOBILE_APPLICATION => UserFeedbackTransformer::class,
            self::ADMIN_APPLICATION => UserFeedbackTransformer::class
        ],
        CutFocusTransformerInterface::class => [
            self::MOBILE_APPLICATION => CutFocusTransformer::class,
            self::ADMIN_APPLICATION => CutFocusTransformer::class
        ],
        AuthUserTransformerInterface::class => [
            self::MOBILE_APPLICATION => AuthUserTransformer::class,
            self::ADMIN_APPLICATION => AuthUserTransformer::class
        ],
        FocusAreaTransformerInterface::class => [
            self::MOBILE_APPLICATION => FocusAreaTransformer::class,
            self::ADMIN_APPLICATION => FocusAreaTransformer::class
        ],
        TopicAreaTransformerInterface::class => [
            self::MOBILE_APPLICATION => TopicAreaTransformer::class,
            self::ADMIN_APPLICATION => TopicAreaTransformer::class
        ],
        GlobalSettingsTransformerInterface::class => [
            self::MOBILE_APPLICATION => GlobalSettingsTransformer::class,
            self::ADMIN_APPLICATION => GlobalSettingsTransformer::class
        ]
    ];

    /**
     * @var array
     */
    protected $userAssociations = [
        User::APP_USER => self::MOBILE_APPLICATION,
        User::LI_ADMIN => self::ADMIN_APPLICATION,
    ];

    public function register(): void
    {
        foreach ($this->associations as $interface => $realisation) {
            $this->app->bind($interface, function () use ($interface) {
                $user = Auth::user();
                if ($user && array_key_exists($user->permission, $this->userAssociations)) {
                    return $this->app->get($this->associations[$interface][$this->userAssociations[$user->permission]]);
                }

                return $this->app->get($this->associations[$interface][$this->userAssociations[User::APP_USER]]);
            });
        }

        $this->app->bind(TopicTransformer::class, static function (Application $application) {
            return new TopicTransformer($application->get(ContentItemTransformerInterface::class));
        });

        $this->app->bind(TopicOfUserTransformer::class, static function (Application $application) {
            return new TopicOfUserTransformer(
                $application->get(ScheduleTopicTransformerInterface::class),
                $application->get(ContentItemTransformerInterface::class),
                Auth::user()
            );
        });

        $this->app->bind(ContentItemOfUserTransformer::class, static function (Application $application) {
            return new ContentItemOfUserTransformer(
                Auth::user(),
                $application->get(ContentItemProgressTransformer::class),
                $application->get(ContentItemProgressMetaTransformer::class)
            );
        });
    }
}
