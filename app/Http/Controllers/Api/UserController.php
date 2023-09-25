<?php
declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\ApiController;
use App\Http\Requests\Api\User\CreateRequest;
use App\Http\Requests\Api\User\ImportRequest;
use App\Http\Requests\Api\User\UpdateRequest;
use App\Http\Requests\ListRequest;
use App\Http\Responses\ListResponseInterface;
use App\Imports\UserImport;
use App\Manager\UserManager;
use App\Models\Client;
use App\Models\User;
use App\Transformers\User\UserTransformerInterface;
use App\Transformers\UserReflection\UserReflectionTransformerInterface;
use Auth;
use Exception;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Maatwebsite\Excel\Facades\Excel;
use Swagger\Annotations as SWG;

class UserController extends ApiController
{
    /**
     * @SWG\Get(
     *     path="/users",
     *     summary="Get list of Users",
     *     tags={"Users"},
     *     operationId="showAll",
     *     security={{"apiToken": {}}},
     *     @SWG\Parameter(
     *         name="offset",
     *         type="integer",
     *         in="query"
     *     ),
     *     @SWG\Parameter(
     *         name="limit",
     *         type="integer",
     *         in="query"
     *     ),
     *     @SWG\Response(
     *         response=200,
     *         description="Successful operation",
     *         @SWG\Schema(
     *             type="array",
     *             @SWG\Items(ref="#/definitions/User")
     *         ),
     *     ),
     *     @SWG\Response(
     *         response="401",
     *         description="Unauthorized user",
     *     ),
     * )
     * @param ListRequest $request
     * @param UserTransformerInterface $transformer
     *
     * @param ListResponseInterface $response
     *
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function index(ListRequest $request, UserTransformerInterface $transformer, ListResponseInterface $response): JsonResponse
    {
        $this->authorize('list', User::class);

        /** @var User $user */
        $user = Auth::user();
        $builder = User::ofUser($user);

