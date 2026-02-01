<?php
/**
 * Handles shortcode, rewrite rules, custom search query and AJAX
 *
 * @since 1.0.0
 * @package WP_Search_Any
 */

if (!defined('ABSPATH')) {
    exit;
}

class WP_Search_Any_Shortcode {
    /**
     * Constructor
     * 
     * @since 1.0.0
     * @return void
     */
    public function __construct() {
        add_shortcode('wp_search_any', [$this, 'mc_wp_search_any_shortcode_callback']);

        add_action('init', [$this, 'mc_wp_search_any_rewrite']);
        add_filter('user_trailingslashit', [$this, 'mc_remove_trailing_slash_for_search'], 10, 2);
        add_filter('query_vars', [$this, 'mc_wp_search_any_query_vars']);
        add_action('pre_get_posts', [$this, 'mc_pre_get_posts_callback']);
        add_filter('template_include', [$this, 'mc_load_wp_search_any_template']);

        add_filter('posts_join', [$this, 'mc_posts_join'], 10, 2);
        add_filter('posts_search', [$this, 'mc_posts_search'], 10, 2);
        add_filter('posts_groupby', [$this, 'mc_posts_groupby'], 10, 2);

        add_action('wp_enqueue_scripts', [$this, 'mc_enqueue_assets']);

        add_action('wp_ajax_mc_ajax_search', [$this, 'mc_ajax_search']);
        add_action('wp_ajax_nopriv_mc_ajax_search', [$this, 'mc_ajax_search']);
    }

    /**
     * Remove trailing slash from custom search URLs
     *
     * @param string $url
     * @param string $type
     * @return string
     */
    public function mc_remove_trailing_slash_for_search($url, $type) {
        if (strpos($url, '/search/content/') !== false) {
            return untrailingslashit($url);
        }
        return $url;
    }

    /**
     * Shortcode callback - renders form or modal button
     *
     * @param array $atts
     * @return string
     */
    public function mc_wp_search_any_shortcode_callback($atts) {
        $atts = shortcode_atts([
            'variant'     => 'form',
            'button_text' => 'Search',
        ], $atts, 'wp_search_any');

        $options = get_option('mc_wp_search_any_settings', []);
        $ajax_enabled = !empty($options['enable_ajax_search']);

        ob_start();

        if ($atts['variant'] !== 'modal') : ?>
            <form method="get"
                  action="<?php echo esc_url(home_url('/search/content')); ?>"
                  class="mc-search-form"
                  <?php if ($ajax_enabled): ?>data-ajax-search="1"<?php endif; ?>>

                <div class="mc-search-field">
                    <input type="text" name="keys" class="mc-search-input"
                           placeholder="<?php esc_attr_e('Enter your search query', 'wp-search-any'); ?>" required>
                    <div class="mc-ajax-results"></div>
                </div>

                <button type="submit" class="mc-search-button">
                    <?php esc_html_e('Search', 'wp-search-any'); ?>
                </button>
            </form>

        <?php else : ?>

            <button type="button" class="mc-search-open">
                <?php echo esc_html($atts['button_text']); ?>
            </button>

            <div class="mc-search-modal" aria-hidden="true">
                <div class="mc-search-modal-overlay"></div>
                <div class="mc-search-modal-content" role="dialog" aria-modal="true">
                    <button class="mc-search-close" aria-label="<?php esc_attr_e('Close', 'wp-search-any'); ?>">×</button>

                    <h3 class="mc-search-title"><?php esc_html_e('Search in website', 'wp-search-any'); ?></h3>

                    <form method="get"
                          action="<?php echo esc_url(home_url('/search/content')); ?>"
                          class="mc-search-form"
                          <?php if ($ajax_enabled): ?>data-ajax-search="1"<?php endif; ?>>

                        <div class="mc-search-field">
                            <input type="text" name="keys" class="mc-search-input"
                                   placeholder="<?php esc_attr_e('Enter your search query', 'wp-search-any'); ?>"
                                   required autofocus>
                            <div class="mc-ajax-results"></div>
                        </div>

                        <button type="submit" class="mc-search-button">
                            <?php esc_html_e('Search', 'wp-search-any'); ?>
                        </button>
                    </form>
                </div>
            </div>

        <?php endif;

        return ob_get_clean();
    }

    /**
     * Add rewrite rule for /search/content
     *
     * @since 1.0.0
     */
    public function mc_wp_search_any_rewrite() {
        add_rewrite_rule('^search/content/?$', 'index.php?mc_search_page=1', 'top');
    }

    /**
     * Register custom query vars
     *
     * @param array $vars
     * @return array
     */
    public function mc_wp_search_any_query_vars($vars) {
        $vars[] = 'mc_search_page';
        $vars[] = 'keys';
        return $vars;
    }

