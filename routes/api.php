<?php
declare(strict_types=1);

/*
 * Api Routes
 * Namespaces indicate folder structure
 * Routes prefix begins '/api'
 * Routes names begins 'api.'
 */
Route::group(['namespace' => 'Api', 'as' => 'api.', 'middleware' => 'api-logger'], function () {
    RouterPath::includeRoutes(__DIR__ . '/Api/');
});
