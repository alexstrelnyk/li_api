<?php
declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Models\User;
use App\Notifications\TestActionPushNotification;
use App\Notifications\TestPushNotification;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Swagger\Annotations as SWG;

class PushController extends Controller
{
    /**
     * @SWG\Post(
     *     path="/push/test",
     *     summary="Send test push notification",
     *     tags={"Push"},
     *     @SWG\Parameter(
     *         name="email",
     *         type="string",
     *         required=true,
     *         in="query"
     *     ),
     *     @SWG\Response(
     *          response="200",
     *          description="1"
     * )
     * )
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function test(Request $request): JsonResponse
    {
        $email = $request->get('email');
        $user = User::findByEmail($email);

        if (!$user instanceof User) {
            return new JsonResponse(['error' => 'User with passed email not found']);
        }

        if (!$user->devices->count()) {
            return new JsonResponse(['error' => 'User has not devices']);

        }

        $user->notify(new TestPushNotification($email));

        return new JsonResponse(['message' => 'Notifications was been sent']);
    }

    /**
     * @SWG\Post(
     *     path="/push/action",
     *     summary="Send test push notification",
     *     tags={"Push"},
     *     @SWG\Parameter(
     *         name="email",
     *         type="string",
     *         required=true,
     *         in="query"
     *     ),
     *     @SWG\Response(
     *          response="200",
     *          description="1"
     * )
     * )
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function action(Request $request): JsonResponse
    {
        $email = $request->get('email');
        $user = User::findByEmail($email);

        if (!$user instanceof User) {
            return new JsonResponse(['error' => 'User with passed email not found']);
        }

        if (!$user->devices->count()) {
            return new JsonResponse(['error' => 'User has not devices']);
        }

        $user->notify(new TestActionPushNotification($email));

        return new JsonResponse(['message' => 'Notifications was been sent']);
    }
}
