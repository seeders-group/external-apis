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
    | Advanced Web Ranking API Configuration
    |--------------------------------------------------------------------------
    */
    'advanced_web_ranking' => [
        'token' => env('ADVANCED_WEB_RANKING_TOKEN'),
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
    | Google Search Console Configuration
    |--------------------------------------------------------------------------
    */
    'search_console' => [
        'client_id' => env('GOOGLE_SERVICES_CLIENT_ID'),
        'client_secret' => env('GOOGLE_SERVICES_CLIENT_SECRET'),
        'redirect' => env('SEARCH_CONSOLE_REDIRECT_URI'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Teamleader ORBIT Configuration
    |--------------------------------------------------------------------------
    */
    'teamleader_orbit' => [
        'client_id' => env('TEAMLEADER_CLIENT_ID'),
        'client_secret' => env('TEAMLEADER_CLIENT_SECRET'),
        'redirect_uri' => env('TEAMLEADER_REDIRECT_URI'),
        'base_url' => env('TEAMLEADER_BASE_URL', 'https://api.yadera.com/'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Bazoom API Configuration
    |--------------------------------------------------------------------------
    */
    'bazoom' => [
        'username' => env('BAZOOM_USERNAME'),
        'password' => env('BAZOOM_PASSWORD'),
        'page_size' => env('BAZOOM_PAGE_SIZE', 50),
    ],

    /*
    |--------------------------------------------------------------------------
    | Leolytics API Configuration
    |--------------------------------------------------------------------------
    */
    'leolytics' => [
        'username' => env('LEOLYTICS_USERNAME'),
        'password' => env('LEOLYTICS_PASSWORD'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Prensalink API Configuration
    |--------------------------------------------------------------------------
    */
    'prensalink' => [
        'email' => env('PRENSALINK_EMAIL'),
        'api_key' => env('PRENSALINK_API_KEY'),
    ],

    /*
    |--------------------------------------------------------------------------
    | PaperClub API Configuration
    |--------------------------------------------------------------------------
    */
    'paperclub' => [
        'auth_header' => env('PAPERCLUB_AUTH_HEADER'),
    ],

    /*
    |--------------------------------------------------------------------------
    | WhitePress API Configuration
    |--------------------------------------------------------------------------
    */
    'whitepress' => [
        'api_key' => env('WHITEPRESS_API_KEY'),
        'limit' => env('WHITEPRESS_LIMIT', 20),
        'languages' => [
            'PL', 'EN', 'DE', 'ES', 'CZ', 'SK', 'HU', 'RO', 'BG', 'TR',
            'HR', 'SI', 'UA', 'BE', 'NL', 'FR', 'PT', 'IT', 'SE', 'FI',
            'DK', 'NO', 'LT', 'LV', 'EE', 'GR', 'AT', 'CH', 'BR',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Website Categorization API Configuration
    |--------------------------------------------------------------------------
    */
    'website_categorization' => [
        'api_key' => env('WEBSITE_CATEGORIZATION_API_KEY'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Tree Nation API Configuration
    |--------------------------------------------------------------------------
    */
    'tree_nation' => [
        'endpoint' => env('TREE_NATION_ENDPOINT', 'https://api.tree-nation.com'),
        'token' => env('TREE_NATION_TOKEN', 'testing-token'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Brand Tracker API Configuration
    |--------------------------------------------------------------------------
    */
    'brand_tracker' => [
        'api_url' => env('BRAND_TRACKER_API_URL'),
        'timeout' => env('BRAND_TRACKER_TIMEOUT', 300),
        'jwt_secret' => env('BRAND_TRACKER_JWT_SECRET'),
        'jwt_expiration' => env('BRAND_TRACKER_JWT_EXPIRATION', 3600),
    ],

    /*
    |--------------------------------------------------------------------------
    | Ereplace API Configuration
    |--------------------------------------------------------------------------
    */
    'ereplace' => [
        'base_url' => 'https://crm.1ereplace.com/api',
    ],

    /*
    |--------------------------------------------------------------------------
    | Usage Tracking Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for API cost tracking, budget alerts, and pricing.
    | Database pricing tables take precedence over these config values.
    |
    */
    'usage_tracking' => [
        'enabled' => true,

        'semrush' => [
            'unit_rules' => [
                'backlinks_overview' => 100,
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
