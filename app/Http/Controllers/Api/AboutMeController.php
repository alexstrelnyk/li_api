<?php
declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\ApiController;
use App\Http\Requests\Api\Setting\EditRequest;
use App\Http\Requests\Api\AboutMe\UpdatePatchRequest;
use App\Http\Requests\Api\AboutMe\UpdateRequest;
use App\Http\Requests\Api\User\AvatarRequest;
use App\Http\Requests\ListRequest;
use App\Http\Responses\ListResponseInterface;
use App\Manager\AboutMeSettingsManager;
use App\Manager\UserManager;
use App\Models\ContentItem;
use App\Models\Focus;
use App\Models\User;
use App\Services\ImageConverter\ImageConverter;
use App\Services\ConsecutiveDaysService\ConsecutiveDaysService;
use App\Services\SilScoreService\SilScoreService;
use App\Transformers\AboutMe\AboutMeTransformerInterface;
use App\Transformers\ContentItem\ContentItemTransformerInterface;
use App\Transformers\ContentItem\Focus\CutFocusTransformerInterface;
use App\Transformers\Setting\SettingTransformer;
use Auth;
use Carbon\Carbon;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\File;
use Illuminate\Http\JsonResponse;
use Storage;
use Swagger\Annotations as SWG;

class AboutMeController extends ApiController
{
    /**
     * @SWG\Get(
     *     path="/about/profile",
     *     summary="Get Profile",
     *     tags={"About Me"},
     *     operationId="getProfile",
     *     security={{"apiToken": {}}},
     *     @SWG\Response(
     *         response=200,
     *         description="Successful operation",
     *         @SWG\Schema(
     *             @SWG\Property(
     *                 property="data",
     *                 @SWG\Property(
     *                     property="image",
     *                     type="string"
     *                 ),
     *                 @SWG\Property(
     *                     property="first_name",
     *                     type="string"
     *                 ),
     *                 @SWG\Property(
     *                     property="last_name",
     *                     type="string"
     *                 ),
     *                 @SWG\Property(
     *                     property="sil_score",
     *                     type="integer"
     *                 ),
     *                 @SWG\Property(
     *                     property="sil_score_progress",
     *                     type="array",
     *                     @SWG\Items(
     *                         type="integer",
     *                     ),
     *                 ),
     *                 @SWG\Property(
     *                     property="sessions_completed_count",
     *                     type="integer"
     *                 ),
     *                 @SWG\Property(
     *                     property="events_scheduled_count",
     *                     type="integer"
     *                 ),
     *                 @SWG\Property(
     *                     property="reflections_written_count",
     *                     type="integer"
     *                 ),
     *                 @SWG\Property(
     *                     property="department",
     *                     type="string"
     *                 ),
     *                 @SWG\Property(
     *                     property="role",
     *                     type="string"
     *                 ),
     *                 @SWG\Property(
     *                     property="email",
     *                     type="string"
     *                 ),
     *                 @SWG\Property(
     *                     property="phone",
     *                     type="string"
     *                 ),
     *             ),
     *         ),
     *     ),
     *     @SWG\Response(
     *         response="401",
     *         description="Unauthorized user",
     *     ),
     * )
     *
     * @param AboutMeTransformerInterface $aboutMeTransformer
     *
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function getProfile(AboutMeTransformerInterface $aboutMeTransformer): JsonResponse
    {
        $this->authorize('appAuth', User::class);
        /* @var User $user */
        $user = Auth::user();

