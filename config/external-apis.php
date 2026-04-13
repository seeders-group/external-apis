<?php

declare(strict_types=1);

return [
    /*
    |--------------------------------------------------------------------------
    | Ahrefs API Configuration
    |--------------------------------------------------------------------------
    */
    'ahrefs' => [
        'token' => env('AHREFS_TOKEN'),
        'ref_domain_limit' => env('AHREFS_REF_DOMAIN_LIMIT', 1000),
    ],

    /*
    |--------------------------------------------------------------------------
    | DataForSEO API Configuration
    |--------------------------------------------------------------------------
    */
    'dataforseo' => [
        'username' => env('DATAFORSEO_USERNAME'),
        'password' => env('DATAFORSEO_PASSWORD'),
    ],

    /*
    |--------------------------------------------------------------------------
    | OpenAI API Configuration
    |--------------------------------------------------------------------------
    */
    'openai' => [
        'key' => env('OPENAI_API_KEY'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Google Gemini API Configuration
    |--------------------------------------------------------------------------
    */
    'gemini' => [
        'key' => env('GEMINI_API_KEY'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Hunter API Configuration
    |--------------------------------------------------------------------------
    */
    'hunter' => [
        'api_key' => env('HUNTER_API_KEY'),
    ],

    /*
    |--------------------------------------------------------------------------
    | MOZ API Configuration
    |--------------------------------------------------------------------------
    */
    'moz' => [
        'client_id' => env('MOZ_CLIENT_ID'),
        'client_secret' => env('MOZ_CLIENT_SECRET'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Majestic API Configuration
    |--------------------------------------------------------------------------
    */
    'majestic' => [
        'api_key' => env('MAJESTIC_API_KEY'),
    ],

    /*
    |--------------------------------------------------------------------------
    | SeRanking API Configuration
    |--------------------------------------------------------------------------
    */
    'seranking' => [
        'token' => env('SERANKING_TOKEN'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Semrush API Configuration
    |--------------------------------------------------------------------------
    */
    'semrush' => [
        'api_key' => env('SEMRUSH_API_KEY'),
    ],

    /*
    |--------------------------------------------------------------------------
    | ScraperAPI Configuration
    |--------------------------------------------------------------------------
    */
    'scraperapi' => [
        'key' => env('SCRAPERAPI_KEY'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Google Search API Configuration
    |--------------------------------------------------------------------------
    */
    'google_search' => [
        'key' => env('GOOGLE_SEARCH_KEY'),
        'cx' => env('GOOGLE_SEARCH_CX'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Google PageSpeed API Configuration
    |--------------------------------------------------------------------------
    */
    'google_pagespeed' => [
        'key' => env('GOOGLE_PAGESPEED_KEY'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Usage Tracking Configuration
    |--------------------------------------------------------------------------
    */
    'usage_tracking' => [
        'enabled' => true,

        /*
        |----------------------------------------------------------------------
        | Grafana Cloud Metrics (Push)
        |----------------------------------------------------------------------
        |
        | Pushes aggregated API usage metrics to Grafana Cloud using the
        | Prometheus remote-write compatible endpoint. Metrics are sent in
        | Prometheus text exposition format via the Mimir import API.
        | Run `php artisan external-apis:push-metrics` (or schedule it)
        | to trigger a push.
        */
        'grafana_cloud' => [
            'enabled' => env('GRAFANA_CLOUD_METRICS_ENABLED', false),
            'endpoint' => env('GRAFANA_CLOUD_METRICS_ENDPOINT'),
            'user_id' => env('GRAFANA_CLOUD_USER_ID'),
            'api_token' => env('GRAFANA_CLOUD_API_TOKEN'),
        ],

        'semrush' => [
            'unit_rules' => [
                'backlinks_overview' => 40,
                'backlinks_comparison_per_target' => 40,
                'api_units' => 0,
            ],
        ],
    ],
];
