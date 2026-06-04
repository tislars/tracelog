<?php
/**
 * WP-CLI content seed — creates demo pages, team members, and a Gravity Form.
 *
 * Run via:
 *   wp eval-file bin/seed-content.php
 *   make db-seed
 *
 * Idempotent: checks for existing content before creating anything.
 */

WP_CLI::log('Starting content seed...');

// ── Helpers ───────────────────────────────────────────────────────────────────

function seed_page(string $title, string $slug, string $content): int
{
    $existing = get_page_by_path($slug, OBJECT, 'page');
    if ($existing) {
        WP_CLI::log("  → '{$slug}' already exists (ID {$existing->ID}), skipping.");
        return (int) $existing->ID;
    }

    $id = wp_insert_post([
        'post_title'   => $title,
        'post_name'    => $slug,
        'post_content' => $content,
        'post_status'  => 'publish',
        'post_type'    => 'page',
    ], true);

    if (is_wp_error($id)) {
        WP_CLI::warning("  ✗ Failed to create '{$title}': " . $id->get_error_message());
        return 0;
    }

    WP_CLI::log("  ✓ Created page '{$title}' (ID {$id})");
    return (int) $id;
}

function seed_team_member(string $name, string $role, string $bio, string $linkedin = ''): int
{
    $existing = get_posts([
        'post_type'      => 'team-member',
        'name'           => sanitize_title($name),
        'post_status'    => 'publish',
        'posts_per_page' => 1,
    ]);

    if (! empty($existing)) {
        WP_CLI::log("  → Team member '{$name}' already exists, skipping.");
        return (int) $existing[0]->ID;
    }

    $id = wp_insert_post([
        'post_title'  => $name,
        'post_status' => 'publish',
        'post_type'   => 'team-member',
    ], true);

    if (is_wp_error($id)) {
        WP_CLI::warning("  ✗ Failed to create '{$name}': " . $id->get_error_message());
        return 0;
    }

    update_field('field_team_role', $role, $id);
    update_field('field_team_bio', $bio, $id);
    if ($linkedin) {
        update_field('field_team_linkedin', $linkedin, $id);
    }

    WP_CLI::log("  ✓ Created team member '{$name}' (ID {$id})");
    return (int) $id;
}

// ── Pages ─────────────────────────────────────────────────────────────────────

WP_CLI::log('Creating pages...');

$home_id = seed_page('Home', 'home', <<<'CONTENT'
<!-- wp:heading {"level":1} -->
<h1 class="wp-block-heading">Welcome to Harborn Digital</h1>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>We build modern headless digital experiences that connect brands with their audiences — fast, flexible, and future-proof.</p>
<!-- /wp:paragraph -->

<!-- wp:heading {"level":2} -->
<h2 class="wp-block-heading">What We Do</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>From strategy to execution, we deliver end-to-end digital solutions powered by cutting-edge technology.</p>
<!-- /wp:paragraph -->

<!-- wp:list -->
<ul class="wp-block-list"><li>Headless WordPress Development</li><li>UX &amp; UI Design</li><li>Digital Strategy</li><li>Performance Optimisation</li></ul>
<!-- /wp:list -->

<!-- wp:heading {"level":2} -->
<h2 class="wp-block-heading">Why Headless?</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>Headless architecture decouples your content from its presentation — giving you the freedom to deliver content across any channel with unmatched performance.</p>
<!-- /wp:paragraph -->
CONTENT);

seed_page('About Us', 'about-us', <<<'CONTENT'
<!-- wp:heading {"level":1} -->
<h1 class="wp-block-heading">About Us</h1>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>We're a digital agency passionate about creating meaningful, high-performance online experiences. Founded by developers, designers, and digital strategists who believe the web can always be better.</p>
<!-- /wp:paragraph -->

<!-- wp:heading {"level":2} -->
<h2 class="wp-block-heading">Our Mission</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>To empower businesses with headless web technology — delivering solutions that are fast, flexible, and built to last.</p>
<!-- /wp:paragraph -->

<!-- wp:heading {"level":2} -->
<h2 class="wp-block-heading">Our Values</h2>
<!-- /wp:heading -->

<!-- wp:list -->
<ul class="wp-block-list"><li><strong>Innovation First</strong> — we embrace emerging technologies to deliver better outcomes</li><li><strong>Client Partnership</strong> — we work alongside you, not just for you</li><li><strong>Technical Excellence</strong> — quality code, clean architecture, measurable results</li><li><strong>Transparent Communication</strong> — no surprises, just honest collaboration</li></ul>
<!-- /wp:list -->

<!-- wp:heading {"level":2} -->
<h2 class="wp-block-heading">Our Story</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>We started with a simple belief: digital products should be built to last. Over the years we've helped businesses of all sizes make the move to modern headless architectures — and we've seen the results first-hand.</p>
<!-- /wp:paragraph -->
CONTENT);

seed_page('Team', 'team', <<<'CONTENT'
<!-- wp:heading {"level":1} -->
<h1 class="wp-block-heading">Meet Our Team</h1>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>We're a diverse group of designers, developers, and strategists united by a passion for great digital work. Get to know the people behind the projects.</p>
<!-- /wp:paragraph -->
CONTENT);

