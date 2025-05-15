<?php
/**
 * Plugin Name: Automatik Blog
 * Description: Plugin for integration with Automatik Blog, allowing automated publishing of SEO-optimized articles.
 * Version: 1.0.3
 * Author: Automatik Blog
 * Author URI: https://automatikblog.com
 * License: GPLv2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: automatik-blog
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

define( 'AUTOMATIK_BLOG_PLUGIN_VERSION', '1.0.3' );
define( 'AUTOMATIK_BLOG_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'AUTOMATIK_BLOG_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

require_once AUTOMATIK_BLOG_PLUGIN_DIR . 'includes/class-plugin-settings.php';
require_once AUTOMATIK_BLOG_PLUGIN_DIR . 'includes/class-rest-api-endpoints.php';
require_once AUTOMATIK_BLOG_PLUGIN_DIR . 'includes/class-logger.php';
require_once AUTOMATIK_BLOG_PLUGIN_DIR . 'includes/class-webstories.php';

/**
 * Generate a unique code and add it to the options.
 */
function automatik_blog_generate_unique_code() {
    $code = get_option( 'automatik_blog_unique_code' );
    if ( ! $code ) {
        // Use wp_generate_password to create a secure unique code.
        $code = wp_generate_password( 20, false, false );
        add_option( 'automatik_blog_unique_code', $code );
    }
}

/**
 * Plugin activation function.
 * - Generates the unique code.
 * - Registers the custom post type.
 * - Flushes rewrite rules so that the custom post type archive is registered.
 */
function automatik_blog_activate() {
    // Generate the unique code (if not already present).
    automatik_blog_generate_unique_code();
    
    // Register the custom post type so it can be flushed.
    new Automatik_Blog_Webstories();
    
    // Flush rewrite rules to register the custom post type archive.
    flush_rewrite_rules();
}
register_activation_hook( __FILE__, 'automatik_blog_activate' );

/**
 * Plugin deactivation function.
 * - Flushes rewrite rules to clean up.
 */
function automatik_blog_deactivate() {
    flush_rewrite_rules();
}
register_deactivation_hook( __FILE__, 'automatik_blog_deactivate' );

/**
 * Initialize the plugin.
 */
function automatik_blog_init() {
    load_plugin_textdomain( 'automatik-blog', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
    new Automatik_Blog_Plugin_Settings();
    new Automatik_Blog_REST_API_Endpoints();

    // Instantiate the Webstories class (this registers your custom post type).
    new Automatik_Blog_Webstories();
}
add_action( 'plugins_loaded', 'automatik_blog_init' );

/**
 * Add a filter to include the "web_story" post type in Yoast SEO's sitemap.
 */
add_action( 'init', 'automatik_blog_include_web_story_in_yoast', 20 );
function automatik_blog_include_web_story_in_yoast() {
    add_filter( 'wpseo_sitemap_post_types', function( $post_types ) {
        $post_types['web_story'] = true;
        return $post_types;
    });
}

/**
 * Add support for Rank Math's sitemap by including the "web_story" CPT.
 */
add_action( 'init', 'automatik_blog_include_web_story_in_rank_math', 20 );
function automatik_blog_include_web_story_in_rank_math() {
    add_filter( 'rank_math/sitemap/post_types', function( $post_types ) {
        $post_types['web_story'] = true;
        return $post_types;
    });
}

/**
 * Add support for All in One SEO's sitemap by ensuring "web_story" is not excluded.
 */
add_action( 'init', 'automatik_blog_include_web_story_in_all_in_one_seo', 20 );
function automatik_blog_include_web_story_in_all_in_one_seo() {
    add_filter( 'aioseop_sitemap_excluded_post_types', function( $excluded_post_types ) {
        if ( ( $key = array_search( 'web_story', $excluded_post_types ) ) !== false ) {
            unset( $excluded_post_types[$key] );
        }
        return $excluded_post_types;
    });
}
