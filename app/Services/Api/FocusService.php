<?php
declare(strict_types=1);

namespace App\Services\Api;

use App\Factory\FocusFactory;
use App\Http\DataResponse;
use App\Models\Focus;
use App\Models\User;
use App\Transformers\Focus\FocusTransformer;
use Exception;
use Illuminate\Http\Response;

class FocusService
{
    /**
     * @var Focus
     */
    private $model;

    /**
     * @var FocusFactory
     */
    private $focusFactory;

    /**
     * FocusService constructor.
     *
     * @param Focus $focus
     * @param FocusFactory $focusFactory
     */
    public function __construct(Focus $focus, FocusFactory $focusFactory)
    {
        $this->model = $focus;
        $this->focusFactory = $focusFactory;
    }

    /**
     * @param int $id
     * @param User $user
     * @return DataResponse
     */
    public function show(int $id, User $user): DataResponse
    {
        $focus = Focus::ofUser($user)->findOrFail($id);

        if ($focus === null) {
            return new DataResponse(Response::HTTP_BAD_REQUEST, ['message' => 'Focus not found']);
        }

        return new DataResponse(Response::HTTP_OK, fractal($focus, new FocusTransformer())->toArray());
    }

    /**
     * @param array $data
     *
     * @return Focus
     */
    public function create(array $data): Focus
    {
        $focus = $this->focusFactory->create();

        $focus->title = $data['title'];
        $focus->status = $data['status'];
        $focus->color = $data['accent_color'];
        $focus->image_url = $data['image_url'];
        $focus->video_overview = $data['video_overview'] ?? null;

        $focus->save();

        return $focus;
    }

    /**
     * @param Focus $focus
     * @param array $data
     *
     * @return DataResponse
     */
    public function update(Focus $focus, array $data): DataResponse
    {
        $focus->title = $data['title'];
        $focus->status = $data['status'];
        $focus->color = $data['accent_color'];
        $focus->image_url = $data['image_url'];
        $focus->video_overview = $data['video_overview'] ?? null;

        $focus->update();

        if ($focus === null) {
            return new DataResponse(Response::HTTP_BAD_REQUEST, ['message' => 'Focus not found']);
        }

        return new DataResponse(Response::HTTP_OK, fractal($focus, new FocusTransformer())->toArray());
    }

    /**
     * @param Focus $focus
     * @param array $data
     *
     * @return DataResponse
     */
    public function updatePatch(Focus $focus, array $data): DataResponse
    {
        $focus->fill($data);
        $focus->save();

        return new DataResponse(Response::HTTP_OK, fractal($focus, new FocusTransformer())->toArray());
    }

    /**
     * @param Focus $focus
     *
     * @return DataResponse
     * @throws Exception
     */
    public function delete(Focus $focus): DataResponse
    {
        $focus->delete();

        return new DataResponse(Response::HTTP_OK, ['message' => 'Focus was successfully deleted']);
    }
}
