<?php
declare(strict_types=1);

namespace App\Models;

use App\Models\Interfaces\AuditableInterface;
use App\Models\Interfaces\ContentItemTypeInterface;
use App\Models\Interfaces\ModelStatusInterface;
use App\Models\Traits\ContentTypeModelTrait;
use App\Models\Traits\ScopeOfUserTrait;
use App\Models\Traits\StatusModelTrait;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Carbon;
use LogicException;
use Yajra\Auditable\AuditableTrait;

/**
 * App\Models\ContentItem
 *
 * @property-read Focus $focus
 * @property mixed $content_type
 * @property-write mixed $status
 * @property-read Topic $topic
 * @method static Builder|ContentItem newModelQuery()
 * @method static Builder|ContentItem newQuery()
 * @method static Builder|ContentItem query()
 * @mixin Eloquent
 * @property int $id
 * @property int $focus_id
 * @property int $topic_id
 * @property string $primer_title
 * @property string $primer_content
 * @property int $reading_time
 * @property string|null $info_content_image
 * @property string|null $info_quick_tip
 * @property string $info_full_content
 * @property string|null $info_video_uri
 * @property string $info_source_title
 * @property string $info_source_link
 * @property int $has_reflection
 * @property string|null $reflection_help_text
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @method static Builder|ContentItem whereContentType($value)
 * @method static Builder|ContentItem whereCreatedAt($value)
 * @method static Builder|ContentItem whereFocusId($value)
 * @method static Builder|ContentItem whereHasReflection($value)
 * @method static Builder|ContentItem whereId($value)
 * @method static Builder|ContentItem whereInfoContentImage($value)
 * @method static Builder|ContentItem whereInfoFullContent($value)
 * @method static Builder|ContentItem whereInfoQuickTip($value)
 * @method static Builder|ContentItem whereInfoSourceLink($value)
 * @method static Builder|ContentItem whereInfoSourceTitle($value)
 * @method static Builder|ContentItem whereInfoVideoUri($value)
 * @method static Builder|ContentItem wherePrimerContent($value)
 * @method static Builder|ContentItem wherePrimerTitle($value)
 * @method static Builder|ContentItem whereReadingTime($value)
 * @method static Builder|ContentItem whereReflectionHelpText($value)
 * @method static Builder|ContentItem whereStatus($value)
 * @method static Builder|ContentItem whereTopicId($value)
 * @method static Builder|ContentItem whereUpdatedAt($value)
 * @property-read UserReflection $userReflection
 * @property-read ContentItemUserProgress $contentItemUserProgress
 * @property string $title
 * @property-read int|null $content_item_user_progress_count
 * @method static Builder|ContentItem whereTitle($value)
 * @property string|null $info_audio_uri
 * @property int|null $created_by
 * @property int|null $updated_by
 * @property-read User|null $creator
 * @property-read string $created_by_name
 * @property-read string $updated_by_name
 * @property-read User|null $updater
 * @method static Builder|ContentItem owned()
 * @method static Builder|ContentItem whereCreatedBy($value)
 * @method static Builder|ContentItem whereInfoAudioUri($value)
 * @method static Builder|ContentItem whereUpdatedBy($value)
 * @property-read User $createdBy
 * @property-read User|null $updatedBy
 * @property-read Collection|UserFeedback[] $userFeedbacks
 * @property-read int|null $user_feedbacks_count
 * @method static Builder|ContentItem bookmarked(User $user)
 * @method static Builder|ContentItem ofUser(User $user)
 * @method static Builder|ContentItem completed(User $user)
 * @method static Builder|ContentItem inProgress(User $user)
 * @property-read FocusArea $focusArea
 * @property-read Collection|FocusAreaTopic[] $focusAreaTopics
 * @property-read int|null $focus_area_topics_count
 * @method static Builder|ContentItem published()
 * @property int|null $is_practice
 * @method static Builder|ContentItem whereIsPractice($value)
 * @method static Builder|ContentItem tip()
 * @property-read Collection|User[] $viewedUsers
 * @property-read int|null $viewed_users_count
 * @property-read Topic|null $tipTopic
 * @property string|null $primer_notification_text
 * @property string|null $content_notification_text
 * @property string|null $reflection_notification_text
 * @method static Builder|ContentItem whereContentNotificationText($value)
 * @method static Builder|ContentItem wherePrimerNotificationText($value)
 * @method static Builder|ContentItem whereReflectionNotificationText($value)
 * @property-read Collection|FocusAreaTopic[] $topicAreas
 * @property-read int|null $topic_areas_count
 * @method static Builder|ContentItem article()
 * @method static Builder|ContentItem audio()
 * @method static Builder|ContentItem video()
 * @method static Builder|ContentItem ofFocus(Focus $focus)
 * @method static Builder|ContentItem withReflection()
 * @property-read Collection|UserReflection[] $userReflections
 * @property-read int|null $user_reflections_count
 */
