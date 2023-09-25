<?php
declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\ApiController;
use App\Http\Requests\Api\InviteEmail\AssignRequest;
use App\Http\Requests\Api\InviteEmail\AssignToMeRequest;
use App\Http\Requests\Api\InviteEmail\SendInviteRequest;
use App\Http\Requests\ListRequest;
use App\Http\Responses\ListResponseInterface;
use App\Manager\InviteEmailManager;
use App\Manager\UserManager;
use App\Models\Client;
use App\Models\InviteEmail;
use App\Services\Api\InviteEmailService;
use App\Transformers\InviteEmail\InviteEmailTransformerInterface;
use App\Transformers\User\UserTransformerInterface;
use Auth;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Swagger\Annotations as SWG;

class InviteEmailController extends ApiController
{
    /**
     * @SWG\Get(
     *     path="/invite",
     *     security={{"apiToken": {}}},
     *     summary="Get list of invited emails",
     *     tags={"Invite Emails"},
     *     @SWG\Parameter(
     *         name="offset",
     *         type="integer",
     *         description="Offset",
     *         in="query",
     *     ),
     *     @SWG\Parameter(
     *         name="limit",
     *         type="integer",
     *         description="Limit",
     *         in="query",
     *     ),
     *     @SWG\Response(
     *         response="200",
     *         description="Success operation",
     *         @SWG\Schema(
     *             allOf={
     *                 @SWG\Schema(ref="#/definitions/OffsetLimitData"),
     *                 @SWG\Schema(
     *                     @SWG\Property(
     *                         property="data",
     *                         @SWG\Items(ref="#/definitions/InviteEmail")
     *                     )
     *                 )
     *             }
     *         )
     *     ),
     *     @SWG\Response(
     *         response="401",
     *         description="Unauthorized user",
     *     ),
     *     @SWG\Response(
     *         response="403",
     *         description="Unauthorized action",
     *     ),
     * )
     *
     * @param ListRequest $request
     * @param InviteEmailTransformerInterface $transformer
     * @param ListResponseInterface $response
     * @return JsonResponse
     */
    public function index(ListRequest $request, InviteEmailTransformerInterface $transformer, ListResponseInterface $response): JsonResponse
    {
        // $this->authorize('viewAny', InviteEmail::class);

        return $response
            ->setBuilder(InviteEmail::query())
            ->setOffset($request->getOffset())
            ->setLimit($request->getLimit())
            ->setTransformer($transformer)
            ->getResponse();
    }

