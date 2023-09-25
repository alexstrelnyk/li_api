<?php
declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\ApiController;
use App\Http\Requests\Api\Client\CreateRequest;
use App\Http\Requests\Api\Client\UpdateRequest;
use App\Http\Requests\ListRequest;
use App\Http\Responses\JsonTransformedResponse;
use App\Http\Responses\ListResponseInterface;
use App\Manager\ClientManager;
use App\Models\Client;
use App\Models\Focus;
use App\Models\User;
use App\Transformers\Client\ClientTransformerInterface;
use App\Transformers\Focus\FocusTransformerInterface;
use App\Transformers\User\UserTransformerInterface;
use Exception;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Swagger\Annotations as SWG;

class ClientController extends ApiController
{
    /**
     * @SWG\Get(
     *     path="/clients",
     *     security={{"apiToken": {}}},
     *     summary="Get clients list",
     *     tags={"Clients"},
     *     @SWG\Response(
     *         response="200",
     *         description="Success opeartion",
     *         @SWG\Schema(
     *             allOf={
     *                 @SWG\Schema(ref="#/definitions/OffsetLimitData"),
     *                 @SWG\Schema(
     *                     @SWG\Property(
     *                         property="data",
     *                         @SWG\Items(ref="#/definitions/Client")
     *                     )
     *                 ),
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
     * @param ListRequest $request
     * @param ClientTransformerInterface $transformer
     *
     * @param ListResponseInterface $response
     *
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function index(ListRequest $request, ClientTransformerInterface $transformer, ListResponseInterface $response): JsonResponse
    {
        $this->authorize('list', Client::class);

        return $response->setTransformer($transformer)
            ->setBuilder(Client::query())
            ->setLimit($request->getLimit())
            ->setOffset($request->getOffset())
            ->setSortDirection($request->getSortDirection())
            ->getResponse();
    }

    /**
     * @SWG\Post(
     *     path="/clients",
     *     security={{"apiToken": {}}},
     *     summary="Create Client",
     *     tags={"Clients"},
     *     @SWG\Parameter(
     *         name="body",
     *         in="body",
     *         required=true,
     *         @SWG\Schema(
     *             @SWG\Property(
     *                 property="name",
     *                 type="string",
     *             ),
     *             @SWG\Property(
     *                 property="content_model",
     *                 type="integer",
     *                 description="1 - LI Content only, 2 - Blank, 3 - Mixed content"
     *             ),
     *             @SWG\Property(
     *                 property="primer_notice_timing",
     *                 type="integer"
     *             ),
     *             @SWG\Property(
     *                 property="content_notice_timing",
     *                 type="integer"
     *             ),
     *             @SWG\Property(
     *                 property="reflection_notice_timing",
     *                 type="integer"
     *             ),
     *         )
     *     ),
     *     @SWG\Response(
     *         response="200",
     *         description="Success opeartion",
     *         @SWG\Schema(
     *             @SWG\Property(
     *                 property="data",
     *                 ref="#/definitions/Client"
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
     * @param CreateRequest $request
     * @param ClientManager $clientManager
     * @param ClientTransformerInterface $transformer
     *
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function create(CreateRequest $request, ClientManager $clientManager, ClientTransformerInterface $transformer): JsonResponse
    {
        $this->authorize('create', Client::class);

        $client = $clientManager->create(
            $request->get('name'),
            (int) $request->get('content_model'),
            (int) $request->get('primer_notice_timing'),
            (int) $request->get('content_notice_timing'),
            (int) $request->get('reflection_notice_timing')
        );

        return new JsonTransformedResponse($client, $transformer);
    }

    /**
     * @SWG\Get(
     *     path="/clients/{id}",
     *     summary="Show Client",
     *     tags={"Clients"},
     *     security={{"apiToken": {}}},
     *     @SWG\Parameter(
     *         name="id",
     *         type="integer",
     *         description="Client ID",
     *         in="path",
     *         required=true
     *     ),
     *     @SWG\Response(
     *         response=200,
     *         description="Successful operation",
     *         @SWG\Schema(
     *             @SWG\Property(
     *                 property="data",
     *                 ref="#/definitions/Client"
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
     * @param Client $client
     * @param ClientTransformerInterface $transformer
     *
     * @return JsonResponse
     */
    public function show(Client $client, ClientTransformerInterface $transformer): JsonResponse
    {
        return new JsonTransformedResponse($client, $transformer);
    }

