<?php
get_header();

global $wpdb, $wp_query;
$current_page = get_query_var('page') ? absint(get_query_var('page')) : 1;
$search_term = trim(get_query_var('keys'));

// Store search history ONLY on first page
if (!empty($search_term) && get_query_var('paged') <= 1) {

    $table = $wpdb->prefix . 'website_search_history';

    // Prevent duplicate insert on refresh (5 min window)
    $recent = $wpdb->get_var(
        $wpdb->prepare(
            "SELECT id FROM {$table}
             WHERE search_string = %s
             AND created_at >= (NOW() - INTERVAL 5 MINUTE)
             LIMIT 1",
            $search_term
        )
    );

    if (!$recent) {
        $wpdb->insert(
            $table,
            ['search_string' => $search_term],
            ['%s']
        );
    }
}
?>

<div class="mc-wp-search-any-wrapper container">

    <h1 style="color: black;">
        Search results for:
        <strong><?php echo esc_html($search_term); ?></strong>
    </h1>
    <p class="search-count">
        <?php echo esc_html($wp_query->found_posts . ' results found.'); ?>
    </p>
    <br>
    
    <div class="row">
        <div class="col-4">
            <form method="get" action="<?php echo esc_url(home_url('/search/content')); ?>">
                <input type="text" name="keys" class="form-control" 
                       placeholder="Enter your search query" 
                       value="<?php echo esc_attr($search_term); ?>" required>
                <button type="submit" class="btn btn-primary">Search</button>
            </form>
        </div>
        <div class="col-8"></div>
    </div>
    <hr>

    <?php if (have_posts()) : ?>

        <ul class="mc-wp-search-any-results">
            <?php while (have_posts()) : the_post();
            ?>
                <li class="mc-wp-search-any-item">
                    <?php if(has_post_thumbnail(get_the_ID())){ ?>
                        <img src="<?php echo get_the_post_thumbnail_url(get_the_ID(), 'full' ); ?>" alt="" style="max-height: 100px; max-width: 100px;">
                    <?php }else{ ?>
                        <img src="<?php echo WP_SEARCH_ANY_URL."/assets/img/noimg.jpg"; ?>" alt="" style="max-height: 100px; max-width: 100px;">
                    <?php } ?>
                    <h2>
                        <a href="<?php the_permalink(); ?>">
                            <?php the_title(); ?>
                        </a>
                    </h2>
                    <div class="mc-wp-search-any-excerpt">
                        <?php the_excerpt(); ?>
                    </div>
                </li>
            <?php endwhile; ?>
        </ul>

        <div class="sdw-pagination">
            <?php
            echo paginate_links([
                'base'     => home_url('/search/content') . '?keys=' . urlencode($search_term) . '%_%',
                'format'   => '&page=%#%',
                'current'  => max(1, get_query_var('page')),
                'total'    => $wp_query->max_num_pages,
            ]);
            ?>
        </div>

    <?php else : ?>

        <div class="sdw-no-results">
            <h2>No results found</h2>
            <p>Please try a different keyword.</p>
        </div>

    <?php endif; ?>

</div>

<?php
get_footer();