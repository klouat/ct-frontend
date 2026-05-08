<?php

return [

    /*
    |--------------------------------------------------------------------------
    | View Storage Paths
    |--------------------------------------------------------------------------
    */

    'paths' => [
        resource_path('views'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Compiled View Path
    |--------------------------------------------------------------------------
    */

    'compiled' => env(
        'VIEW_COMPILED_PATH',
        env('VERCEL')
            ? '/tmp/storage/framework/views'
            : realpath(storage_path('framework/views'))
    ),

];
