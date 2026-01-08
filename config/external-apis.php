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
];
