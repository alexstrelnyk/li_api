<?php

return [

    'dsn' => env('SENTRY_LARAVEL_DSN', 'https://972f1b38097a4125ab9d65d0177009ba@sentry.io/1792316'),

    // capture release as git sha
    // 'release' => trim(exec('git --git-dir ' . base_path('.git') . ' log --pretty="%h" -n1 HEAD')),

    'breadcrumbs' => [

        // Capture bindings on SQL queries logged in breadcrumbs
        'sql_bindings' => true,

    ],

];