class ContentItem extends Model implements ModelStatusInterface, AuditableInterface, ContentItemTypeInterface
{
    use AuditableTrait;
    use StatusModelTrait;
    use ContentTypeModelTrait;
    use ScopeOfUserTrait;

    /**
     * @var array
     */
    protected $fillable = [
        'content_type',
        'primer_title',
        'primer_content',
        'reading_time',
        'info_content_image',
        'info_quick_tip',
        'info_full_content',
        'info_video_uri',
        'info_audio_uri',
        'info_source_title',
        'info_source_link',
        'has_reflection',
        'reflection_help_text',
        'status',
        'title',
        'focus_id',
        'topic_id',
        'is_practice'
    ];

    /**
     * @var array
     */
    protected $casts = [
        'has_reflection' => 'boolean'
    ];

    /**
     * @return BelongsTo
     */
    public function focus(): BelongsTo
    {
        return  $this->belongsTo(Focus::class);
    }

    /**
     * @return HasOne
     */
    public function topic(): hasOne
    {
        return $this->hasOne(Topic::class);
    }

    /**
     * @return BelongsTo
     */
    public function tipTopic(): BelongsTo
    {
        return $this->belongsTo(Topic::class, 'topic_id');
    }

    /**
     * @param int $type
     */
    public function setType(int $type): void
    {
        $this->content_type = $type;
    }

    /**
     * @param Builder $builder
     * @param User $user
     * @return Builder
     */
    public function scopeCompleted(Builder $builder, User $user): Builder
    {
        return $builder->whereHas('contentItemUserProgress', static function (Builder $builder) use ($user) {
            $builder->where('user_id', $user->id)
                ->whereNotNull('completed_at');
        });
    }

    /**
     * @param Builder $builder
     * @param User $user
     * @return Builder
     */
    public function scopeInProgress(Builder $builder, User $user): Builder
    {
        return $builder->whereDoesntHave('contentItemUserProgress', static function (Builder $builder) use ($user) {
            $builder->where('user_id', $user->id);
        })->orWhereHas('contentItemUserProgress', static function (Builder $builder) use ($user) {
            $builder->where('user_id', $user->id)->whereNull('completed_at');
        });
    }

    /**
     * @return array
     */
    public static function getAvailableTypes(): array
    {
        return [
            self::ARTICLE_CONTENT_TYPE,
            self::AUDIO_CONTENT_TYPE,
            self::VIDEO_CONTENT_TYPE,
            self::TIP_CONTENT_TYPE
        ];
    }

    /**
     * @return array
     */
    public static function getAvailableStatuses(): array
    {
        return [
            self::STATUS_DRAFT,
            self::STATUS_REVIEW,
            self::STATUS_PUBLISHED,
        ];
    }

    /**
     * @return array
     */
    public static function getAvailableContentTypes(): array
    {
        return [
            self::ARTICLE_CONTENT_TYPE,
            self::AUDIO_CONTENT_TYPE,
            self::VIDEO_CONTENT_TYPE,
            self::TIP_CONTENT_TYPE,
        ];
    }

    /**
     * @return HasMany
     */
    public function userFeedbacks(): HasMany
    {
        return $this->hasMany(UserFeedback::class);
    }

