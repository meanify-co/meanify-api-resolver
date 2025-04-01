<?php

return [

    'host'    => env('MEANIFY_API_RESOLVER_HOST', 'localhost'),

    'api_key' => env('MEANIFY_API_RESOLVER_API_KEY', null),

    'render_api_exception' => env('MEANIFY_API_RESOLVER_RENDER_EXCEPTION', false),

    'constant_headers' => [

        'x-mfy-app-id'  => env('MEANIFY_API_RESOLVER_APP_ID', null),

        'x-mfy-app-secret-key' => env('MEANIFY_API_RESOLVER_APP_SECRET_KEY', null),
    ],
];