    /**
     * @SWG\Put(
     *     path="/clients/{id}",
     *     summary="Edit Client",
     *     tags={"Clients"},
     *     security={{"apiToken": {}}},
     *     @SWG\Parameter(
     *         name="id",
     *         type="integer",
     *         description="Client ID",
     *         in="path"
     *     ),
     *     @SWG\Parameter(
     *         name="body",
     *         in="body",
     *         required=true,
     *         @SWG\Schema(
     *             @SWG\Property(
     *                 property="name",
     *                 type="string",
     *             ),
     *             @SWG\Property(
     *                 property="content_model",
     *                 type="integer",
     *                 description="1 - LI Content only, 2 - Blank, 3 - Mixed content"
     *             ),
     *             @SWG\Property(
     *                 property="primer_notice_timing",
     *                 type="integer"
     *             ),
     *             @SWG\Property(
     *                 property="content_notice_timing",
     *                 type="integer"
     *             ),
     *             @SWG\Property(
     *                 property="reflection_notice_timing",
     *                 type="integer"
     *             ),
     *         )
     *     ),
     *     @SWG\Response(
     *         response=200,
     *         description="Successful operation",
     *         @SWG\Schema(
     *             @SWG\Property(
     *                 property="data",
     *                 ref="#/definitions/Client"
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
     * @param Client $client
     * @param UpdateRequest $request
     * @param ClientManager $clientManager
     * @param ClientTransformerInterface $transformer
     *
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function update(Client $client, UpdateRequest $request, ClientManager $clientManager, ClientTransformerInterface $transformer): JsonResponse
    {
        $this->authorize('update', $client);

        $clientManager->update($client, $request->validated());

        return new JsonTransformedResponse($client, $transformer);
    }

    /**
     * @SWG\Delete(
     *     path="/clients/{id}",
     *     security={{"apiToken": {}}},
     *     tags={"Clients"},
     *     summary="Delete Client",
     *     @SWG\Parameter(
     *         name="id",
     *         description="Client ID",
     *         type="integer",
     *         in="path"
     *     ),
     *     @SWG\Response(
     *         response="200",
     *         description="Successful operation",
     *         @SWG\Schema(
     *             ref="#/definitions/Client"
     *         )
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
     *         description="Client not found"
     *     ),
     * )
     *
     * @param Client $client
     * @param ClientManager $clientManager
     * @return JsonResponse
     * @throws Exception
     */
    public function delete(Client $client, ClientManager $clientManager): JsonResponse
    {
        $this->authorize('delete', $client);

        if ($client->id === Client::DEFAULT_CLIENT_ID) {
            return new JsonResponse(['message' => 'This client cannot be deleted']);
        }

        $status = $clientManager->delete($client);

        return new JsonResponse($status ? ['message' => 'Success operation'] : ['error' => 'Aborted']);
    }

