<?php

/**
 * Headless Redirect Theme
 *
 * Redirects all public WordPress page views to the Nuxt frontend.
 * WP Admin, REST API (/wp-json), GraphQL (/wp/graphql), feeds, and
 * AJAX requests are intentionally excluded from redirection.
 */

add_action('template_redirect', function () {
    // Never redirect admin, AJAX, feeds, or search-engine pings
    if (
        is_admin()
        || wp_doing_ajax()
        || is_feed()
        || is_robots()
        || is_trackback()
    ) {
        return;
    }

    // Preserve direct access to WP REST API and GraphQL endpoints
    $request_uri = $_SERVER['REQUEST_URI'] ?? '';
    $passthrough_patterns = ['/wp-json', '/wp/graphql', '/wp-login.php', '/wp-cron.php'];

    foreach ($passthrough_patterns as $pattern) {
        if (str_starts_with($request_uri, $pattern)) {
            return;
        }
    }

    $nuxt_url = rtrim(getenv('NUXT_URL') ?: 'http://localhost:3000', '/');
    $path     = $request_uri ?: '/';

    // Use 302 (temporary) during development so WP can take back URLs if needed.
    // Change to 301 only in production after the routing is stable.
    wp_redirect($nuxt_url . $path, 302);
    exit;
});
