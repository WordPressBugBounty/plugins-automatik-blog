<?php
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Handles the Web Stories CPT and template loading.
 */
class Automatik_Blog_Webstories {

    /**
     * Constructor
     */
    public function __construct() {
        // Register CPT during 'init'
        add_action('init', [$this, 'register_web_stories_cpt']);

        // Use a custom template for single 'web_story'
        add_filter('template_include', [$this, 'load_web_story_template']);
    }

    /**
     * Register the Custom Post Type for Web Stories
     */
    public function register_web_stories_cpt() {
        // You could make this slug dynamic with a plugin setting later
        $base_slug = get_option('automatik_blog_webstories_slug', 'webstories');

        $labels = [
            'name'          => __('Web Stories', 'automatik-blog'),
            'singular_name' => __('Web Story', 'automatik-blog'),
            'add_new_item'  => __('Add New Web Story', 'automatik-blog'),
            'edit_item'     => __('Edit Web Story', 'automatik-blog'),
            'new_item'      => __('New Web Story', 'automatik-blog'),
            'view_item'     => __('View Web Story', 'automatik-blog'),
            'search_items'  => __('Search Web Stories', 'automatik-blog'),
        ];

        $args = [
            'label'               => __('Web Stories', 'automatik-blog'),
            'labels'              => $labels,
            'public'              => true,
            'has_archive'         => true,
            'exclude_from_search' => false,
            'publicly_queryable'  => true,
            'show_ui'             => true,
            'show_in_menu'        => true,
            'supports'            => ['title', 'thumbnail'],
            'rewrite' => [
                'slug'       => 'webstories',
                'with_front' => false
            ],
        ];

        register_post_type('web_story', $args);
    }

    /**
     * Force a custom template when viewing a single Web Story
     */
    public function load_web_story_template( $template ) {
    if ( is_singular( 'web_story' ) ) {
        $plugin_template = AUTOMATIK_BLOG_PLUGIN_DIR . 'templates/webstory-template.php';
        if ( file_exists( $plugin_template ) ) {
            return $plugin_template;
        }
    }

    // Load archive template for web_story CPT
    if ( is_post_type_archive( 'web_story' ) ) {
        $archive_template = AUTOMATIK_BLOG_PLUGIN_DIR . 'templates/archive-web_story.php';
        if ( file_exists( $archive_template ) ) {
            return $archive_template;
        }
    }
    
    return $template;
    }
}
