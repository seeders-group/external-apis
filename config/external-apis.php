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

        'semrush' => [
            'unit_rules' => [
                'backlinks_overview' => 40,
                'backlinks_comparison_per_target' => 40,
                'api_units' => 0,
            ],
        ],

        'pricing' => [
            'openai' => [
                'models' => [
                    'dall-e-3' => [
                        'standard_1024x1024' => 0.040,
                        'standard_1024x1792' => 0.080,
                        'standard_1792x1024' => 0.080,
                        'hd_1024x1024' => 0.080,
                        'hd_1024x1792' => 0.120,
                        'hd_1792x1024' => 0.120,
                    ],
                    'dall-e-2' => [
                        '1024x1024' => 0.016,
                        '512x512' => 0.018,
                        '256x256' => 0.020,
                    ],
                    'whisper' => [
                        'per_minute' => 0.006,
                    ],
                    'tts' => [
                        'per_1m_characters' => 15.00,
                    ],
                    'tts-hd' => [
                        'per_1m_characters' => 30.00,
                    ],
                ],
                'costs_api_url' => 'https://api.openai.com/v1/organization/costs',
                'usage_api_url' => 'https://api.openai.com/v1/organization/usage',
            ],
            'gemini' => [
                'models' => [
                    'gemini-2.0-flash' => [
                        'input_per_1m_tokens' => 0.10,
                        'output_per_1m_tokens' => 0.40,
                    ],
                    'gemini-1.5-flash' => [
                        'input_per_1m_tokens' => 0.075,
                        'output_per_1m_tokens' => 0.30,
                    ],
                    'gemini-1.5-pro' => [
                        'input_per_1m_tokens' => 1.25,
                        'output_per_1m_tokens' => 5.00,
                    ],
                ],
            ],
            'ahrefs' => [
                'cost_per_unit' => 0.01,
                'unit_type' => 'api_units',
            ],
            'semrush' => [
                'cost_per_unit' => 0.00005,
                'unit_type' => 'api_units',
            ],
        ],
    ],
];
