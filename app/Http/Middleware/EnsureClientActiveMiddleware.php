<?php
declare(strict_types=1);

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use DateTime;
use Illuminate\Http\Request;

class EnsureClientActiveMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        /** @var User $user */
        $user = $request->user();
        if ($user->permission === User::CLIENT_ADMIN || $user->permission === User::APP_USER) {
            $client = $user->client;

            if ($client->deactivated_at instanceof DateTime) {
                abort(403, 'Client deactivated');
            }
        }

        return $next($request);
    }
}