    /**
     * @SWG\Post(
     *     path="/clients/{id}/activate",
     *     summary="Activate client",
     *     security={{"apiToken": {}}},
     *     tags={"Clients"},
     *     @SWG\Parameter(
     *         name="id",
     *         type="string",
     *         description="Client ID",
     *         in="path"
     *     ),
     *     @SWG\Response(
     *         response="200",
     *         description="Success oparation",
     *         @SWG\Schema(
     *             ref="#/definitions/Client"
     *         )
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
     *         description="Client not found"
     *     ),
     * )
     *
     * @param Client $client
     * @param ClientManager $clientManager
     * @param ClientTransformerInterface $transformer
     *
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function activate(Client $client, ClientManager $clientManager, ClientTransformerInterface $transformer): JsonResponse
    {
        $this->authorize('update', $client);

        $clientManager->activate($client);

        return new JsonTransformedResponse($client, $transformer);
    }

    /**
     * @SWG\Post(
     *     path="/clients/{id}/deactivate",
     *     summary="Deactivate client",
     *     security={{"apiToken": {}}},
     *     tags={"Clients"},
     *     @SWG\Parameter(
     *         name="id",
     *         type="string",
     *         description="Client ID",
     *         in="path"
     *     ),
     *     @SWG\Response(
     *         response="200",
     *         description="Success oparation",
     *         @SWG\Schema(
     *             ref="#/definitions/Client"
     *         )
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
     *         description="Client not found"
     *     ),
     * )
     *
     * @param Client $client
     * @param ClientManager $clientManager
     * @param ClientTransformerInterface $transformer
     *
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function deactivate(Client $client, ClientManager $clientManager, ClientTransformerInterface $transformer): JsonResponse
    {
        $this->authorize('update', $client);

        $clientManager->deactivate($client);

        return new JsonTransformedResponse($client, $transformer);
    }

    /**
     * @SWG\Get(
     *     path="/clients/{id}/focuses",
     *     summary="Get available focuses for client",
     *     security={{"apiToken": {}}},
     *     tags={"Clients"},
     *     @SWG\Parameter(
     *         name="id",
     *         type="string",
     *         description="Client ID",
     *         in="path",
     *         required=true
     *     ),
     *     @SWG\Response(
     *         response="200",
     *         description="List of focuses",
     *         @SWG\Schema(
     *             allOf={
     *                 @SWG\Property(
     *                     property="total",
     *                     type="integer"
     *                 ),
     *                 @SWG\Property(
     *                     property="data",
     *                     @SWG\Items(ref="#/definitions/Focus")
     *                 )
     *             }
     *         )
     *     )
     * )
     * @param ListRequest $request
     * @param ListResponseInterface $response
     * @param Client $client
     * @param ClientTransformerInterface $clientTransformer
     * @param FocusTransformerInterface $transformer
     *
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function focuses(
        ListRequest $request,
        ListResponseInterface $response,
        Client $client,
        ClientTransformerInterface $clientTransformer,
        FocusTransformerInterface $transformer
    ): JsonResponse {
        $this->authorize('view', $client);
        $this->authorize('list', Focus::class);

        $builder = Focus::ofUser($client->user);

        return $response
            ->setBuilder($builder)
            ->setTransformer($transformer)
            ->addValueWithTransformer('client', $client, $clientTransformer)
            ->setOffset($request->getOffset())
            ->setLimit($request->getLimit())
            ->getResponse();
    }

    /**
     * @SWG\Get(
     *     path="/clients/{id}/users",
     *     summary="Get users of client",
     *     security={{"apiToken": {}}},
     *     tags={"Clients"},
     *     @SWG\Parameter(
     *         name="id",
     *         type="string",
     *         description="Client ID",
     *         in="path",
     *         required=true
     *     ),
     *     @SWG\Parameter(
     *         name="offset",
     *         type="integer",
     *         description="Offset",
     *         in="path",
     *         required=false
     *     ),
     *     @SWG\Parameter(
     *         name="limit",
     *         type="integer",
     *         description="Limit",
     *         in="path",
     *         required=false
     *     ),
     *     @SWG\Response(
     *         response="200",
     *         description="List of users",
     *         @SWG\Schema(
     *             allOf={
     *                 @SWG\Property(
     *                     property="offset",
     *                     type="integer"
     *                 ),
     *                 @SWG\Property(
     *                     property="limit",
     *                     type="integer"
     *                 ),
     *                 @SWG\Property(
     *                     property="total",
     *                     type="integer"
     *                 ),
     *                 @SWG\Property(
     *                     property="data",
     *                     @SWG\Items(ref="#/definitions/User")
     *                 )
     *             }
     *         )
     *     )
     * )
     *
     * @param ListRequest $request
     * @param ListResponseInterface $response
     *
     * @param Client $client
     * @param ClientTransformerInterface $clientTransformer
     * @param UserTransformerInterface $userTransformer
     *
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function users(
        ListRequest $request,
        ListResponseInterface $response,
        Client $client,
        ClientTransformerInterface $clientTransformer,
        UserTransformerInterface $userTransformer
    ): JsonResponse {
        $this->authorize('view', $client);
        $this->authorize('list', User::class);

        $builder = $client->users()->getQuery();

        return $response
            ->setBuilder($builder)
            ->setTransformer($userTransformer)
            ->addValueWithTransformer('client', $client, $clientTransformer)
            ->setOffset($request->getOffset())
            ->setLimit($request->getLimit())
            ->getResponse();
    }
}
