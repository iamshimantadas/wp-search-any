<?php
/**
 * Plugin Name: WP Search Any
 * Description: WordPress Site Search Plugin Across Post-Types, Pages, CPT etc.
 * Text Domain: wp-search-any
 * Author:      Shimanta Das
 * Author URI:  https://microcodes.in
 * Version:     1.0.0
 * Package:     WP_Search_Any
 */

if (!defined('ABSPATH')) {
    exit;
}

define('WP_SEARCH_ANY_VERSION', '1.0.0');
define('WP_SEARCH_ANY_PATH', plugin_dir_path(__FILE__));
define('WP_SEARCH_ANY_URL', plugin_dir_url(__FILE__));
define('WP_SEARCH_ANY_FILE', __FILE__);

require_once WP_SEARCH_ANY_PATH . 'includes/class-wp-search-any.php';

function run_wp_search_any() {
    $plugin = new WP_Search_Any();
    $plugin->run();
}
run_wp_search_any();

/**
 * Flush rewrite rules on activation/deactivation
 */
register_activation_hook(__FILE__, 'mc_wp_search_any_activate');
register_deactivation_hook(__FILE__, 'mc_wp_search_any_deactivate');

function mc_wp_search_any_activate() {
    if (class_exists('WP_Search_Any_Shortcode')) {
        $shortcode = new WP_Search_Any_Shortcode();
        $shortcode->mc_wp_search_any_rewrite();
    }
    flush_rewrite_rules();
}

function mc_wp_search_any_deactivate() {
    flush_rewrite_rules();
}