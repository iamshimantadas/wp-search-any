<?php
/**
 * Settings page rendering for WP Search Any plugin
 *
 * @since 1.0.0
 * @package WP_Search_Any
 */

if (!defined('ABSPATH')) {
    exit;
}

class WP_Search_Any_Settings {

    /**
     * Render the settings page HTML output
     *
     * @since 1.0.0
     * @return void
     */
    public static function render() {
        // Handle clear logs request
        $message = '';
        $message_type = '';
        
        if (isset($_POST['mc_clear_logs']) && $_POST['mc_clear_logs'] == '1') {
            // Verify nonce
            if (wp_verify_nonce($_POST['mc_clear_logs_nonce'], 'mc_clear_all_search_logs')) {
                // Check user capability
                if (current_user_can('manage_options')) {
                    $cleared = self::clear_all_search_logs();
                    if ($cleared) {
                        $message = __('All search history logs have been cleared successfully!', 'wp-search-any');
                        $message_type = 'success';
                    } else {
                        $message = __('Failed to clear search logs. Please try again.', 'wp-search-any');
                        $message_type = 'error';
                    }
                } else {
                    $message = __('You do not have permission to perform this action.', 'wp-search-any');
                    $message_type = 'error';
                }
            } else {
                $message = __('Security check failed. Please try again.', 'wp-search-any');
                $message_type = 'error';
            }
        }
        ?>
        <div class="wrap">
            <h1><?php esc_html_e('WP Search Any Settings', 'wp-search-any'); ?></h1>

            <?php if ($message): ?>
            <div class="notice notice-<?php echo esc_attr($message_type); ?> is-dismissible">
                <p><?php echo esc_html($message); ?></p>
            </div>
            <?php endif; ?>

            <form method="post" action="options.php">
                <?php
                // Use the new settings group name (consistent with register_setting in admin class)
                settings_fields('mc_wp_search_any_settings_group');
                
                // Use the new page slug (consistent with add_submenu_page)
                do_settings_sections('wp-search-any-settings');
                
                submit_button();
                ?>
            </form>
            
            <?php self::render_clear_logs_section(); ?>
        </div>
        <?php
    }
    
    /**
     * Clear all search logs from database
     *
     * @since 1.0.0
     * @return bool True on success, false on failure
     */
    public static function clear_all_search_logs() {
        global $wpdb;
        $table_name = $wpdb->prefix . 'wp_search_any_history';
        
        // Check if table exists
        if ($wpdb->get_var("SHOW TABLES LIKE '{$table_name}'") != $table_name) {
            return false;
        }
        
        // Clear the table
        $result = $wpdb->query("TRUNCATE TABLE {$table_name}");
        
        return $result !== false;
    }
    
    /**
     * Render the clear logs section
     *
     * @since 1.0.0
     * @return void
     */
    public static function render_clear_logs_section() {
        global $wpdb;
        $table = $wpdb->prefix . 'wp_search_any_history';
        $count = 0;
        
        // Check if table exists before counting
        if ($wpdb->get_var("SHOW TABLES LIKE '{$table}'") == $table) {
            $count = $wpdb->get_var("SELECT COUNT(*) FROM {$table}");
        }
        ?>
        <div class="mc-clear-logs-section" style="margin-top: 40px; padding: 20px; background: #fff; border: 1px solid #ccd0d4; border-radius: 4px; max-width: 800px;">
            <h2><?php esc_html_e('Clear Search History', 'wp-search-any'); ?></h2>
            
            <div style="margin-bottom: 20px;">
                <p>
                    <strong><?php esc_html_e('Current Status:', 'wp-search-any'); ?></strong>
                    <?php printf(
                        esc_html__('There are %d search log entries in the database.', 'wp-search-any'),
                        intval($count)
                    ); ?>
                </p>
                <p style="color: #666;">
                    <?php esc_html_e('You can clear all search history logs with the button below. This action is irreversible.', 'wp-search-any'); ?>
                </p>
            </div>
            
            <form method="post" action="" onsubmit="return confirm('<?php echo esc_js(__('Are you sure you want to clear ALL search history logs? This action cannot be undone.', 'wp-search-any')); ?>');">
                <input type="hidden" name="mc_clear_logs" value="1">
                <?php wp_nonce_field('mc_clear_all_search_logs', 'mc_clear_logs_nonce'); ?>
                
                <button type="submit" 
                        name="submit" 
                        class="button button-danger"
                        <?php echo $count == 0 ? 'disabled' : ''; ?>>
                    <span class="dashicons dashicons-trash" style="vertical-align: middle; margin-right: 5px;"></span>
                    <?php esc_html_e('Clear All Search History', 'wp-search-any'); ?>
                </button>
            </form>
            
            <?php if($count == 0): ?>
            <p style="color: #666; font-style: italic; margin-top: 10px;">
                <?php esc_html_e('No search logs to clear.', 'wp-search-any'); ?>
            </p>
            <?php endif; ?>
        </div>
        
        <style>
        .button-danger {
            background: #dc3232;
            border-color: #dc3232;
            color: #fff;
        }
        
        .button-danger:hover {
            background: #a00;
            border-color: #a00;
            color: #fff;
        }
        
        .button-danger:disabled {
            background: #ccc;
            border-color: #ccc;
            color: #666;
            cursor: not-allowed;
        }
        
        .mc-clear-logs-section {
            max-width: 800px;
        }
        </style>
        <?php
    }
}