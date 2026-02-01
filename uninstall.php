<?php
/**
 * WP Search Any - Uninstall Script
 *
 * @package WP_Search_Any
 */

// If uninstall not called from WordPress, exit
if (!defined('WP_UNINSTALL_PLUGIN')) {
    exit;
}

// Check if user has permission to uninstall
if (!current_user_can('activate_plugins')) {
    return;
}

global $wpdb;

// Delete plugin options
delete_option('mc_wp_search_any_settings');
delete_option('mc_wp_search_any_version');

// For multisite, delete options from all sites
if (is_multisite()) {
    $blog_ids = $wpdb->get_col("SELECT blog_id FROM $wpdb->blogs");
    $original_blog_id = get_current_blog_id();
    
    foreach ($blog_ids as $blog_id) {
        switch_to_blog($blog_id);
        
        // Delete options for each site
        delete_option('mc_wp_search_any_settings');
        delete_option('mc_wp_search_any_version');
        
        // Delete search history table for each site
        $table_name = $wpdb->prefix . 'wp_search_any_history';
        $wpdb->query("DROP TABLE IF EXISTS {$table_name}");
        
        restore_current_blog();
    }
} else {
    // For single site, delete the search history table
    $table_name = $wpdb->prefix . 'wp_search_any_history';
    $wpdb->query("DROP TABLE IF EXISTS {$table_name}");
}

// Clean up any transients
$wpdb->query("DELETE FROM $wpdb->options WHERE option_name LIKE '_transient_mc_search_%'");
$wpdb->query("DELETE FROM $wpdb->options WHERE option_name LIKE '_transient_timeout_mc_search_%'");

// Remove any scheduled events
$timestamp = wp_next_scheduled('mc_search_any_cleanup');
if ($timestamp) {
    wp_unschedule_event($timestamp, 'mc_search_any_cleanup');
}

// Remove rewrite rules
flush_rewrite_rules();

// Log uninstall action (optional)
if (function_exists('wp_log_action')) {
    wp_log_action('WP Search Any plugin uninstalled');
}