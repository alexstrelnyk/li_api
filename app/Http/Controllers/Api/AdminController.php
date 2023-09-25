<?php
declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\ApiController;
use App\Http\Requests\Api\Admin\CreateRequest;
use App\Http\Requests\Api\Admin\UpdateRequest;
use App\Http\Requests\ListRequest;
use App\Http\Responses\JsonTransformedResponse;
use App\Http\Responses\ListResponseInterface;
use App\Manager\AdminManager;
use App\Models\User;
use App\Transformers\Admin\AdminTransformer;
use Auth;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;

class AdminController extends ApiController
{
    /**
     * @SWG\Get(
     *     path="/admins",
     *     summary="Get list of Users",
     *     tags={"Admins"},
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
     *     ),
     *     @SWG\Response(
     *         response="401",
     *         description="Unauthorized user",
     *     ),
     * )
     * @param ListRequest $request
     * @param AdminTransformer $transformer
     *
     * @param ListResponseInterface $response
     *
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function index(ListRequest $request, AdminTransformer $transformer, ListResponseInterface $response): JsonResponse
    {
        $this->authorize('list', User::class);

        /** @var User $user */
        $user = Auth::user();
        $builder = User::ofUser($user)->whereIn('permission', [User::LI_ADMIN, User::LI_CONTENT_EDITOR]);

        return $response
            ->setBuilder($builder)
            ->setTransformer($transformer)
            ->setOffset($request->getOffset())
            ->setLimit($request->getLimit())
            ->getResponse();
    }

    /**
     * @SWG\Post(
     *     path="/admins",
     *     summary="Create new Admin",
     *     tags={"Admins"},
     *     security={{"apiToken": {}}},
     *     @SWG\Parameter(
     *         name="body",
     *         description="Admin Body",
     *         in="body",
     *         @SWG\Schema(
     *             @SWG\Property(
     *                 property="email",
     *                 type="string"
     *             ),
     *             @SWG\Property(
     *                 property="first_name",
     *                 type="string"
     *             ),
     *             @SWG\Property(
     *                 property="last_name",
     *                 type="string"
     *             ),
     *             @SWG\Property(
     *                 property="role",
     *                 type="integer"
     *             ),
     *         ),
     *     ),
     *     @SWG\Response(
     *         response=200,
     *         description="Successful operation",
     *     ),
     *     @SWG\Response(
     *         response="401",
     *         description="Unauthorized user",
     *     ),
     *     @SWG\Response(
     *         response="400",
     *         description="Validation Error",
     *         @SWG\Schema(ref="#/definitions/Validation Error")
     *     ),
     * )
     *
     * @param CreateRequest $request
     * @param AdminTransformer $transformer
     * @param AdminManager $adminManager
     *
     * @return JsonResponse
     */
    public function create(CreateRequest $request, AdminTransformer $transformer, AdminManager $adminManager): JsonResponse
    {
        $admin = $adminManager->create($request->getEmail(), $request->getFirstName(), $request->getLastName(), $request->getRole());

        return new JsonTransformedResponse($admin, $transformer);
    }

    /**
     * @SWG\Put(
     *     path="/admins/{id}",
     *     summary="Update Admin",
     *     tags={"Admins"},
     *     security={{"apiToken": {}}},
     *     @SWG\Parameter(
     *         name="id",
     *         type="integer",
     *         description="Admin ID",
     *         in="path",
     *         required=true
     *     ),
     *     @SWG\Parameter(
     *         name="body",
     *         description="Admin Body",
     *         in="body",
     *         @SWG\Schema(
     *             @SWG\Property(
     *                 property="email",
     *                 type="string"
     *             ),
     *             @SWG\Property(
     *                 property="first_name",
     *                 type="string"
     *             ),
     *             @SWG\Property(
     *                 property="last_name",
     *                 type="string"
     *             ),
     *             @SWG\Property(
     *                 property="role",
     *                 type="integer"
     *             ),
     *         ),
     *     ),
     *     @SWG\Response(
     *         response=200,
     *         description="Successful operation",
     *     ),
     *     @SWG\Response(
     *         response="401",
     *         description="Unauthorized user",
     *     ),
     *     @SWG\Response(
     *         response="400",
     *         description="Validation Error",
     *         @SWG\Schema(ref="#/definitions/Validation Error")
     *     ),
     * )
     *
     * @param UpdateRequest $request
     * @param AdminTransformer $transformer
     * @param User $user
     * @param AdminManager $adminManager
     *
     * @return JsonResponse
     */
    public function update(UpdateRequest $request, User $user, AdminTransformer $transformer, AdminManager $adminManager): JsonResponse
    {
        $adminManager->update($user, array_merge($request->all(['email', 'first_name', 'last_name'], ['permission' => $request->getRole()])));

        return new JsonTransformedResponse($user, $transformer);
    }

    /**
     * @SWG\Delete(
     *     path="/admins/{id}",
     *     summary="Delete Admin",
     *     tags={"Admins"},
     *     security={{"apiToken": {}}},
     *     @SWG\Parameter(
     *         name="id",
     *         type="integer",
     *         description="Admin ID",
     *         in="path",
     *         required=true
     *     ),
     *     @SWG\Response(
     *         response=204,
     *         description="No Content",
     *     ),
     *     @SWG\Response(
     *         response="401",
     *         description="Unauthorized user",
     *     ),
     * )
     *
     * @param User $user
     * @param AdminManager $adminManager
     *
     * @return JsonResponse
     */
    public function delete(User $user, AdminManager $adminManager): JsonResponse
    {
        $adminManager->delete($user);

        return new JsonResponse(null, 204);
    }
}
