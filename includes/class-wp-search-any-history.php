<?php
/**
 * Handles plugin search history view
 *
 * @since 1.0.0
 * @package WP_Search_Any
 */

if (!defined('ABSPATH')) {
    exit;
}

if (!class_exists('WP_List_Table')) {
    require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
}

class WP_Search_Any_History extends WP_List_Table {

    /**
     * Helps to render all the search histiry including recent ones
     * 
     * @since 1.0.0
     * @return void
     */
    public static function render() {
        $table = new self();
        $table->prepare_items();
        ?>
        <div class="wrap">
            <h1 class="wp-heading-inline"><?php esc_html_e('Search History', 'wp-search-any'); ?></h1>
            <hr class="wp-header-end">
            <form method="get">
                <input type="hidden" name="page" value="wp-search-any">
                <?php
                $table->search_box(__('Search', 'wp-search-any'), 'search-history');
                $table->display();
                ?>
            </form>
        </div>
        <?php
    }

    /**
     * Constructor
     * 
     * @since 1.0.0
     * @return void
     */
    public function __construct() {
        parent::__construct([
            'singular' => 'search',
            'plural'   => 'searches',
            'ajax'     => false,
        ]);
    }

    /**
     * Wp List Table show listed columns 
     * 
     * @since 1.0.0
     * @return void
     */
    public function get_columns() {
        return [
            'id'            => __('ID', 'wp-search-any'),
            'search_string' => __('Search String', 'wp-search-any'),
            'created_at'    => __('Date Time', 'wp-search-any'),
        ];
    }

    /**
     * Listing all search logs in descending order
     * 
     * @since 1.0.0
     * @return void
     */
    public function prepare_items() {
        global $wpdb;

        $table = $wpdb->prefix . 'wp_search_any_history';

        $per_page    = $this->get_items_per_page('searches_per_page', 20);
        $current_page = $this->get_pagenum();
        $offset      = ($current_page - 1) * $per_page;

        $where = '';
        if (!empty($_REQUEST['s'])) {
            $search = esc_sql($_REQUEST['s']);
            $where = "WHERE search_string LIKE '%{$search}%'";
        }

        $total_items = $wpdb->get_var("SELECT COUNT(*) FROM {$table} {$where}");

        $this->items = $wpdb->get_results(
            $wpdb->prepare(
                "SELECT * FROM {$table} {$where} ORDER BY id DESC LIMIT %d OFFSET %d",
                $per_page,
                $offset
            ),
            ARRAY_A
        );

        $this->set_pagination_args([
            'total_items' => $total_items,
            'per_page'    => $per_page,
        ]);

        $this->_column_headers = [$this->get_columns(), [], []];
    }

    /**
     * related to showcase table
     * 
     * @since 1.0.0
     * @return void
     */
    protected function column_default($item, $column_name) {
        return esc_html($item[$column_name] ?? '');
    }

    /**
     * Manages screen options
     * 
     * @since 1.0.0
     * @return void
     */
    public function get_screen_options() {
        add_screen_option('per_page', [
            'label'   => __('Searches per page', 'wp-search-any'),
            'default' => 20,
            'option'  => 'searches_per_page',
        ]);
    }
}

add_filter('set-screen-option', function ($status, $option, $value) {
    if ($option === 'searches_per_page') {
        return (int) $value;
    }
    return $status;
}, 10, 3);