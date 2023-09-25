<?php
declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\ApiController;
use App\Http\Requests\Api\Device\ActivateRequest;
use App\Http\Requests\Api\Device\DeactivateRequest;
use App\Http\Responses\JsonErrorResponse;
use App\Manager\DeviceManager;
use App\Services\Azure\NotificationHubService\Exception\DeviceRegistrationException;
use Illuminate\Http\JsonResponse;
use Swagger\Annotations as SWG;

class DeviceController extends ApiController
{
    /**
     * @SWG\Post(
     *     path="/devices/activate",
     *     summary="Activate device",
     *     security={{"apiToken": {}}},
     *     tags={"Devices"},
     *     @SWG\Parameter(
     *         name="body",
     *         in="body",
     *         required=true,
     *         @SWG\Schema(
     *             @SWG\Property(
     *                 property="device_id",
     *                 type="string",
     *             ),
     *             @SWG\Property(
     *                 property="device_token",
     *                 type="string",
     *                 description="Is not required"
     *             ),
     *             @SWG\Property(
     *                 property="type",
     *                 type="string",
     *                 description="apple, fcm"
     *             ),
     *         )
     *     ),
     *     @SWG\Response(
     *         response="200",
     *         description="Successful operation"
     *     )
     * )
     *
     * @param ActivateRequest $request
     * @param DeviceManager $deviceManager
     *
     * @return JsonResponse
     */
    public function activate(ActivateRequest $request, DeviceManager $deviceManager): JsonResponse
    {
        try {
            $deviceManager->addDevice($request->getDeviceId(), $request->getType(), $request->getDeviceToken());
        } catch (DeviceRegistrationException $exception) {
            return new JsonErrorResponse($exception->getMessage());
        }

        return new JsonResponse(null, 204);
    }

    /**
     * @SWG\Post(
     *     path="/devices/deactivate",
     *     summary="Deactivate device",
     *     security={{"apiToken": {}}},
     *     tags={"Devices"},
     *     @SWG\Parameter(
     *         name="body",
     *         in="body",
     *         required=true,
     *         @SWG\Schema(
     *             @SWG\Property(
     *                 property="device_id",
     *                 type="string",
     *             )
     *         )
     *     ),
     *     @SWG\Response(
     *         response="200",
     *         description="Successful operation"
     *     )
     * )
     *
     * @param DeactivateRequest $request
     * @param DeviceManager $deviceManager
     *
     * @return JsonResponse
     */
    public function deactivate(DeactivateRequest $request, DeviceManager $deviceManager): JsonResponse
    {
        $deviceManager->removeDevice($request->getDeviceId());
        return new JsonResponse();
    }
}
