<?php
/**
 * Main plugin class for WP Search Any
 *
 * @since 1.0.0
 * @package WP_Search_Any
 */

if (!defined('ABSPATH')) {
    exit;
}

class WP_Search_Any {

    /**
     * Constructor - load dependencies and initialize table
     *
     * @since 1.0.0
     * @return void
     */
    public function __construct() {
        $this->load_dependencies();
        add_action('init', [$this, 'init_search_table']);
    }

    /**
     * Load all required plugin class files
     *
     * @since 1.0.0
     * @return void
     */
    public function load_dependencies() {
        require_once WP_SEARCH_ANY_PATH . 'includes/class-wp-search-any-row-meta.php';
        require_once WP_SEARCH_ANY_PATH . 'includes/class-wp-search-any-shortcode.php';
        require_once WP_SEARCH_ANY_PATH . 'includes/class-wp-search-any-admin.php';
    }

    /**
     * Initialize all core plugin components
     *
     * @since 1.0.0
     * @return void
     */
    public function run() {
        new WP_Search_Any_Plugin_Meta();
        new WP_Search_Any_Shortcode();
        new WP_Search_Any_Admin();
    }

    /**
     * Create search history table on plugin activation/init
     *
     * @since 1.0.0
     * @return void
     */
    public function init_search_table() {
        global $wpdb;

        $table = $wpdb->prefix . 'wp_search_any_history';
        $charset_collate = $wpdb->get_charset_collate();

        $sql = "CREATE TABLE {$table} (
            id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
            search_string TEXT NOT NULL,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id)
        ) {$charset_collate};";

        require_once ABSPATH . 'wp-admin/includes/upgrade.php';
        dbDelta($sql);
    }
}