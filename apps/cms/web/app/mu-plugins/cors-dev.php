<?php

/**
 * CORS headers for the Nuxt dev server.
 * Only active in the 'development' environment and only on GraphQL requests.
 */

if (defined('WP_ENV') && WP_ENV !== 'development') {
    return;
}

add_action('init', function () {
    $nuxt_origin = getenv('NUXT_URL') ?: 'http://localhost:3000';

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