    /**
     * @param Builder $builder
     * @param User $user
     *
     * @return Builder
     */
    public function scopeBookmarked(Builder $builder, User $user) : Builder
    {
        $builder->whereHas('userFeedbacks', function (Builder $builder) use ($user) {
            /** @var UserFeedback $builder */
            $builder->bookmarked($user);
        });

        return $builder;
    }

    /**
     * @return HasMany
     */
    public function userReflections(): HasMany
    {
        return $this->hasMany(UserReflection::class);
    }

    /**
     * @return int
     */
    public function getTotal(): int
    {
        return $this->has_reflection ? 3 : 2;
    }

    /**
     * @return HasMany
     */
    public function contentItemUserProgress(): HasMany
    {
        return $this->hasMany(ContentItemUserProgress::class);
    }

    /**
     * @return string
     */
    public function getTypeSlug(): string
    {
        $types = [
            self::ARTICLE_CONTENT_TYPE => 'article',
            self::AUDIO_CONTENT_TYPE => 'audio',
            self::VIDEO_CONTENT_TYPE => 'video',
            self::TIP_CONTENT_TYPE => 'tip',
        ];

        if (!isset($types[$this->content_type])) {
            throw new LogicException('Content type '.$this->content_type.' is not resolved!');
        }

        return $types[$this->content_type];
    }

    /**
     * @return BelongsTo
     */
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * @return BelongsTo
     */
    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * @return User|null
     */
    public function getCreatedBy(): User
    {
        return $this->createdBy;
    }

    /**
     * @return BelongsTo
     */
    public function focusArea(): BelongsTo
    {
        return  $this->belongsTo(FocusArea::class);
    }

    /**
     * @return BelongsToMany
     */
    public function focusAreaTopics(): BelongsToMany
    {
        return $this->belongsToMany(FocusAreaTopic::class, 'focus_areas_topics_content_items', 'content_item_id', 'focus_area_topics_id');
    }

    /**
     * @return BelongsToMany
     */
    public function topicAreas(): BelongsToMany
    {
        return $this->belongsToMany(FocusAreaTopic::class, 'focus_areas_topics_content_items', 'content_item_id', 'focus_area_topics_id');
    }

    /**
     * @return BelongsToMany
     */
    public function viewedUsers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'viewed_tips', 'content_item_id');
    }

    /**
     * @param Builder $builder
     *
     * @return Builder
     */
    public function scopeTip(Builder $builder): Builder
    {
        return $builder->where('content_type', self::TIP_CONTENT_TYPE);
    }

    /**
     * @return Topic|null
     */
    public function getTopic(): ?Topic
    {
        if (in_array($this->content_type, [
            ContentItemTypeInterface::ARTICLE_CONTENT_TYPE,
            ContentItemTypeInterface::AUDIO_CONTENT_TYPE,
            ContentItemTypeInterface::VIDEO_CONTENT_TYPE], true)
        ) {
            if ($this->is_practice) {
                $topic = $this->topic;
            } else {
                $topic = $this->topicAreas()->first() instanceof FocusAreaTopic ? $this->topicAreas()->first()->topic : null;
            }
        } else {
            $topic = $this->tipTopic;
        }

        return $topic;
    }

    /**
     * @param Builder $builder
     *
     * @return Builder
     */
    public function scopeArticle(Builder $builder): Builder
    {
        return $builder->where('content_type', self::ARTICLE_CONTENT_TYPE);
    }

    /**
     * @param Builder $builder
     *
     * @return Builder
     */
    public function scopeAudio(Builder $builder): Builder
    {
        return $builder->where('content_type', self::AUDIO_CONTENT_TYPE);
    }

    /**
     * @param Builder $builder
     *
     * @return Builder
     */
    public function scopeVideo(Builder $builder): Builder
    {
        return $builder->where('content_type', self::VIDEO_CONTENT_TYPE);
    }

    /**
     * @param Builder $builder
     * @param Focus $focus
     *
     * @return Builder
     */
    public function scopeOfFocus(Builder $builder, Focus $focus): Builder
    {
        return $builder->where('focus_id', $focus->id);
    }

    /**
     * @param Builder $builder
     *
     * @return Builder
     */
    public function scopeWithReflection(Builder $builder): Builder
    {
        return $builder->where('has_reflection', true);
    }
}