    /**
     * @SWG\Post(
     *     path="/invite/assign",
     *     summary="Create user and assign to client",
     *     description="Create user from passed InviteEmail and assign to passed Client. Can perform only LI Admin",
     *     security={{"apiToken": {}}},
     *     tags={"Invite Emails"},
     *     @SWG\Parameter(
     *         name="body",
     *         in="body",
     *         @SWG\Schema(
     *             @SWG\Property(
     *                 property="invite_email_id",
     *                 type="integer",
     *             ),
     *             @SWG\Property(
     *                 property="client_id",
     *                 type="integer",
     *             ),
     *         ),
     *     ),
     *     @SWG\Response(
     *         response="201",
     *         description="Created User",
     *         @SWG\Schema(
     *             @SWG\Property(
     *                 property="data",
     *                 ref="#/definitions/User"
     *             )
     *         )
     *     ),
     *     @SWG\Response(
     *         response="401",
     *         description="Unauthorized user",
     *     ),
     *     @SWG\Response(
     *         response="402",
     *         description="Validation Error",
     *         @SWG\Schema(ref="#/definitions/Validation Error")
     *     ),
     *     @SWG\Response(
     *         response="403",
     *         description="Unauthorized action",
     *     ),
     * )
     *
     * @param AssignRequest $request
     * @param UserTransformerInterface $transformer
     * @param InviteEmailManager $inviteEmailManager
     * @param UserManager $userManager
     *
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function assign(
        AssignRequest $request,
        UserTransformerInterface $transformer,
        InviteEmailManager $inviteEmailManager,
        UserManager $userManager
    ): JsonResponse {
        $inviteEmail = InviteEmail::findOrFail($request->get('invite_email_id'));

        // $this->authorize('assign', $inviteEmail);

        $client = Client::findOrFail($request->get('client_id'));
        $user = $inviteEmailManager->createUserFromInviteEmail($inviteEmail);
        $userManager->assignToClient($user, $client);

        return new JsonResponse(fractal($user, $transformer)->toArray());
    }

    /**
     * @SWG\Post(
     *     path="/invite/assign-to-me",
     *     summary="Create user and assign to current client",
     *     description="Create user from passed InviteEmail and assign to current client. Can perform only Client Admin",
     *     security={{"apiToken": {}}},
     *     tags={"Invite Emails"},
     *     @SWG\Parameter(
     *         name="body",
     *         in="body",
     *         @SWG\Schema(
     *             @SWG\Property(
     *                 property="invite_email_id",
     *                 type="integer",
     *             ),
     *         ),
     *     ),
     *     @SWG\Response(
     *         response="201",
     *         description="Created User",
     *         @SWG\Schema(
     *             @SWG\Property(
     *                 property="data",
     *                 ref="#/definitions/User"
     *             )
     *         )
     *     ),
     *     @SWG\Response(
     *         response="401",
     *         description="Unauthorized user",
     *     ),
     *     @SWG\Response(
     *         response="402",
     *         description="Validation Error",
     *         @SWG\Schema(ref="#/definitions/Validation Error")
     *     ),
     *     @SWG\Response(
     *         response="403",
     *         description="Unauthorized action",
     *     ),
     * )
     *
     * @param AssignToMeRequest $request
     * @param UserTransformerInterface $transformer
     * @param InviteEmailManager $inviteEmailManager
     * @param UserManager $userManager
     *
     * @return JsonResponse
     */
    public function assignToMe(
        AssignToMeRequest $request,
        UserTransformerInterface $transformer,
        InviteEmailManager $inviteEmailManager,
        UserManager $userManager
    ): JsonResponse {
        $inviteEmail = InviteEmail::findOrFail($request->get('invite_email_id'));

        // $this->authorize('assignToMe', $inviteEmail);

        $client = Auth::user()->client;
        $user = $inviteEmailManager->createUserFromInviteEmail($inviteEmail);
        $userManager->assignToClient($user, $client);

        return new JsonResponse(fractal($user, $transformer)->toArray());
    }

    /**
     * @SWG\Post(
     *     path="/invite/send",
     *     summary="Send invite email to requested email address",
     *     tags={"Invite Emails"},
     *     @SWG\Parameter(
     *         name="body",
     *         description="Send invite email to requested email address Body",
     *         in="body",
     *         @SWG\Schema(ref="#/definitions/InviteEmailBody"),
     *         required=true,
     *     ),
     *     @SWG\Response(
     *         response=200,
     *         description="Email was been sent",
     *         @SWG\Schema(
     *             type="object",
     *             @SWG\Property(
     *                 property="message",
     *                 type="success operation",
     *             ),
     *         ),
     *     ),
     *     @SWG\Response(
     *         response="400",
     *         description="Validation Error",
     *         @SWG\Schema(ref="#/definitions/Validation Error")
     *     ),
     * )
     *
     * @param SendInviteRequest $request
     * @param InviteEmailService $inviteEmailService
     *
     * @return JsonResponse
     */
    public function sendInviteMagicToken(SendInviteRequest $request, InviteEmailService $inviteEmailService): JsonResponse
    {
        $response = $inviteEmailService->sendInvite($request->getEmail());

        return response()->json($response->getData(), $response->getStatus());
    }

    /**
     * @SWG\Post(
     *     path="/invite/get",
     *     summary="Get invite token",
     *     tags={"Invite Emails"},
     *     @SWG\Parameter(
     *         name="body",
     *         description="Get invite token for authorize",
     *         in="body",
     *         @SWG\Schema(ref="#/definitions/InviteEmailBody"),
     *         required=true,
     *     ),
     *     @SWG\Response(
     *         response=200,
     *         description="Successful operation",
     *         @SWG\Schema(
     *             type="object",
     *             @SWG\Property(
     *                 property="message",
     *                 type="string",
     *             ),
     *         ),
     *     ),
     *     @SWG\Response(
     *         response="400",
     *         description="Validation Error",
     *         @SWG\Schema(ref="#/definitions/Validation Error")
     *     ),
     * )
     *
     * @param SendInviteRequest $request
     * @param InviteEmailService $inviteEmailService
     *
     * @return JsonResponse
     */
    public function getInviteMagicToken(SendInviteRequest $request, InviteEmailService $inviteEmailService): JsonResponse
    {
        $response = $inviteEmailService->getInvite($request->getEmail());

        return response()->json($response->getData(), $response->getStatus());
    }
}
