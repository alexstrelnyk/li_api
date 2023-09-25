<?php
declare(strict_types=1);

namespace App\Http\Middleware;

use App\Models\User;
use App\Services\ConsecutiveDaysService\ConsecutiveDaysService;
use Closure;
use Illuminate\Http\Request;

class ConsecutiveDaysMiddleware
{
    /**
     * @var ConsecutiveDaysService
     */
    private $consecutiveDays;

    /**
     * ConsecutiveDaysMiddleware constructor.
     *
     * @param ConsecutiveDaysService $consecutiveDays
     */
    public function __construct(ConsecutiveDaysService $consecutiveDays)
    {
        $this->consecutiveDays = $consecutiveDays;
    }

    /**
     * Handle an incoming request.
     *
     * @param  Request  $request
     * @param Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if ($request->user() instanceof User) {
            $this->consecutiveDays->processConsecutiveDays($request->user());
        }
        return $next($request);
    }
}