        return $this->responsePrepare($user, $aboutMeTransformer);
    }

    /**
     * @SWG\Get(
     *     path="/about/settings",
     *     summary="Get Settings",
     *     tags={"About Me"},
     *     security={{"apiToken": {}}},
     *     @SWG\Response(
     *         response=200,
     *         description="Successful operation",
     *         @SWG\Schema(
     *             @SWG\Property(
     *                 property="data",
     *                 ref="#/definitions/UserSettings"
     *             ),
     *         ),
     *     ),
     *     @SWG\Response(
     *         response="401",
     *         description="Unauthorized user",
     *     ),
     * )
     *
     * @param SettingTransformer $transformer
     * @param AboutMeSettingsManager $aboutMeSettingsManager
     *
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function getSettings(
        SettingTransformer $transformer,
        AboutMeSettingsManager $aboutMeSettingsManager
    ): JsonResponse {
        $this->authorize('appAuth', User::class);
        /** @var User $user */
        $user = Auth::user();

        $aboutMeSettings = $aboutMeSettingsManager->getByUser($user);

        return new JsonResponse(fractal($aboutMeSettings, $transformer)->toArray());
    }

    /**
     * @SWG\Patch(
     *     path="/about/settings",
     *     summary="Update Settings",
     *     tags={"About Me"},
     *     security={{"apiToken": {}}},
     *     @SWG\Parameter(
     *         name="body",
     *         in="body",
     *         required=true,
     *         @SWG\Schema(
     *             ref="#/definitions/UserSettings"
     *         )
     *     ),
     *     @SWG\Response(
     *         response=200,
     *         description="Successful operation",
     *         @SWG\Schema(
     *             @SWG\Property(
     *                 property="data",
     *                 ref="#/definitions/UserSettings"
     *             ),
     *         ),
     *     ),
     *     @SWG\Response(
     *         response="401",
     *         description="Unauthorized user",
     *     ),
     * )
     *
     * @param EditRequest $request
     * @param AboutMeSettingsManager $aboutMeSettingsManager
     * @param UserManager $userManager
     * @param SettingTransformer $transformer
     *
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function updateSettings(
        EditRequest $request,
        AboutMeSettingsManager $aboutMeSettingsManager,
        UserManager $userManager,
        SettingTransformer $transformer
    ): JsonResponse {
        $this->authorize('appAuth', User::class);
        /** @var User $user */
        $user = Auth::user();

        $aboutMeSettings = $aboutMeSettingsManager->update($user, $request->validated());

        if ($request->get('tip_time')) {
            $userManager->update($user, ['tip_time' => Carbon::createFromFormat('h:i A', $request->get('tip_time'))->format('H:i:s')]);
        }

        return new JsonResponse(fractal($aboutMeSettings, $transformer)->toArray());
    }

    /**
     * @SWG\Patch(
     *     path="/about/share",
     *     summary="Update ShareActivty Setting",
     *     tags={"About Me"},
     *     security={{"apiToken": {}}},
     *     @SWG\Response(
     *         response=200,
     *         description="Successful operation",
     *         @SWG\Schema(
     *             @SWG\Property(
     *                 property="data",
     *                 ref="#/definitions/UserSettings"
     *             ),
     *         ),
     *     ),
     *     @SWG\Response(
     *         response="401",
     *         description="Unauthorized user",
     *     ),
     * )
     *
     * @param AboutMeSettingsManager $aboutMeSettingsManager
     * @param SettingTransformer $transformer
     *
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function toggleShareActivitySetting(
        AboutMeSettingsManager $aboutMeSettingsManager,
        SettingTransformer $transformer
    ): JsonResponse {
        $this->authorize('appAuth', User::class);
        /** @var User $user */
        $user = Auth::user();

        $aboutMeSettings = $aboutMeSettingsManager->toggleShareActivity($user);

        return new JsonResponse(fractal($aboutMeSettings, $transformer)->toArray());
    }

    /**
     * @SWG\Get(
     *     path="/about/photo",
     *     summary="Get Profile Photo",
     *     tags={"About Me"},
     *     security={{"apiToken": {}}},
     *     @SWG\Response(
     *         response=200,
     *         description="Successful operation",
     *         @SWG\Schema(
     *             @SWG\Property(
     *                 property="data",
     *                 @SWG\Property(
     *                     property="image",
     *                     type="string"
     *                 ),
     *             ),
     *         ),
     *     ),
     *     @SWG\Response(
     *         response="401",
     *         description="Unauthorized user",
     *     ),
     * )
     * @throws AuthorizationException
     */
    public function getPhoto(): JsonResponse
    {
        $this->authorize('appAuth', User::class);
        /** @var User $user */
        $user = Auth::user();

        return new JsonResponse([
            'data' => [
                'image' => $user->avatar ? Storage::disk('avatars')->url($user->avatar) : null,
            ],
        ]);
    }

    /**
     * @SWG\Post(
     *     path="/about/photo",
     *     summary="Set Profile Photo",
     *     tags={"About Me"},
     *     security={{"apiToken": {}}},
     *     @SWG\Parameter(
     *         name="body",
     *         in="body",
     *         required=true,
     *         @SWG\Schema(
     *             @SWG\Property(
     *                 property="image",
     *                 type="string",
     *                 description="Basse64 format"
     *             )
     *         )
     *     ),
     *     @SWG\Response(
     *         response=200,
     *         description="Successful operation",
     *         @SWG\Schema(
     *             @SWG\Property(
     *                 property="data",
     *                 @SWG\Property(
     *                     property="image",
     *                     type="string"
     *                 ),
     *             ),
     *         ),
     *     ),
     *     @SWG\Response(
     *         response="401",
     *         description="Unauthorized user",
     *     ),
     * )
     * @param AvatarRequest $request
     * @param UserManager $userManager
     * @param ImageConverter $imageConverter
     *
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function postPhoto(AvatarRequest $request, UserManager $userManager, ImageConverter $imageConverter): JsonResponse
    {
        $this->authorize('appAuth', User::class);
        /** @var User $user */
        $user = Auth::user();

        $key = md5($request->get('image'));
        $tempPath = '/tmp/'.$key;
        $image = $imageConverter->base64ToImage($request->get('image'), $tempPath);

        $file = new File($image);
        $filePath = Storage::disk('avatars')->putFile('avatars', $file);

        $userManager->setAvatar($user, $filePath);

        return new JsonResponse([
            'data' => [
                'image' => Storage::disk('avatars')->url($user->avatar),
            ],
        ]);
    }

    /**
     * @SWG\Get(
     *     path="/about/day-streak",
     *     summary="Get Profile Photo",
     *     tags={"About Me"},
     *     operationId="getDayStreak",
     *     security={{"apiToken": {}}},
     *     @SWG\Response(
     *         response=200,
     *         description="Successful operation",
     *         @SWG\Schema(
     *             @SWG\Property(
     *                 property="data",
     *                 @SWG\Property(
     *                     property="image",
     *                     type="string"
     *                 ),
     *                 @SWG\Property(
     *                     property="name",
     *                     type="string"
     *                 ),
     *                 @SWG\Property(
     *                     property="day_streak",
     *                     type="integer"
     *                 ),
     *                 @SWG\Property(
     *                     property="sil_score",
     *                     type="integer"
     *                 ),
     *             ),
     *         ),
     *     ),
     *     @SWG\Response(
     *         response="401",
     *         description="Unauthorized user",
     *     ),
     * )
     * @param ConsecutiveDaysService $consecutiveDaysService
     * @param SilScoreService $silScoreService
     *
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function getDayStreak(ConsecutiveDaysService $consecutiveDaysService, SilScoreService $silScoreService): JsonResponse
    {
        $this->authorize('appAuth', User::class);
        /** @var User $user */
        $user = Auth::user();

        $consecutiveDaysService->processConsecutiveDays($user);
        $bonus = $silScoreService->checkConsecutiveDays($user);

        return new JsonResponse([
            'data' => [
                'image' => $user->avatar !== null ? Storage::disk('avatars')->url($user->avatar) : null,
                'name' => $user->name,
                'first_name' => $user->first_name,
                'last_name' => $user->last_name,
                'day_streak' =>  $bonus,
                'sil_score' => $user->sil_score,
            ],
        ]);
    }

    /**
     *
     * @SWG\Put(
     *     path="/about/profile",
     *     summary="Update profile. All properties is required.",
     *     tags={"About Me"},
     *     security={{"apiToken": {}}},
     *     @SWG\Parameter(
     *         name="body",
     *         description="User body",
     *         in="body",
     *         @SWG\Schema(ref="#/definitions/UserBody"),
     *         required=true,
     *     ),
     *     @SWG\Response(
     *         response=200,
     *         description="User was successfully updated",
     *         @SWG\Schema(
     *             type="object",
     *             @SWG\Property(
     *                 property="data",
     *                 type="array",
     *                 @SWG\Items(
     *                     ref="#/definitions/AboutMeUser"
     *                 ),
     *             ),
     *         ),
     *     ),
     *     @SWG\Response(
     *         response="401",
     *         description="Unauthorized user",
     *     ),
     *     @SWG\Response(
     *         response="403",
     *         description="Unauthorized action",
     *     ),
     *     @SWG\Response(
     *         response="400",
     *         description="Validation Error",
     *         @SWG\Schema(ref="#/definitions/Validation Error")
     *     ),
     * )
     *
     * @param UpdateRequest $request
     * @param UserManager $userManager
     * @param AboutMeTransformerInterface $aboutMeTransformer
     *
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function update(UpdateRequest $request, UserManager $userManager, AboutMeTransformerInterface $aboutMeTransformer): JsonResponse
    {
        $this->authorize('appAuth', User::class);
        /** @var User $user */
        $user = Auth::user();
        $userManager->update($user, $request->validated());

        return $this->responsePrepare($user, $aboutMeTransformer);
    }

    /**
     * @SWG\Patch(
     *     path="/about/profile",
     *     summary="Update profile.",
     *     tags={"About Me"},
     *     security={{"apiToken": {}}},
     *     @SWG\Parameter(
     *         name="body",
     *         description="User body",
     *         in="body",
     *         @SWG\Schema(ref="#/definitions/UserBody"),
     *         required=true,
     *     ),
     *     @SWG\Response(
     *         response=200,
     *         description="User was successfully updated",
     *         @SWG\Schema(
     *             type="object",
     *             @SWG\Property(
     *                 property="data",
     *                 type="array",
     *                 @SWG\Items(
     *                     ref="#/definitions/AboutMeUser"
     *                 ),
     *             ),
     *         ),
     *     ),
     *     @SWG\Response(
     *         response="401",
     *         description="Unauthorized user",
     *     ),
     *     @SWG\Response(
     *         response="403",
     *         description="Unauthorized action",
     *     ),
     *     @SWG\Response(
     *         response="400",
     *         description="Validation Error",
     *         @SWG\Schema(ref="#/definitions/Validation Error")
     *     ),
     * )
     *
     * @param UpdatePatchRequest $request
     * @param UserManager $userManager
     * @param AboutMeTransformerInterface $aboutMeTransformer
     *
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function updatePatch(UpdatePatchRequest $request, UserManager $userManager, AboutMeTransformerInterface $aboutMeTransformer): JsonResponse
    {
        $this->authorize('appAuth', User::class);
        /** @var User $user */
        $user = Auth::user();
        $userManager->update($user, $request->validated());

        return $this->responsePrepare($user, $aboutMeTransformer);
    }

    /**
     * @param User $user
     * @param AboutMeTransformerInterface $aboutMeTransformer
     * @return JsonResponse
     */
    protected function responsePrepare(User $user, AboutMeTransformerInterface $aboutMeTransformer) : JsonResponse
    {
        return new JsonResponse(fractal($user, $aboutMeTransformer)->toArray());
    }

    /**
     * @SWG\Get(
     *     path="/about/bookmarked-content",
     *     summary="Get bookmarked Content Items for Current User",
     *     security={{"apiToken": {}}},
     *     tags={"About Me"},
     *     @SWG\Parameter(
     *         name="focus_id",
     *         description="Filter by Focus ID",
     *         type="integer",
     *         in="query"
     *     ),
     *     @SWG\Response(
     *         response=200,
     *         description="List of Content Items",
     *         @SWG\Schema(
     *             allOf={
     *                 @SWG\Schema(ref="#/definitions/OffsetLimitData"),
     *                 @SWG\Schema(
     *                     @SWG\Property(
     *                         property="data",
     *                         @SWG\Items(ref="#/definitions/ContentItem")
     *                     )
     *                 )
     *             }
     *         )
     *     ),
     * )
     *
     * @param ListRequest $request
     * @param ContentItemTransformerInterface $transformer
     * @param ListResponseInterface $response
     * @param CutFocusTransformerInterface $focusTransformer
     *
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function bookmarkedContent(ListRequest $request, ContentItemTransformerInterface $transformer, ListResponseInterface $response, CutFocusTransformerInterface $focusTransformer): JsonResponse
    {
        $this->authorize('appAuth', User::class);
        /** @var User $user */
        $user = User::find($request->get('user_id')) ?? Auth::user();

        $query = ContentItem::bookmarked($user);

        if ($request->get('focus_id')) {
            $response->setWhere(['focus_id', $request->get('focus_id')]);
        }

        $focuses = Focus::with('contentItems')->whereIn('id', $query->get()->pluck('focus_id'))->get();

        return $response
            ->setBuilder($query)
            ->setOffset($request->getOffset())
            ->setLimit($request->getLimit())
            ->setTransformer($transformer)
            ->addValue('focuses', fractal($focuses, $focusTransformer)->toArray()['data'])
            ->getResponse();
    }
}
