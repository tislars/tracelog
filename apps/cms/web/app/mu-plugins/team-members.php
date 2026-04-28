<?php
/**
 * Team Member custom post type and ACF field group registration.
 *
 * CPT is GraphQL-enabled so it can be queried via WPGraphQL.
 * ACF fields are exposed via the wpgraphql-acf plugin.
 */

declare(strict_types=1);

// ── Custom post type ──────────────────────────────────────────────────────────

add_action('init', function (): void {
    register_post_type('team-member', [
        'labels' => [
            'name'               => 'Team Members',
            'singular_name'      => 'Team Member',
            'add_new_item'       => 'Add New Team Member',
            'edit_item'          => 'Edit Team Member',
            'new_item'           => 'New Team Member',
            'view_item'          => 'View Team Member',
            'search_items'       => 'Search Team Members',
            'not_found'          => 'No team members found',
            'not_found_in_trash' => 'No team members found in trash',
        ],
        'public'              => true,
        'has_archive'         => true,
        'rewrite'             => ['slug' => 'team'],
        'supports'            => ['title', 'thumbnail'],
        'menu_icon'           => 'dashicons-groups',
        // WPGraphQL
        'show_in_graphql'     => true,
        'graphql_single_name' => 'teamMember',
        'graphql_plural_name' => 'teamMembers',
    ]);
});

// ── ACF field group ───────────────────────────────────────────────────────────

add_action('acf/include_fields', function (): void {
    if (! function_exists('acf_add_local_field_group')) {
        return;
    }

    acf_add_local_field_group([
        'key'                   => 'group_team_member_fields',
        'title'                 => 'Team Member Details',
        'fields'                => [
            [
                'key'               => 'field_team_role',
                'label'             => 'Role',
                'name'              => 'role',
                'type'              => 'text',
                'required'          => 1,
                'placeholder'       => 'e.g. Senior Developer',
                'show_in_graphql'   => 1,
            ],
            [
                'key'               => 'field_team_bio',
                'label'             => 'Bio',
                'name'              => 'bio',
                'type'              => 'textarea',
                'rows'              => 4,
                'show_in_graphql'   => 1,
            ],
            [
                'key'               => 'field_team_photo',
                'label'             => 'Photo',
                'name'              => 'photo',
                'type'              => 'image',
                'return_format'     => 'array',
                'preview_size'      => 'medium',
                'show_in_graphql'   => 1,
            ],
            [
                'key'               => 'field_team_linkedin',
                'label'             => 'LinkedIn URL',
                'name'              => 'linkedin_url',
                'type'              => 'url',
                'show_in_graphql'   => 1,
            ],
        ],
        'location'              => [
            [
                [
                    'param'    => 'post_type',
                    'operator' => '==',
                    'value'    => 'team-member',
                ],
            ],
        ],
        'show_in_graphql'       => 1,
        'graphql_field_name'    => 'teamMemberFields',
    ]);
});
