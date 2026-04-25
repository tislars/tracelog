<?php

/**
 * Configuration overrides for WP_ENV === 'development'
 */

use Roots\WPConfig\Config;

use function Env\env;

Config::define('SAVEQUERIES', true);
Config::define('WP_DEBUG', true);
Config::define('WP_DEBUG_DISPLAY', true);
Config::define('WP_DEBUG_LOG', env('WP_DEBUG_LOG') ?? true);
Config::define('WP_DISABLE_FATAL_ERROR_HANDLER', true);
Config::define('SCRIPT_DEBUG', true);
Config::define('DISALLOW_INDEXING', true);

ini_set('display_errors', '1');

// Enable plugin and theme updates and installation from the admin
Config::define('DISALLOW_FILE_MODS', false);

// WPGraphQL debug mode
Config::define('GRAPHQL_DEBUG', true);

// CORS for Nuxt dev server
add_action('init', function () {
    $nuxt_origin = env('NUXT_URL') ?: 'http://localhost:3000';

    // Only emit headers on GraphQL requests to avoid polluting other responses
    $request_uri = $_SERVER['REQUEST_URI'] ?? '';
    if (! str_contains($request_uri, '/graphql')) {
        return;
    }

    header('Access-Control-Allow-Origin: ' . $nuxt_origin);
    header('Access-Control-Allow-Credentials: true');
    header('Access-Control-Allow-Headers: Content-Type, Authorization, X-WP-Nonce');
    header('Access-Control-Allow-Methods: POST, GET, OPTIONS');

    if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
        http_response_code(204);
        exit;
    }
});

