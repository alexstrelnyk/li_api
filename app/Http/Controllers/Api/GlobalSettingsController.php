<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\ApiController;
use App\Http\Requests\ApiRequest;
use App\Manager\GlobalSettingsManager;
use App\Transformers\GlobalSettings\GlobalSettingsTransformerInterface;
use Illuminate\Http\JsonResponse;

/**
 * Class GlobalSettingsController
 * @package App\Http\Controllers\Api
 */
class GlobalSettingsController extends ApiController
{

    /**
     *
     * @SWG\Get(
     *     path="/global-settings",
     *     summary="Get Global Settings",
     *     tags={"Global Settings"},
     *     security={{"apiToken": {}}},
     *     @SWG\Response(
     *         response=200,
     *         description="Successful operation",
     *         @SWG\Schema(
     *             @SWG\Property(
     *                 property="data",
     *                 ref="#/definitions/GlobalSettings"
     *             ),
     *         ),
     *     ),
     *     @SWG\Response(
     *         response="401",
     *         description="Unauthorized user",
     *     ),
     * )
     *
     * @param GlobalSettingsTransformerInterface $globalSettingsTransformer
     * @param GlobalSettingsManager $globalSettingsManager
     * @return JsonResponse
     */
    public function getGlobalSettings(GlobalSettingsTransformerInterface $globalSettingsTransformer, GlobalSettingsManager $globalSettingsManager): JsonResponse
    {
        $globalSettings = $globalSettingsManager->getGlobalSettings();

        return new JsonResponse(fractal($globalSettings, $globalSettingsTransformer)->toArray());
    }

    /**
     *
     * @SWG\Patch(
     *     path="/global-settings",
     *     summary="Update Global Settings",
     *     tags={"Global Settings"},
     *     security={{"apiToken": {}}},
     *     @SWG\Parameter(
     *         name="body",
     *         in="body",
     *         required=true,
     *         @SWG\Schema(
     *             ref="#/definitions/GlobalSettings"
     *         )
     *     ),
     *     @SWG\Response(
     *         response=200,
     *         description="Successful operation",
     *         @SWG\Schema(
     *             @SWG\Property(
     *                 property="data",
     *                 ref="#/definitions/GlobalSettings"
     *             ),
     *         ),
     *     ),
     *     @SWG\Response(
     *         response="401",
     *         description="Unauthorized user",
     *     ),
     * )
     *
     * @param ApiRequest $apiRequest
     * @param GlobalSettingsManager $globalSettingsManager
     * @param GlobalSettingsTransformerInterface $globalSettingsTransformer
     * @return JsonResponse
     */
    public function updateGlobalSettings(ApiRequest $apiRequest, GlobalSettingsManager $globalSettingsManager, GlobalSettingsTransformerInterface $globalSettingsTransformer) : JsonResponse
    {

        $globalSettings = $globalSettingsManager->updateGlobalSettings($apiRequest->all());

        return new JsonResponse(fractal($globalSettings, $globalSettingsTransformer)->toArray());
    }
}