    /**
     * Modify main query for custom search page
     *
     * @param WP_Query $query
     */
    public function mc_pre_get_posts_callback($query) {
        if (is_admin() || !$query->is_main_query() || !get_query_var('mc_search_page')) {
            return;
        }

        $search_term = sanitize_text_field(get_query_var('keys'));
        if (!$search_term) return;

        static $already_logged = false;
        if ($already_logged) {
        } else {
            global $wpdb;
            $table = $wpdb->prefix . 'wp_search_any_history';

            $wpdb->insert(
                $table,
                ['search_string' => $search_term],
                ['%s']
            );

            $already_logged = true;
        }

        $query->set('paged', max(1, get_query_var('page')));
        $query->set('s', $search_term);

        $post_types = get_post_types(['public' => true], 'names');
        unset($post_types['attachment']);
        $query->set('post_type', $post_types);
    }

    public function mc_posts_search($search, $query) {
        global $wpdb;

        if (
            (!$query->is_main_query() && !$query->get('mc_ajax_search')) ||
            (!get_query_var('mc_search_page') && !$query->get('mc_ajax_search'))
        ) {
            return $search;
        }

        $term = $query->get('s');
        if (!$term) return $search;

        $like = '%' . $wpdb->esc_like($term) . '%';

        $search = $wpdb->prepare(
            " AND (
                {$wpdb->posts}.post_title LIKE %s
                OR {$wpdb->posts}.post_content LIKE %s
                OR pm.meta_value LIKE %s
                OR t.name LIKE %s
            ) ",
            $like, $like, $like, $like
        );

        return $search;
    }

    public function mc_posts_join($join, $query) {
        global $wpdb;

        if (
            (!$query->is_main_query() && !$query->get('mc_ajax_search')) ||
            (!get_query_var('mc_search_page') && !$query->get('mc_ajax_search'))
        ) {
            return $join;
        }

        if (strpos($join, $wpdb->postmeta) === false) {
            $join .= " LEFT JOIN {$wpdb->postmeta} pm ON ({$wpdb->posts}.ID = pm.post_id) ";
        }

        if (strpos($join, $wpdb->terms) === false) {
            $join .= "
            LEFT JOIN {$wpdb->term_relationships} tr ON ({$wpdb->posts}.ID = tr.object_id)
            LEFT JOIN {$wpdb->term_taxonomy} tt ON (tr.term_taxonomy_id = tt.term_taxonomy_id)
            LEFT JOIN {$wpdb->terms} t ON (tt.term_id = t.term_id)
            ";
        }

        return $join;
    }

    public function mc_posts_groupby($groupby, $query) {
        global $wpdb;

        if (
            (!$query->is_main_query() && !$query->get('mc_ajax_search')) ||
            (!get_query_var('mc_search_page') && !$query->get('mc_ajax_search'))
        ) {
            return $groupby;
        }

        return "{$wpdb->posts}.ID";
    }

    /**
     * Load custom search results template
     *
     * @param string $template
     * @return string
     */
    public function mc_load_wp_search_any_template($template) {
        if (get_query_var('mc_search_page')) {
            return WP_SEARCH_ANY_PATH . 'templates/search-results.php';
        }
        return $template;
    }

    /**
     * Enqueue frontend styles and scripts
     *
     * @since 1.0.2
     */
    public function mc_enqueue_assets() {
        wp_enqueue_script('jquery');

        wp_enqueue_script(
            'mc-search-modal',
            WP_SEARCH_ANY_URL . 'assets/js/search-modal.js',
            ['jquery'],
            WP_SEARCH_ANY_VERSION,
            true
        );

        wp_enqueue_style(
            'mc-search-styles',
            WP_SEARCH_ANY_URL . 'assets/css/search.css',
            [],
            WP_SEARCH_ANY_VERSION
        );

        $options = get_option('mc_wp_search_any_settings', []);
        $ajax_enabled = !empty($options['enable_ajax_search']);

        if ($ajax_enabled) {
            wp_enqueue_script(
                'mc-ajax-search',
                WP_SEARCH_ANY_URL . 'assets/js/ajax-search.js',
                ['jquery'],
                WP_SEARCH_ANY_VERSION,
                true
            );

            wp_localize_script('mc-ajax-search', 'MC_Search', [
                'ajaxurl' => admin_url('admin-ajax.php'),
                'noimg'   => WP_SEARCH_ANY_URL . 'assets/img/noimg.jpg',
                'nonce'   => wp_create_nonce('mc_ajax_search'),
            ]);
        }
    }

    /**
     * Handle AJAX search requests
     */
    public function mc_ajax_search() {
        check_ajax_referer('mc_ajax_search', 'nonce');

        $term = sanitize_text_field($_GET['term'] ?? '');
        if (strlen($term) < 2) {
            wp_send_json([]);
        }

        $args = [
            'post_type'      => get_post_types(['public' => true]),
            'post_status'    => 'publish',
            'posts_per_page' => 5,
            's'              => $term,
            'mc_ajax_search' => true,
        ];

        $query = new WP_Query($args);
        $results = [];

        while ($query->have_posts()) {
            $query->the_post();

            $img = get_the_post_thumbnail_url(get_the_ID(), 'thumbnail')
                ?: WP_SEARCH_ANY_URL . 'assets/img/noimg.jpg';

            $results[] = [
                'title' => get_the_title(),
                'url'   => get_permalink(),
                'img'   => $img,
                'desc'  => wp_trim_words(strip_tags(get_the_excerpt()), 25),
            ];
        }

        wp_reset_postdata();
        wp_send_json($results);
    }
}