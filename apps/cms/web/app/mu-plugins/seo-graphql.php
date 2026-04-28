<?php
/**
 * Exposes RankMath SEO meta fields to WPGraphQL.
 *
 * RankMath stores its data in standard post meta. This plugin reads those
 * values and registers them on the WPGraphQL schema so Nuxt can query them.
 *
 * Fields added to Page, Post, and TeamMember types:
 *   seo { title, description, robots, canonical, ogTitle, ogDescription }
 */

declare(strict_types=1);

add_action('graphql_register_types', function (): void {

    // ── SEO Object type ──────────────────────────────────────────────────────

    register_graphql_object_type('SeoMeta', [
        'description' => 'SEO metadata from RankMath',
        'fields'      => [
            'title'          => ['type' => 'String', 'description' => 'SEO title'],
            'description'    => ['type' => 'String', 'description' => 'Meta description'],
            'robots'         => ['type' => 'String', 'description' => 'Robots directive (e.g. index,follow)'],
            'canonical'      => ['type' => 'String', 'description' => 'Canonical URL'],
            'ogTitle'        => ['type' => 'String', 'description' => 'Open Graph title'],
            'ogDescription'  => ['type' => 'String', 'description' => 'Open Graph description'],
        ],
    ]);

    // ── Register `seo` field on content types ────────────────────────────────

    $types = ['Page', 'Post', 'TeamMember'];

    foreach ($types as $type) {
        register_graphql_field($type, 'seo', [
            'type'        => 'SeoMeta',
            'description' => 'SEO metadata',
            'resolve'     => function ($source) use ($type): array {
                $post_id = $source->ID ?? ($source->databaseId ?? 0);

                if (! $post_id) {
                    return [];
                }

                // RankMath meta keys
                $rm_title       = get_post_meta($post_id, 'rank_math_title', true);
                $rm_description = get_post_meta($post_id, 'rank_math_description', true);
                $rm_robots_raw  = get_post_meta($post_id, 'rank_math_robots', true);
                $rm_og_title    = get_post_meta($post_id, 'rank_math_facebook_title', true);
                $rm_og_desc     = get_post_meta($post_id, 'rank_math_facebook_description', true);

                // robots is stored as a serialized array; flatten to a string
                $robots = '';
                if ($rm_robots_raw) {
                    $arr    = maybe_unserialize($rm_robots_raw);
                    $robots = is_array($arr) ? implode(',', $arr) : (string) $rm_robots_raw;
                }

                // Fall back to post title if no custom SEO title set
                $post  = get_post($post_id);
                $title = $rm_title ?: ($post ? get_the_title($post) : '');

                return [
                    'title'         => $title,
                    'description'   => $rm_description ?: '',
                    'robots'        => $robots ?: 'index,follow',
                    'canonical'     => (string) get_permalink($post_id),
                    'ogTitle'       => $rm_og_title ?: $title,
                    'ogDescription' => $rm_og_desc ?: ($rm_description ?: ''),
                ];
            },
        ]);
    }
});