        return $response
            ->setBuilder($builder)
            ->setTransformer($transformer)
            ->setOffset($request->getOffset())
            ->setLimit($request->getLimit())
            ->getResponse();
    }

    /**
     * @SWG\Post(
     *     path="/users",
     *     summary="Create User",
     *     tags={"Users"},
     *     security={{"apiToken": {}}},
     *     @SWG\Parameter(
     *         name="body",
     *         in="body",
     *         required=true,
     *         @SWG\Schema(
     *             @SWG\Property(
     *                 property="first_name",
     *                 type="string"
     *             ),
     *             @SWG\Property(
     *                 property="last_name",
     *                 type="string"
     *             ),
     *             @SWG\Property(
     *                 property="email",
     *                 type="string"
     *             ),
     *             @SWG\Property(
     *                 property="password",
     *                 type="string"
     *             ),
     *             @SWG\Property(
     *                 property="job_role",
     *                 type="string"
     *             ),
     *             @SWG\Property(
     *                 property="job_dept",
     *                 type="string"
     *             ),
     *             @SWG\Property(
     *                 property="permission",
     *                 type="integer",
     *                 description="1, 2, 3, 4"
     *             ),
     *         )
     *     ),
     *     @SWG\Response(
     *         response=200,
     *         description="Successful operation",
     *         @SWG\Schema(
     *             @SWG\Property(
     *                 property="data",
     *                 ref="#/definitions/User"
     *             )
     *         ),
     *     ),
     *     @SWG\Response(
     *         response="401",
     *         description="Unauthorized user",
     *     ),
     * )
     *
     * @param CreateRequest $request
     * @param UserManager $userManager
     * @param UserTransformerInterface $transformer
     *
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function create(CreateRequest $request, UserManager $userManager, UserTransformerInterface $transformer): JsonResponse
    {
        $this->authorize('create', User::class);

        $client = Client::find($request->get('client_id'));

        $user = $userManager->create(
            $request->get('email'),
            $request->get('first_name'),
            $request->get('last_name'),
            $request->get('password'),
            $request->get('job_role'),
            $request->get('job_dept'),
            $request->get('permission'),
            $client
        );

        return new JsonResponse(fractal($user, $transformer)->toArray());
    }

    /**
     * @SWG\Get(
     *     path="/users/{id}",
     *     summary="Get User data",
     *     security={{"apiToken": {}}},
     *     tags={"Users"},
     *     @SWG\Parameter(
     *         name="id",
     *         type="integer",
     *         in="path",
     *         description="User ID"
     *     ),
     *     @SWG\Response(
     *         response="200",
     *         description="Success operation",
     *         @SWG\Schema(
     *             @SWG\Property(
     *                 property="data",
     *                 ref="#/definitions/User"
     *             )
     *         )
     *     )
     * )
     *
     * @param User $user
     * @param UserTransformerInterface $userTransformer
     *
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function show(User $user, UserTransformerInterface $userTransformer): JsonResponse
    {
        $this->authorize('view', $user);

        return new JsonResponse(fractal($user, $userTransformer)->toArray());
    }

    /**
     * @SWG\Put(
     *     path="/users/{id}",
     *     summary="Edit User",
     *     tags={"Users"},
     *     security={{"apiToken": {}}},
     *     @SWG\Parameter(
     *         name="id",
     *         type="integer",
     *         description="User ID",
     *         in="path"
     *     ),
     *     @SWG\Parameter(
     *         name="body",
     *         in="body",
     *         required=true,
     *         @SWG\Schema(
     *             required={"first_name", "last_name", "job_role", "job_dept", "permission"},
     *             @SWG\Property(
     *                 property="first_name",
     *                 type="string"
     *             ),
     *             @SWG\Property(
     *                 property="last_name",
     *                 type="string"
     *             ),
     *             @SWG\Property(
     *                 property="job_role",
     *                 type="string"
     *             ),
     *             @SWG\Property(
     *                 property="job_dept",
     *                 type="string"
     *             ),
     *             @SWG\Property(
     *                 property="permission",
     *                 type="integer",
     *                 description="1 - LI_ADMIN, 2 - LI_EDITOR, 3 - CLIENT_ADMIN, 4 - USER_APP"
     *             ),
     *             @SWG\Property(
     *                 property="client_id",
     *                 type="string"
     *             ),
     *         )
     *     ),
     *     @SWG\Response(
     *         response=200,
     *         description="Successful operation",
     *         @SWG\Schema(
     *             @SWG\Property(
     *                 property="data",
     *                 ref="#/definitions/User"
     *             )
     *         ),
     *     ),
     *     @SWG\Response(
     *         response="401",
     *         description="Unauthenticated user",
     *     ),
     *     @SWG\Response(
     *         response="403",
     *         description="Unauthorized action",
     *     ),
     *     @SWG\Response(
     *         response="422",
     *         description="Validation error",
     *     ),
     * )
     *
     * @param UpdateRequest $request
     * @param User $user
     * @param UserManager $userManager
     * @param UserTransformerInterface $transformer
     *
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function edit(UpdateRequest $request, User $user, UserManager $userManager, UserTransformerInterface $transformer): JsonResponse
    {
        $this->authorize('update', $user);

        $client = Client::find($request->getClientId());

        $userManager->update($user, $request->validated(), $client);

        return new JsonResponse(fractal($user, $transformer)->toArray());
    }

    /**
     * @SWG\Delete(
     *     path="/users/{id}",
     *     security={{"apiToken": {}}},
     *     tags={"Users"},
     *     summary="Delete User",
     *     @SWG\Parameter(
     *         name="id",
     *         description="User ID",
     *         type="integer",
     *         in="path"
     *     ),
     *     @SWG\Response(
     *         response="200",
     *         description="Successful operation"
     *     ),
     *     @SWG\Response(
     *         response="402",
     *         description="Unauthenticated"
     *     ),
     *     @SWG\Response(
     *         response="403",
     *         description="Action is unauthorized"
     *     ),
     *     @SWG\Response(
     *         response="404",
     *         description="User not found"
     *     ),
     * )
     *
     * @param User $user
     * @param UserManager $userManager
     *
     * @return JsonResponse
     * @throws Exception
     */
    public function remove(User $user, UserManager $userManager): JsonResponse
    {
        $status = $userManager->delete($user);

        return new JsonResponse($status ? ['message' => 'Success operation'] : ['error' => 'Aborted']);
    }

    /**
     * @SWG\Get(
     *     path="/users/{id}/reflections",
     *     tags={"Users"},
     *     security={{"apiToken": {}}},
     *     summary="Get list of users reflections",
     *     @SWG\Parameter(
     *         in="path",
     *         type="integer",
     *         name="id",
     *         description="User ID",
     *         required=true
     *     ),
     *     @SWG\Response(
     *         response=200,
     *         description="Successful operation",
     *         @SWG\Schema(
     *             type="object",
     *             allOf={
     *                 @SWG\Schema(ref="#/definitions/OffsetLimitData"),
     *                 @SWG\Schema(
     *                     @SWG\Property(
     *                         property="user",
     *                         ref="#/definitions/User"
     *                     )
     *                 ),
     *                 @SWG\Schema(
     *                     @SWG\Property(
     *                         property="data",
     *                         @SWG\Items(ref="#/definitions/Reflection")
     *                     )
     *                 )
     *             },
     *         ),
     *     ),
     * )
     *
     * @param ListRequest $request
     * @param User $user
     * @param UserReflectionTransformerInterface $userReflectionTransformer
     *
     * @param UserTransformerInterface $userTransformer
     *
     * @param ListResponseInterface $response
     *
     * @return JsonResponse
     */
    public function reflections(
        ListRequest $request,
        User $user,
        UserReflectionTransformerInterface $userReflectionTransformer,
        UserTransformerInterface $userTransformer,
        ListResponseInterface $response
    ): JsonResponse {
        $reflections = $user->reflections()->offset($request->getOffset())->limit($request->getLimit())->get();

        return $response
            ->setBuilder($user->reflections())
            ->setData($reflections)
            ->setTransformer($userReflectionTransformer)
            ->addValue('user', fractal($user, $userTransformer)->toArray()['data'])
            ->getResponse();
    }

    /**
     * @SWG\Post(
     *     path="/users/import",
     *     summary="Import Users from CSV",
     *     security={{"apiToken": {}}},
     *     tags={"Users"},
     *     @SWG\Parameter(
     *         name="body",
     *         in="body",
     *         required=true,
     *         @SWG\Schema(
     *             @SWG\Property(
     *                 property="base_64",
     *                 type="string"
     *             )
     *         )
     *     ),
     *     @SWG\Response(
     *         response=200,
     *         description="Successful operation",
     *     ),
     *     @SWG\Response(
     *         response="401",
     *         description="Unauthorized user",
     *     ),
     * )
     *
     * @param ImportRequest $request
     *
     * @return JsonResponse
     * @throws Exception
     */
    public function import(ImportRequest $request): JsonResponse
    {
        $fileName = random_int(1, 100).date('Y-m-d H:i:s');
        $outputFile = '/tmp/'.$fileName.'.csv';

        $base64String = $request->get('base_64');

        $file = fopen($outputFile, 'wb');

        if (strpos($base64String, ',') !== false) {
            $base64String = explode(',', $base64String)[1];
        }

        fwrite($file, base64_decode($base64String));
        fclose($file);

        Excel::import(new UserImport(), $outputFile);

        return new JsonResponse();
    }
}
