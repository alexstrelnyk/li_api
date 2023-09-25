<?php
declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Requests\Api\Media\UploadFile;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;
use Spatie\ImageOptimizer\OptimizerChain;

/**
 * Class MediaController
 * @package App\Http\Controllers\Api
 */
class MediaController
{
    /**
     * @SWG\Post(
     *     path="/media/upload",
     *     summary="Upload file to storage",
     *     tags={"Media"},
     *     security={{"apiToken": {}}},
     *     @SWG\Parameter(
     *          name="file",
     *          type="file",
     *          in="formData",
     *     ),
     *     @SWG\Response(
     *         response=200,
     *         description="Successful operation",
     *         @SWG\Schema(
     *             @SWG\Property(
     *                 property="data",
     *                 @SWG\Property(
     *                     property="file",
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
     * @param UploadFile $request
     *
     * @param OptimizerChain $optimizer
     *
     * @return JsonResponse
     */
    public function uploadFile(UploadFile $request, OptimizerChain $optimizer): JsonResponse
    {
        $file = $request->getFile();

        $optimizer->optimize($file->getPath());

        $filePath = Storage::disk('content-images')->putFile('files', $file);

        return new JsonResponse([
            'data' => [
                'file' => Storage::disk('content-images')->url($filePath),
            ],
        ]);
    }
}