seed_page('Contact', 'contact', <<<'CONTENT'
<!-- wp:heading {"level":1} -->
<h1 class="wp-block-heading">Contact Us</h1>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>Have a project in mind? Fill in the form and we'll get back to you within one business day.</p>
<!-- /wp:paragraph -->
CONTENT);

// ── Reading settings ──────────────────────────────────────────────────────────

if ($home_id) {
    update_option('show_on_front', 'page');
    update_option('page_on_front', $home_id);
    WP_CLI::log("  ✓ Set home page (ID {$home_id}) as front page");
}

// ── Team members ──────────────────────────────────────────────────────────────

WP_CLI::log('Creating team members...');

seed_team_member(
    'Jane Smith',
    'Chief Executive Officer',
    'Jane has 15 years of experience leading digital transformation projects for enterprise clients. She founded the agency with a vision to make headless technology accessible to businesses of every size.',
    'https://linkedin.com/in/jane-smith'
);

seed_team_member(
    'Lars Hoevenaar',
    'Chief Technology Officer',
    'Lars is a full-stack developer with deep expertise in WordPress, Nuxt.js, and headless architecture. He leads the technical direction of every project and is passionate about performance and developer experience.',
    'https://linkedin.com/in/lars-hoevenaar'
);

seed_team_member(
    'Sarah Johnson',
    'Head of Design',
    'Sarah brings user-centred design thinking to every project. With a background in product design and a love for accessibility, she ensures every interface is both beautiful and functional.',
    'https://linkedin.com/in/sarah-johnson'
);

seed_team_member(
    'Mike Williams',
    'Senior Frontend Developer',
    'Mike specialises in Vue.js and Nuxt, building the fast, accessible frontends that power our headless projects. He obsesses over Core Web Vitals and progressive enhancement.',
    'https://linkedin.com/in/mike-williams'
);

// ── Gravity Form ──────────────────────────────────────────────────────────────

WP_CLI::log('Creating Gravity Form...');

if (! class_exists('GFAPI')) {
    WP_CLI::warning('  ✗ GFAPI not available — is Gravity Forms active? Skipping form creation.');
} else {
    $form_exists = false;
    foreach (GFAPI::get_forms() as $existing_form) {
        if ($existing_form['title'] === 'Contact') {
            WP_CLI::log("  → Gravity Form 'Contact' already exists (ID {$existing_form['id']}), skipping.");
            $form_exists = true;
            break;
        }
    }

    if (! $form_exists) {
        $form_id = GFAPI::add_form([
            'title'               => 'Contact',
            'labelPlacement'      => 'top_label',
            'descriptionPlacement' => 'below',
            'fields'              => [
                [
                    'id'          => 1,
                    'type'        => 'text',
                    'label'       => 'Your Name',
                    'isRequired'  => true,
                    'placeholder' => 'Full name',
                ],
                [
                    'id'          => 2,
                    'type'        => 'email',
                    'label'       => 'Email Address',
                    'isRequired'  => true,
                    'placeholder' => 'you@example.com',
                ],
                [
                    'id'          => 3,
                    'type'        => 'phone',
                    'label'       => 'Phone Number',
                    'isRequired'  => false,
                    'placeholder' => '+31 6 00 00 00 00',
                    'phoneFormat' => 'standard',
                ],
                [
                    'id'          => 4,
                    'type'        => 'textarea',
                    'label'       => 'Message',
                    'isRequired'  => true,
                    'placeholder' => 'How can we help you?',
                ],
            ],
            'button'              => [
                'type' => 'text',
                'text' => 'Send Message',
            ],
            'confirmations'       => [
                [
                    'id'        => 'default_confirmation',
                    'name'      => 'Default Confirmation',
                    'isDefault' => true,
                    'type'      => 'message',
                    'message'   => "<p>Thank you for your message. We'll be in touch within one business day.</p>",
                ],
            ],
            'notifications'       => [
                [
                    'id'               => 'admin_notification',
                    'isActive'         => true,
                    'name'             => 'Admin Notification',
                    'service'          => 'wordpress',
                    'event'            => 'form_submission',
                    'toType'           => 'email',
                    'to'               => get_option('admin_email'),
                    'subject'          => 'New contact form submission',
                    'message'          => '{all_fields}',
                    'from'             => get_option('admin_email'),
                    'fromName'         => get_bloginfo('name'),
                    'replyTo'          => '{Entry:2}',
                    'conditionalLogic' => null,
                ],
            ],
        ]);

        if (is_wp_error($form_id)) {
            WP_CLI::warning('  ✗ Failed to create Gravity Form: ' . $form_id->get_error_message());
        } else {
            WP_CLI::log("  ✓ Created Gravity Form 'Contact' (ID {$form_id})");
        }
    }
}

// ── Flush rewrite rules ───────────────────────────────────────────────────────

flush_rewrite_rules();
WP_CLI::log('✓ Rewrite rules flushed.');
WP_CLI::success('Content seed complete!');
