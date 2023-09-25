<?php
declare(strict_types=1);

namespace App\Http\Controllers;

use Artisan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;

class BaseController extends Controller
{
    /**
     * @param string $token
     *
     * @return RedirectResponse|Redirector
     */
    public function openApp(string $token)
    {
        return redirect('leadinclusively://verify/'.$token);
    }

    /**
     * @param Request $request
     * @param string $command
     *
     * @return string
     */
    public function callCommand(Request $request, string $command): string
    {
        Artisan::call($command, $request->all());

        return Artisan::output();
    }

    public function phpinfo()
    {
        phpinfo();
    }
}
