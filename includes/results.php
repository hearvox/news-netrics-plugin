<?php
/**
 * HTML tables and lists to display results, for single Publication and site-wide averages.
 *
 * @since   0.1.0
 *
 * @package    News Netrics
 * @subpackage news-netrics/includes
 */

/* ------------------------------------------------------------------------ *
 * HTML Tables
 * ------------------------------------------------------------------------ */
/**
 * Outputs HTML table body rows with mean and median all-Pubs averages.
 *
 * @param array $month_avgs  PSI means and medians.
 * @return void
 */
function netrics_print_pubs_avgs_table( $month_avgs = array(), $title = '' ) {
    if ( ! $month_avgs ) { // If no averages supplied, get averages for all Pubs.
        $pubs_avgs  = get_transient( 'netrics_psi' );
        $month_avgs = end( $pubs_avgs );
        $month      = key( array_slice( $pubs_avgs, -1, 1, true ) );
    } else {
        $month = $month_avgs['date'];
    }

    $metrics = netrics_get_pagespeed_metrics();
    ?>
    <table class="tabular">
        <caption><?php echo $title; ?>U.S. daily newspapers: average PSI results (<output><?php echo $month_avgs['results']; ?></output> articles from <output><?php echo $month_avgs['total']; ?></output> papers in <?php echo $month ?>)</caption>
        <?php echo netrics_pubs_avgs_table_head(); ?>
        <tbody>
            <tr>
                <th scope="row"><?php esc_attr_e( 'Mean', 'newsnetrics' ); ?></th>
                <?php
                foreach ( $metrics as $metric ) {
                    echo '<td>' . netrics_pagespeed_format( $metric, $month_avgs[ $metric ] ) . '</td>';
                }
                ?>
            </tr>
            <tr>
                <th scope="row"><?php esc_attr_e( 'Median', 'newsnetrics' ); ?></th>
                <?php
                foreach ( $metrics as $metric ) {
                    echo '<td>' . netrics_pagespeed_format( $metric, $month_avgs[ $metric . '-q2' ] ) . '</td>';
                }
                ?>
            </tr>
        </tbody>
    </table>
    <?php
}

/**
 * Outputs HTML table headings (<th>) for PSI metrics.
 *
 * @return string $thead HTML table headings.
 */
function netrics_pubs_avgs_table_head() {
    $thead   = '';
    $metrics = netrics_get_pagespeed_metrics();

    // Build table head HTML.
    $thead  .= '<thead><tr><td></td>';

    foreach ( $metrics as $metric ) {
        $thead .= "<th scope=\"col\">$metric</th>";
    }

    $thead  .= '</tr></thead>';

    return $thead;
}


/**
 * Outputs HTML table head and body with array averages and medians.
 *
 */
function netrics_pagespeed_mean( $array, $tbody = true ) {
    $thead   = '';
    $results = array( 'dom', 'requests', 'size', 'speed', 'tti', 'score' );
    ?>
    <thead>
        <td style="width: 11rem;"></td>
        <?php foreach ($results as $key => $result ) { ?>
        <th><?php echo $result ?></th>
        <?php } ?>
    </thead>
    <tbody>
        <tr>
            <th scope="row"><?php esc_attr_e( 'Mean', 'newsnetrics' ); ?></th>
            <td><?php echo number_format( nstats_mean( $array['dom'] ), 1, '.', ',' ); ?></td>
            <td><?php echo number_format( nstats_mean( $array['requests'] ), 1, '.', ',' ); ?></td>
            <td><?php echo size_format( nstats_mean( $array['size'] ), 1 ); ?></td>
            <td><?php echo number_format( nstats_mean( $array['speed'] ) / 1000, 1, '.', ',' ); ?></td>
            <td><?php echo number_format( nstats_mean( $array['tti'] ) / 1000, 1, '.', ',' ); ?></td>
            <td><?php echo number_format( nstats_mean( $array['score'] ) * 100, 1, '.', ',' ); ?></td>
        </tr>
        <tr>
            <th scope="col"><?php esc_attr_e( 'Median', 'newsnetrics' ); ?></th>
            <td><?php echo number_format( nstats_q2( $array['dom'] ), 1, '.', ',' ); ?></td>
            <td><?php echo number_format( nstats_q2( $array['requests'] ), 1, '.', ',' ); ?></td>
            <td><?php echo size_format( nstats_q2( $array['size'] ), 1 ); ?></td>
            <td><?php echo number_format( nstats_q2( $array['speed'] ) / 1000, 1, '.', ',' ); ?></td>
            <td><?php echo number_format( nstats_q2( $array['tti'] ) / 1000, 1, '.', ',' ); ?></td>
            <td><?php echo number_format( nstats_q2( $array['score'] ) * 100, 1, '.', ',' ); ?></td>
        </tr>
    <?php if ( $tbody ) { // Close or leave open ?>
    </tbody>
    <?php
    }
}

/**
 * Outputs HTML with array averages, quartiles, and standard deviations.
 *
 */
function netrics_pagespeed( $array ) {
    $thead   = '';
    $results = array( 'dom', 'requests', 'size', 'speed', 'tti', 'score' );
    ?>
    <thead>
        <td style="width: 11rem;"></td>
        <?php foreach ($results as $key => $result ) { ?>
        <th><?php echo $result ?></th>
        <?php } ?>
    </thead>
    <tbody>
        <tr>
            <th scope="row"><?php esc_attr_e( 'Mean', 'newsnetrics' ); ?></th>
            <td><?php echo number_format( nstats_mean( $array['dom'] ), 1, '.', ',' ); ?></td>
            <td><?php echo number_format( nstats_mean( $array['requests'] ), 1, '.', ',' ); ?></td>
            <td><?php echo size_format( nstats_mean( $array['size'] ), 1 ); ?></td>
            <td><?php echo number_format( nstats_mean( $array['speed'] ) / 1000, 1, '.', ',' ); ?></td>
            <td><?php echo number_format( nstats_mean( $array['tti'] ) / 1000, 1, '.', ',' ); ?></td>
            <td><?php echo number_format( nstats_mean( $array['score'] ) * 100, 1, '.', ',' ); ?></td>
        </tr>
        <tr>
            <th scope="col"><?php esc_attr_e( 'Quartile 1', 'newsnetrics' ); ?></th>
            <td><?php echo number_format( nstats_q1( $array['dom'] ), 1, '.', ',' ); ?></td>
            <td><?php echo number_format( nstats_q1( $array['requests'] ), 1, '.', ',' ); ?></td>
            <td><?php echo size_format( nstats_q1( $array['size'] ), 1 ); ?></td>
            <td><?php echo number_format( nstats_q1( $array['speed'] ) / 1000, 1, '.', ',' ); ?></td>
            <td><?php echo number_format( nstats_q1( $array['tti'] ) / 1000, 1, '.', ',' ); ?></td>
            <td><?php echo number_format( nstats_q1( $array['score'] ) * 100, 1, '.', ',' ); ?></td>
        </tr>
        <tr>
            <th scope="col"><?php esc_attr_e( 'Q2/Median', 'newsnetrics' ); ?></th>
            <td><?php echo number_format( nstats_q2( $array['dom'] ), 1, '.', ',' ); ?></td>
            <td><?php echo number_format( nstats_q2( $array['requests'] ), 1, '.', ',' ); ?></td>
            <td><?php echo size_format( nstats_q2( $array['size'] ), 1 ); ?></td>
            <td><?php echo number_format( nstats_q2( $array['speed'] ) / 1000, 1, '.', ',' ); ?></td>
            <td><?php echo number_format( nstats_q2( $array['tti'] ) / 1000, 1, '.', ',' ); ?></td>
            <td><?php echo number_format( nstats_q2( $array['score'] ) * 100, 1, '.', ',' ); ?></td>
        </tr>
        <tr>
            <th scope="col"><?php esc_attr_e( 'Quartile 3', 'newsnetrics' ); ?></th>
            <td><?php echo number_format( nstats_q3( $array['dom'] ), 1, '.', ',' ); ?></td>
            <td><?php echo number_format( nstats_q3( $array['requests'] ), 1, '.', ',' ); ?></td>
            <td><?php echo size_format( nstats_q3( $array['size'] ), 1 ); ?></td>
            <td><?php echo number_format( nstats_q3( $array['speed'] ) / 1000, 1, '.', ',' ); ?></td>
            <td><?php echo number_format( nstats_q3( $array['tti'] ) / 1000, 1, '.', ',' ); ?></td>
            <td><?php echo number_format( nstats_q3( $array['score'] ) * 100, 1, '.', ',' ); ?></td>
        </tr>
        <tr>
            <th scope="col"><?php esc_attr_e( 'Interquartile Range', 'newsnetrics' ); ?></th>
            <td><?php echo number_format( nstats_iqr( $array['dom'] ), 1, '.', ',' ); ?></td>
            <td><?php echo number_format( nstats_iqr( $array['requests'] ), 1, '.', ',' ); ?></td>
            <td><?php echo size_format( nstats_iqr( $array['size'] ), 1 ); ?></td>
            <td><?php echo number_format( nstats_iqr( $array['speed'] ) / 1000, 1, '.', ',' ); ?></td>
            <td><?php echo number_format( nstats_iqr( $array['tti'] ) / 1000, 1, '.', ',' ); ?></td>
            <td><?php echo number_format( nstats_iqr( $array['score'] ) * 100, 1, '.', ',' ); ?></td>
        </tr>
        <tr>
            <th scope="row"><?php esc_attr_e( 'Maximum', 'newsnetrics' ); ?></th>
            <td><?php echo number_format( nstats_max( $array['dom'] ), 1, '.', ',' ); ?></td>
            <td><?php echo number_format( nstats_max( $array['requests'] ), 1, '.', ',' ); ?></td>
            <td><?php echo size_format( nstats_max( $array['size'] ), 1 ); ?></td>
            <td><?php echo number_format( nstats_max( $array['speed'] ) / 1000, 1, '.', ',' ); ?></td>
            <td><?php echo number_format( nstats_max( $array['tti'] ) / 1000, 1, '.', ',' ); ?></td>
            <td><?php echo number_format( nstats_max( $array['score'] ) * 100, 1, '.', ',' ); ?></td>
        </tr>
        <tr>
            <th scope="row"><?php esc_attr_e( 'Minimum', 'newsnetrics' ); ?></th>
            <td><?php echo number_format( nstats_min( $array['dom'] ), 1, '.', ',' ); ?></td>
            <td><?php echo number_format( nstats_min( $array['requests'] ), 1, '.', ',' ); ?></td>
            <td><?php echo size_format( nstats_min( $array['size'] ), 1 ); ?></td>
            <td><?php echo number_format( nstats_min( $array['speed'] ) / 1000, 1, '.', ',' ); ?></td>
            <td><?php echo number_format( nstats_min( $array['tti'] ) / 1000, 1, '.', ',' ); ?></td>
            <td><?php echo number_format( nstats_min( $array['score'] ) * 100, 1, '.', ',' ); ?></td>
        </tr>
        <tr>
            <th scope="row"><?php esc_attr_e( 'Range', 'newsnetrics' ); ?></th>
            <td><?php echo number_format( nstats_range( $array['dom'] ), 1, '.', ',' ); ?></td>
            <td><?php echo number_format( nstats_range( $array['requests'] ), 1, '.', ',' ); ?></td>
            <td><?php echo size_format( nstats_range( $array['size'] ), 1 ); ?></td>
            <td><?php echo number_format( nstats_range( $array['speed'] ) / 1000, 1, '.', ',' ); ?></td>
            <td><?php echo number_format( nstats_range( $array['tti'] ) / 1000, 1, '.', ',' ); ?></td>
            <td><?php echo number_format( nstats_range( $array['score'] ) * 100, 1, '.', ',' ); ?></td>
        </tr>
        <tr>
            <th scope="col"><?php esc_attr_e( 'Standard Deviation', 'newsnetrics' ); ?></th>
            <td><?php echo number_format( nstats_sd( $array['dom'] ), 1, '.', ',' ); ?></td>
            <td><?php echo number_format( nstats_sd( $array['requests'] ), 1, '.', ',' ); ?></td>
            <td><?php echo size_format( nstats_sd( $array['size'] ), 1 ); ?></td>
            <td><?php echo number_format( nstats_sd( $array['speed'] ) / 1000, 1, '.', ',' ); ?></td>
            <td><?php echo number_format( nstats_sd( $array['tti'] ) / 1000, 1, '.', ',' ); ?></td>
            <td><?php echo number_format( nstats_sd( $array['score'] ) * 100, 1, '.', ',' ); ?></td>
        </tr>
    </tbody>

   <?php
}

/**
 * Outputs HTML with array averages, quartiles, and standard deviations.
 *
 */
function netrics_pagespeed_tbody( $array, $all = 1 ) {
    $tbody   = '';
    $metrics = netrics_get_pagespeed_metrics();
    $stats   = array( 'Mean', 'Maximum', 'Minimum', 'Range'. 'Quartile 1', 'Q2/Median', 'Quartile 1', 'Interquartile Range', 'Standard Deviation' );

    $tbody .= '<tr><th scope="row">Mean</th>';
    foreach ( $metrics as $metric ) {
        $tbody .= '<td>' . netrics_pagespeed_format( $metric, nstats_mean( $array[ $metric ] ), 1 )  . '</td>';
    }
    $tbody .= '</tr>';

    if ( $all ) {

        $tbody .= '<tr><th scope="row">Maximum</th>';
        foreach ( $metrics as $metric ) {
            $tbody .= '<td>' . netrics_pagespeed_format( $metric, nstats_max( $array[ $metric ] ), 1 )  . '</td>';
        }
        $tbody .= '</tr>';

        $tbody .= '<tr><th scope="row">Minimum</th>';
        foreach ( $metrics as $metric ) {
            $tbody .= '<td>' . netrics_pagespeed_format( $metric, nstats_min( $array[ $metric ] ), 1 )  . '</td>';
        }
        $tbody .= '</tr>';

        $tbody .= '<tr><th scope="row">Range</th>';
        foreach ( $metrics as $metric ) {
            $tbody .= '<td>' . netrics_pagespeed_format( $metric, nstats_range( $array[ $metric ] ), 1 )  . '</td>';
        }
        $tbody .= '</tr>';

        $tbody .= '<tr><th scope="row">Quartile 1</th>';
        foreach ( $metrics as $metric ) {
            $tbody .= '<td>' . netrics_pagespeed_format( $metric, nstats_q1( $array[ $metric ] ), 1 )  . '</td>';
        }
        $tbody .= '</tr>';

        $tbody .= '<tr><th scope="row">Q2/Median</th>';
        foreach ( $metrics as $metric ) {
            $tbody .= '<td>' . netrics_pagespeed_format( $metric, nstats_q2( $array[ $metric ] ), 1 )  . '</td>';
        }
        $tbody .= '</tr>';

        $tbody .= '<tr><th scope="row">Quartile 3</th>';
        foreach ( $metrics as $metric ) {
            $tbody .= '<td>' . netrics_pagespeed_format( $metric, nstats_q3( $array[ $metric ] ), 1 )  . '</td>';
        }
        $tbody .= '</tr>';

        $tbody .= '<tr><th scope="row">Interquartile Range</th>';
        foreach ( $metrics as $metric ) {
            $tbody .= '<td>' . netrics_pagespeed_format( $metric, nstats_iqr( $array[ $metric ] ), 1 )  . '</td>';
        }
        $tbody .= '</tr>';

        $tbody .= '<tr><th scope="row">Standard Deviation</th>';
        foreach ( $metrics as $metric ) {
            $tbody .= '<td>' . netrics_pagespeed_format( $metric, nstats_sd( $array[ $metric ] ), 1 )  . '</td>';
        }
        $tbody .= '</tr>';

    } else {
        $tbody .= '<tr><th scope="row">Median</th>';
        foreach ( $metrics as $metric ) {
            $tbody .= '<td>' . netrics_pagespeed_format( $metric, nstats_q2( $array[ $metric ] ), 1 )  . '</td>';
        }
        $tbody .= '</tr>';
    }

    return $tbody;
}

/**
 * Outputs HTML table headings (<th>) for PSI metrics.
 *
 * @return string $thead  HTML table headings.
 */
function netrics_pagespeed_thead() {
    $thead   = '';
    $metrics = netrics_get_pagespeed_metrics();

    foreach ( $metrics as $metric ) {
        $thead .= "<th>$metric</th>";
    }

    return $thead;
}

/**
 * Outputs HTML with array averages, quartiles, and standard deviations.
 *
 */
function netrics_pagespeed_corr( $array ) {
    $thead     = '';
    $results = array( 'dom', 'requests', 'size', 'speed', 'tti', 'score' );
    ?>
<table class="tabular" style="">
    <caption>U.S. daily newspapers: Correlations of Pagespeed/Lighthouse metrics (2019-08)</caption>
    <thead>
        <td style="width: 11rem;"></td>
        <?php foreach ($results as $key => $result ) { ?>
        <th><?php echo $result ?></th>
        <?php } ?>
    </thead>
    <tbody>
        <tr>
            <th scope="col"><?php esc_attr_e( 'dom', 'newsnetrics' ); ?></th>
            <td><?php echo round( nstats_correlation( $array['dom'], $array['dom'] ), 1); ?></td>
            <td><?php echo round( nstats_correlation( $array['dom'], $array['requests'] ), 1); ?></td>
            <td><?php echo round( nstats_correlation( $array['dom'], $array['size'] ), 1); ?></td>
            <td><?php echo round( nstats_correlation( $array['dom'], $array['speed'] ), 1); ?></td>
            <td><?php echo round( nstats_correlation( $array['dom'], $array['tti'] ), 1); ?></td>
            <td><?php echo round( nstats_correlation( $array['dom'], $array['score'] ), 1); ?></td>        </tr>
        <tr>
            <th scope="col"><?php esc_attr_e( 'requests', 'newsnetrics' ); ?></th>
            <td><?php echo round( nstats_correlation( $array['requests'], $array['dom'] ), 1); ?></td>
            <td><?php echo round( nstats_correlation( $array['requests'], $array['requests'] ), 1); ?></td>
            <td><?php echo round( nstats_correlation( $array['requests'], $array['size'] ), 1); ?></td>
            <td><?php echo round( nstats_correlation( $array['requests'], $array['speed'] ), 1); ?></td>
            <td><?php echo round( nstats_correlation( $array['requests'], $array['tti'] ), 1); ?></td>
            <td><?php echo round( nstats_correlation( $array['requests'], $array['score'] ), 1); ?></td>
        </tr>
        <tr>
            <th scope="row"><?php esc_attr_e( 'size', 'newsnetrics' ); ?></th>
            <td><?php echo round( nstats_correlation( $array['size'], $array['dom'] ), 1); ?></td>
            <td><?php echo round( nstats_correlation( $array['size'], $array['requests'] ), 1); ?></td>
            <td><?php echo round( nstats_correlation( $array['size'], $array['size'] ), 1); ?></td>
            <td><?php echo round( nstats_correlation( $array['size'], $array['speed'] ), 1); ?></td>
            <td><?php echo round( nstats_correlation( $array['size'], $array['tti'] ), 1); ?></td>
            <td><?php echo round( nstats_correlation( $array['size'], $array['score'] ), 1); ?></td>
        </tr>


        <tr>
            <th scope="row"><?php esc_attr_e( 'speed', 'newsnetrics' ); ?></th>
            <td><?php echo round( nstats_correlation( $array['speed'], $array['dom'] ), 1); ?></td>
            <td><?php echo round( nstats_correlation( $array['speed'], $array['requests'] ), 1); ?></td>
            <td><?php echo round( nstats_correlation( $array['speed'], $array['size'] ), 1); ?></td>
            <td><?php echo round( nstats_correlation( $array['speed'], $array['speed'] ), 1); ?></td>
            <td><?php echo round( nstats_correlation( $array['speed'], $array['tti'] ), 1); ?></td>
            <td><?php echo round( nstats_correlation( $array['speed'], $array['score'] ), 1); ?></td>
        </tr>

        <tr>
            <th scope="row"><?php esc_attr_e( 'tti', 'newsnetrics' ); ?></th>
            <td><?php echo round( nstats_correlation( $array['tti'], $array['dom'] ), 1); ?></td>
            <td><?php echo round( nstats_correlation( $array['tti'], $array['requests'] ), 1); ?></td>
            <td><?php echo round( nstats_correlation( $array['tti'], $array['size'] ), 1); ?></td>
            <td><?php echo round( nstats_correlation( $array['tti'], $array['speed'] ), 1); ?></td>
            <td><?php echo round( nstats_correlation( $array['tti'], $array['tti'] ), 1); ?></td>
            <td><?php echo round( nstats_correlation( $array['tti'], $array['score'] ), 1); ?></td>
        </tr>
        <tr>
            <th scope="row"><?php esc_attr_e( 'score', 'newsnetrics' ); ?></th>
            <td><?php echo round( nstats_correlation( $array['score'], $array['dom'] ), 1); ?></td>
            <td><?php echo round( nstats_correlation( $array['score'], $array['requests'] ), 1); ?></td>
            <td><?php echo round( nstats_correlation( $array['score'], $array['size'] ), 1); ?></td>
            <td><?php echo round( nstats_correlation( $array['score'], $array['speed'] ), 1); ?></td>
            <td><?php echo round( nstats_correlation( $array['score'], $array['tti'] ), 1); ?></td>
            <td><?php echo round( nstats_correlation( $array['score'], $array['score'] ), 1); ?></td>
        </tr>
    </tbody>
    <tfoot>
        <tr>
            <th scope="row"><?php esc_attr_e( 'Results for:', 'newsnetrics' ); ?></th>
            <td colspan="6" style="text-align: left;">3,070 articles from 1,126 newspapers</td>
        </tr>
    </tfoot>
</table>
   <?php
}

/* ------------------------------------------------------------------------ *
 * PageSpeed Insights results
 * ------------------------------------------------------------------------ */

/**
 * Clear taxonomy terms from posts.
 *
 * Default terms for monthly feed and PSI:
 * 6177 'Articles',
 * 6178 '1PageSpeed'.
 *
 * @param
 * @return
 */
function netrics_clear_post_terms( $tax = 'flag', $term_ids = array( 6177, 6178 ) ) {
    $args = array(
        'post_type'      => 'publication',
        'orderby'        => 'title',
        'order'          => 'ASC',
        'posts_per_page' => 2000,
        'offset'         => 0,
        'fields'         => 'ids',
        'tax_query' => array(
            array(
                'taxonomy' => $tax,
                'field'    => 'term_id',
                'terms'    => $term_ids,
            ),
        ),
    );
    $query_ids = new WP_Query( $args );

    foreach ( $query_ids as $post_id ) {
        // wp_remove_object_terms( $post_id, $term_ids, $tax ); // Monthly flags: feed and PSI.
    }

    return $query_ids;
}

/**
 * Add Pub's current-month articles/PSI-results to PSI history.
 *
 * Post meta: add array 'nn_articles_new' to 'nn_articles'.
 *
 * @param
 * @return
 */
function netrics_add_month_psi() {
    $month_done = 6179; // '1PageSpeed';
    $month = date( 'Y-m' );

    $args = array(
        'post_type'      => 'publication',
        'orderby'        => 'title',
        'order'          => 'ASC',
        'posts_per_page' => 3000,
        'offset'         => 0,
        'fields'         => 'ids',
        'tax_query'      => array(
            'relation' => 'AND',
            array(
                'taxonomy' => 'flag',
                'field'    => 'term_id',
                'terms'    => $month_done,
            ),
        ),
    );
    $query_ids = new WP_Query( $args );

    foreach( $query_ids->posts as $post_id ) {
        $articles_all = get_post_meta( $post_id, 'nn_articles', true );
        $articles_add = get_post_meta( $post_id, 'nn_articles_new', true );

        $articles_all[ $month ] = $articles_add;

        update_post_meta( $post_id, 'nn_articles', $articles_all );

        // Print results.
        print_r( get_post_meta( $post_id, 'nn_articles', true ) );
    }

    wp_reset_postdata();
}

/**
 * Calculate Pub's current PSI averages; save in averages history and monthly score.
 *
 * Post meta: average PSI metrics in 'nn_articles_new'.
 * Save averages array to 'nn_psi_avgs' and number to 'nn_psi_score'.
 *
 * @param
 * @return
 */
 function netrics_add_month_psi_avgs() {
    $month_done = 6179; // '1PageSpeed';
    $month = date( 'Y-m' );

    $args = array(
        'post_type'      => 'publication',
        'orderby'        => 'title',
        'order'          => 'ASC',
        'posts_per_page' => 3000,
        'offset'         => 0,
        'fields'         => 'ids',
        'tax_query'      => array(
            'relation' => 'AND',
            array(
                'taxonomy' => 'flag',
                'field'    => 'term_id',
                'terms'    => $month_done,
            ),
        ),
    );
    $query_ids = new WP_Query( $args );

    foreach( $query_ids->posts as $post_id ) {
        $nn_psi_avgs = get_post_meta( $post_id, 'nn_psi_avgs', true );
        $articles    = get_post_meta( $post_id, 'nn_articles', true );

        // Add current month's results.
        if ( isset( $articles[ $month ] ) ) {
            // foreach ( $articles as $month => $results ) { // All months.
            foreach ( $articles[ $month ] as $results ) {
                $nn_psi_avgs[ $month ] = netrics_pagespeed_avgs( $post_id, $month );
            }

            $avgs  = update_post_meta( $post_id, 'nn_psi_avgs', $nn_psi_avgs );
            $score = ( isset( $nn_psi_avgs[ $month ]['score'] ) ) ? $nn_psi_avgs[ $month ]['score'] : '';
            update_post_meta( $post_id, 'nn_psi_score', $score );

            // Print results.
            print_r( get_post_meta( $post_id, 'nn_psi_avgs', true ) );
            echo $post_id . ' ' . get_post_meta( $post_id, 'nn_psi_score', true ) . "\n";
        }
    }
}

/**
 * Calculate combined PSI averages for a set of Pubs (e.g., with a taxonomy term).
 *
 * @param array $post_ids    Array of Post IDs (for the set of Pubs).
 * @return array $pubs_avgs  Array of combined means and medians.
 */
 function netrics_pubs_psi_avgs( $post_ids ) {
    $psi_avgs  = get_transient( 'netrics_psi_avgs' ); // Posts in with current PSI results.
    $id_keys   = array_flip( $post_ids ); // Make Post IDs (values) be the keys.
    $pubs_psi  = array_intersect_key( $psi_avgs, $id_keys ); // Posts in set with results.
    $pubs_avgs = array();

    // Averages of averages for Pubs.
    $all_scores = wp_list_pluck( $pubs_psi, 'score' );
    $all_speeds = wp_list_pluck( $pubs_psi, 'speed' );
    $all_ttis   = wp_list_pluck( $pubs_psi, 'tti' );
    $all_sizes  = wp_list_pluck( $pubs_psi, 'size' );
    $all_reqs   = wp_list_pluck( $pubs_psi, 'requests' );
    $all_doms   = wp_list_pluck( $pubs_psi, 'dom' );

    // Get date from first item in array.
    $pubs_avgs['date']  = reset( $pubs_psi )['date'];
    // Number of articles (array_sum) and papers (count) with results.
    $pubs_avgs['results']  = array_sum( wp_list_pluck( $pubs_psi, 'results' ) );
    $pubs_avgs['total']    = count( $pubs_psi ); // Number of
    // Calculate means for PSI metrics.
    $pubs_avgs['score']    = nstats_mean( $all_scores );
    $pubs_avgs['speed']    = nstats_mean( $all_speeds );
    $pubs_avgs['tti']      = nstats_mean( $all_ttis );
    $pubs_avgs['size']     = nstats_mean( $all_sizes );
    $pubs_avgs['requests'] = nstats_mean( $all_reqs );
    $pubs_avgs['dom']      = nstats_mean( $all_doms );
    // Calculate medians (2nd quartile).
    $pubs_avgs['score-q2']    = nstats_q2( $all_scores );
    $pubs_avgs['speed-q2']    = nstats_q2( $all_speeds );
    $pubs_avgs['tti-q2']      = nstats_q2( $all_ttis );
    $pubs_avgs['size-q2']     = nstats_q2( $all_sizes );
    $pubs_avgs['requests-q2'] = nstats_q2( $all_reqs );
    $pubs_avgs['dom-q2']      = nstats_q2( $all_doms );

    return $pubs_avgs;
}

/**
 * Get PageSpeed averages for all articles of a Publication with results.
 *
 * @param  int    $post_id  ID of a post.
 * @param  string $date     Month of PSI tests (YYYY-MM).
 * @return array  $pub_psi  Array of PageSpeed averages.
 */
function netrics_pagespeed_avgs( $post_id, $date = null ) {
    // Used in: theme>>archive.php, page-data-list-articles.php, taxonomy-owner.php.
    if ( ! $date ) {
        // get_option( 'netrics_month' );
        $date = date( 'Y-m' );
    }

    $data    = get_post_meta( $post_id, 'nn_articles', true);
    $items   = $data[ $date ];
    $pub_psi = array();

    if ( ! $items || ! isset( $items[0]['pagespeed'] ) ) {
        return $pub_psi;
    }

    $pagespeed = wp_list_pluck( $items, 'pagespeed' );
    $errors    = wp_list_pluck( $pagespeed, 'error' );

    if ( ! in_array( 0, $errors) ) { // Proceed only if results (i.e, no error = 0).
        return $pub_psi;
    }

    foreach ( $pagespeed as $key => $result ) { // Remove item if no results (error > 0).
        if ( $result['error'] ) {
            unset( $pagespeed[$key] );
        }
    }

    // Results for Pub's articles.
    $pub_scores = wp_list_pluck( $pagespeed, 'score' );
    $pub_speeds = wp_list_pluck( $pagespeed, 'speed' );
    $pub_ttis   = wp_list_pluck( $pagespeed, 'tti' );
    $pub_sizes  = wp_list_pluck( $pagespeed, 'size' );
    $pub_reqs   = wp_list_pluck( $pagespeed, 'requests' );
    $pub_doms   = wp_list_pluck( $pagespeed, 'dom' );

    // Number of articles with results.
    $pub_psi['results']  = count( $pagespeed );
    // Calculate means for Pub's articles.
    $pub_psi['score']    = nstats_mean( $pub_scores );
    $pub_psi['speed']    = nstats_mean( $pub_speeds );
    $pub_psi['tti']      = nstats_mean( $pub_ttis );
    $pub_psi['size']     = nstats_mean( $pub_sizes );
    $pub_psi['requests'] = nstats_mean( $pub_reqs );
    $pub_psi['dom']      = nstats_mean( $pub_doms );
    // Calculate medians (2nd quartile).
    $pub_psi['score-q2']    = nstats_q2( $pub_scores );
    $pub_psi['speed-q2']    = nstats_q2( $pub_speeds );
    $pub_psi['tti-q2']      = nstats_q2( $pub_ttis );
    $pub_psi['size-q2']     = nstats_q2( $pub_sizes );
    $pub_psi['requests-q2'] = nstats_q2( $pub_reqs );
    $pub_psi['dom-q2']      = nstats_q2( $pub_doms );

    return $pub_psi;
}

/**
 * Calculate all Pubs' current PSI averages; save in averages history.
 *
 * Post Meta: average all Pubs' 'nn_psi_avgs'.
 * Transient: Save averages array to 'netrics_psi'
 * and individual Pub's to 'netrics_psi_avgs'.
 *
 * @param
 * @return
 */
 function netrics_add_month_psi_avgs_all() {
    $month = date( 'Y-m' );
    $query = netrics_get_pub_posts();

    $pubs_psi = $site_psi = array();

    // Array of each Pub's current PSI averages.
    foreach ( $query->posts as $post ) {
        // Get all months of Pub's PSI averages.
        $psi_avgs = get_post_meta( $post->ID, 'nn_psi_avgs', true);

        // Add Pub's current month's Pub's PSI average to array.
        if ( isset( $psi_avgs[ $month ]['score'] ) ) {
            $pubs_psi[ $post->ID ] = $psi_avgs[ $month ]; // Current averages.
            $pubs_psi[ $post->ID ][ 'date' ] =  $month; // Current month.
        }
    }

    // Store array of current each-Pub PSI averages.
    set_transient( 'netrics_psi_avgs', $pubs_psi, 70 * DAY_IN_SECONDS );

    // Compile averages of averages for all Pubs.
    $all_scores = wp_list_pluck( $pubs_psi, 'score' );
    $all_speeds = wp_list_pluck( $pubs_psi, 'speed' );
    $all_ttis   = wp_list_pluck( $pubs_psi, 'tti' );
    $all_sizes  = wp_list_pluck( $pubs_psi, 'size' );
    $all_reqs   = wp_list_pluck( $pubs_psi, 'requests' );
    $all_doms   = wp_list_pluck( $pubs_psi, 'dom' );

    // Save averaged averages as array (to be stored in a transient).
    $site_psi[$month]['date']     = $month;
    // Number of articles (array_sum) and papers (count) with results.
    $site_psi[$month]['results']  = array_sum( wp_list_pluck( $pubs_psi, 'results' ) );
    $site_psi[$month]['total']    = count( $pubs_psi ); // Number of
    // Calculate means.
    $site_psi[$month]['score']    = nstats_mean( $all_scores );
    $site_psi[$month]['speed']    = nstats_mean( $all_speeds );
    $site_psi[$month]['tti']      = nstats_mean( $all_ttis );
    $site_psi[$month]['size']     = nstats_mean( $all_sizes );
    $site_psi[$month]['requests'] = nstats_mean( $all_reqs );
    $site_psi[$month]['dom']      = nstats_mean( $all_doms );
    // Calculate medians (2nd quartile).
    $site_psi[$month]['score-q2']    = nstats_q2( $all_scores );
    $site_psi[$month]['speed-q2']    = nstats_q2( $all_speeds );
    $site_psi[$month]['tti-q2']      = nstats_q2( $all_ttis );
    $site_psi[$month]['size-q2']     = nstats_q2( $all_sizes );
    $site_psi[$month]['requests-q2'] = nstats_q2( $all_reqs );
    $site_psi[$month]['dom-q2']      = nstats_q2( $all_doms );

    // Get monthly history of combined averages for all Pubs.
    $netrics_psi = get_transient( 'netrics_psi' );

    // Add month's all-Pubs averages at array (in transient).
    $netrics_psi[ $month ] = $site_psi[ $month ];

    // Store monthly history adding current month's combined averages.
    set_transient( 'netrics_psi', $netrics_psi, 70 * DAY_IN_SECONDS );

    // Log results.
    print_r( get_transient( 'netrics_psi' ) );
    print_r( get_transient( 'netrics_psi_avgs' ) );
}

/**
 * Get PageSpeed averages for all articles of a Publication with results.
 * No longer used.
 *
 * @param  int    $post_id   ID of a post.
 * @param  string $meta_key  Post meta key with PSI results.
 * @return array  $pub_ps    Array of PageSpeed averages.
 */
function netrics_site_pagespeed( $post_id, $meta_key = 'nn_articles_201908' ) {
    // $items_all = get_post_meta( $post_id, 'nn_articles', true);
    // $items     = end( $items_all );
    $items   = get_post_meta( $post_id, $meta_key, true);
    $site_ps = array();

    if ( ! $items || ! isset( $items[0]['pagespeed'] ) ) {
        return $site_ps;
    }

    $pagespeed = wp_list_pluck( $items, 'pagespeed' );
    $errors    = wp_list_pluck( $pagespeed, 'error' );

    if ( ! in_array( 0, $errors) ) { // Proceed only if results (i.e, no error = 0).
        return $site_ps;
    }

    foreach ( $pagespeed as $key => $result ) { // Remove item if no results (error > 0).
        if ( $result['error'] ) {
            unset( $pagespeed[$key] );
        }
    }

    // PageSpeed data.
    $site_ps['score']    = nstats_mean( wp_list_pluck( $pagespeed, 'score' ) );
    $site_ps['speed']    = nstats_mean( wp_list_pluck( $pagespeed, 'speed' ) );
    $site_ps['tti']      = nstats_mean( wp_list_pluck( $pagespeed, 'tti' ) );
    $site_ps['size']     = nstats_mean( wp_list_pluck( $pagespeed, 'size' ) );
    $site_ps['requests'] = nstats_mean( wp_list_pluck( $pagespeed, 'requests' ) );
    $site_ps['dom']      = nstats_mean( wp_list_pluck( $pagespeed, 'dom' ) );
    $site_ps['results']  = count( $pagespeed ); // Number of articles with results.

    return $site_ps;
}

/**
 * Outputs HTML with array averages, quartiles, and standard deviations.
 *
 */
function netrics_pagespeed_results_list( $query, $items ) {
    $list = '';
    foreach ( $items as $item ) {
        $list .= ( isset( $item['url'] ) ) ? "<li><a href=\"{$item['url']}\">{$item['url']}</a>" : '<li>';

        if ( isset( $item['pagespeed']['error'] ) ) {
            $pgspeed = $item['pagespeed'];
            $list .=  '<br><small>';

            if ( ! $pgspeed['error'] ) {
                $list .=  $pgspeed['date'] . ' | ';
                $list .=  'Score: ' . $pgspeed['score'] * 100;
                $list .=  ' | Speed/TTI(s): ' . round( $pgspeed['speed'] / 1000, 1 ) . '/' . round( $pgspeed['tti']  / 1000, 1 );
                $list .=  ' | Size: ' . size_format( $pgspeed['size'], 1 );
                $list .=  ' | DOM: ' . number_format( $pgspeed['dom'] );
                $list .=  ' | Requests: ' . number_format( $pgspeed['requests'] );
            } else {
                $list .= 'Error: ' . $pgspeed['error'];
            }

            $list .=  '</small>';
        }

        $list .=  "</li>";
    }

    return $list;
}


/**
 * Format PageSpeed data numbers for front-end display.
 *
 * $data = netriics_pub_pagespeed( $post_id );
 * foreach ( $data as $k => $v ) {
 *     echo $k . ': ' . netrics_pagespeed_format( $k, $v ). "\n";
 * }
 *
 *
 */
function netrics_pagespeed_format( $metric, $num, $size_unit = 0 ) {
    switch ( $metric ) {
        case 'score':
            $num = number_format( $num * 100, 1, '.', ',' );
            break;
        case 'speed':
            $num = number_format( $num / 1000, 1, '.', ',' );
            break;
        case 'tti':
            $num = number_format( $num / 1000, 1, '.', ',' );
            break;
        case 'size':
            if ( $size_unit ) {
                $num = number_format( $num / 1000000, 1, '.', ',' ); // Number only.
            } else {
                $num = size_format( $num, 1 ); // Number with unit (e.g., MB).
            }
            break;
        case 'requests':
            $num = number_format( $num, 1, '.', ',' );
            break;
        case 'dom':
            $num = number_format( $num, 1, '.', ',' );
            break;
        case 'results':
            break;
        default:
            $num = null;
            break;
    }

    return $num;
}

/**
 * Get text list of PSI results for a Publication.
 *
 * @param  int    $post_id  ID of a post.
 * @param  string $items    PSI results for an article.
 * @return array  $html     HTML formatted text.
 */
function netrics_articles_results( $post_id, $items ) {
    $html      = 'No articles.';
    $item      = 0;
    $g_metrics = array(
        'score' => null,
        'speed' => null,
        'tti' => null,
        'size' => null,
        'dom' => null,
        'requests' => null,
    );

    if ( $items && 1 < count( $items ) ) {
        $html = '<ol>';

        foreach ( $items as $article ) {
            $psi_url = 'https://developers.google.com/speed/pagespeed/insights/?url='. urlencode( $article['url'] );
            $html .= '<li><a href="' . esc_url( $article['url'] ) . '">' . esc_html( $article['title'] ) . '</a>';

            if ( isset( $article['pagespeed']['error'] ) ) {
                $pgspeed = $article['pagespeed'];
                $html .=  '<br><small>';

                if ( ! $pgspeed['error'] ) {
                    foreach ($g_metrics as $key => $value) {
                        $g_metrics[$key][$item] = $pgspeed[$key];
                    }
                    $item++;

                    $html .=  'DOM: ' . number_format( $pgspeed['dom'] );
                    $html .=  ' | Requests: ' . number_format( $pgspeed['requests'] );
                    $html .=  ' | Size: ' . size_format( $pgspeed['size'], 1 );
                    $html .=  ' | Speed/TTI(s): ' . round( $pgspeed['speed'] / 1000, 1 ) . '/' . round( $pgspeed['tti']  / 1000, 1 );
                    $html .=  ' | <a href="' . esc_url( $psi_url ) . '">Score</a>: ' . $pgspeed['score'] * 100;

                } else {
                    $html .= 'Error: ' . $pgspeed['error'];
                }
                $html .=  '</small>';

            }
            $html .=  "</li>";

        }
        $html .= '</ol>';

    }  elseif ( has_term( 6176, 'flag', $post_id ) ) {

        $html .= "  / No articles <small>(Fetch RSS failed)</small></li>";

    } elseif ( has_term( 6172, 'flag', $post_id ) ) {

        $html .= "  / No articles (JSON feed; will fetch soon)</li>";

    } elseif ( has_term( 6175, 'flag', $post_id ) ) {

        $html .= "  / No articles (Site has no RSS feed)</li>";

    } else {

        $html .= "  / No articles <small>(Check RSS feed)</small></li>";

    }
    $html .= '</ol>';

    return $html;
}



/**
 * Get HTML table of PSI results for a Publication.
 *
 * @param  int    $post_id  ID of a post.
 * @param  string $items    PSI results for an article.
 * @return string $html     HTML table rows.
 */
function netrics_articles_results_table( $post_id, $items ) {
    $html      = '';
    $item      = 0;
    $g_metrics = array(
        'score' => null,
        'speed' => null,
        'tti' => null,
        'size' => null,
        'dom' => null,
        'requests' => null,
    );

    if ( $items && 1 < count( $items ) ) {

        foreach ( $items as $article ) {
            $html .= '<tr><th colspan="7" style="text-align: left; font-size: 0.9rem; padding-top: 0.5rem; border: none;">';
            $html .= "<a href=\"{$article['url']}\">{$article['title']}</a></th></tr>";
            if ( isset( $article['pagespeed']['error'] ) ) {

                $pgspeed = $article['pagespeed'];
                if ( ! $pgspeed['error'] ) {

                    foreach ($g_metrics as $key => $value) {
                        $g_metrics[$key][$item] = $pgspeed[$key];
                    }
                    $item++;

                    $html .=  '<tr><td></td><td>' . number_format( $pgspeed['dom'] ) . '</td>';
                    $html .=  '<td>' . number_format( $pgspeed['requests'] ) . '</td>';
                    $html .=  '<td>' . size_format( $pgspeed['size'], 1 ) . '</td>';
                    $html .=  '<td>' . round( $pgspeed['speed'] / 1000, 1 ) . '</td>';
                    $html .=  '<td>' . round( $pgspeed['tti']  / 1000, 1 ) . '</td>';
                    $html .=  '<td>' . $pgspeed['score'] * 100 . '</td></tr>';

                } else {
                    $html .= '<tr><td colspan=\"7\">No PageSpeed results.</td></tr>';
                }

            }

        }
    }

    return $html;
}


/**
 * Get array of PSI metrics slugs.
 *
 *
 * @since   0.1.0
 *
 * @return array $metrics Array of slugs.
 */
function netrics_get_pagespeed_metrics() {
    $metrics = array( 'dom', 'requests', 'size', 'speed', 'tti', 'score' );

    return $metrics;
}

/**
 * Get data about Publications.
 *
 * @since   0.1.0
 *
 * @param  query  $post_id  WP Query object.
 * @param  bool   $$circ    Include circulation in returned data.
 * @param  bool   $rank     Include site rank in returned data.
 * @return array $pubs_data Array of data for all CPT posts.
 */
function netrics_get_pubs_query_data( $query = array(), $circ = 1, $rank = 1 ) {
    if ( ! isset( $query->posts ) ) {
        $query = newsstats_get_pub_posts( 2000 );
    }

    $pubs_data = array();
    $pub_data  = array();
    $metrics   = netrics_get_pagespeed_metrics();

    foreach ( $query->posts as $post ) {
        $post_id   = $post->ID;
        $pub_data = netrics_pub_pagespeed( $post_id ); // PSI averages.

        if ( $circ ) {
            $pubs_data['circ'][] = get_post_meta( $post_id, 'nn_circ', true );
        }

        if ( $rank ) {
            $pubs_data['rank'][] = get_post_meta( $post_id, 'nn_rank', true );
        }

        foreach ($metrics as $metric ) {
            if ( isset( $pub_data[ $metric ] ) ) {
                $pubs_data[ $metric ][] = floatval( $pub_data[ $metric ] );
            }
        }

        if ( isset( $pub_data['results'] ) ) {
            $pubs_data['results'][] = $pub_data['results'];
        }
    }

    return $pubs_data;
}

/**
 * Get PageSpeed averages for all articles of a Publication with results.
 *
 * @param  int    $post_id   ID of a post.
 * @return array  $pub_ps    Array of PageSpeed averages.
 */
function netrics_pub_pagespeed( $post_id ) {
    $pub_psi_all   = get_post_meta( $post_id, 'nn_psi_avgs', true );
    $pub_psi_month = end( $pub_psi_all );

    return $pub_psi_month;
}

/*******************************
 =Filter Post Results (by form selections)
 ******************************/
/* Add query var for form -- used in hidden input field (archive-trail.php) */
function netrics_add_query_vars( $vars ) {
    $vars[] = "action"; // name of the var as seen in the URL.

    return $vars;
}
add_filter('query_vars', 'netrics_add_query_vars');

/* List post results based on user's form selections (hidden input field value: 'find'). */
function netrics_pre_get_posts( $query ) {
    if ( isset( $query->query_vars['action'] ) && $query->query_vars['action'] == 'find' ) {

        if ( $query->is_main_query() && ! is_admin() && is_post_type_archive( 'publication' ) ) {

            $tax_query = array();

            $tax_input = $_POST['tax_input']; // Needs Nonce.
            if( ! empty( $tax_input ) ) {
                foreach ( $tax_input as $key => $value ) {
                    if ( ! empty( $value ) && $value[0] != "0" ) {
                        $value = array_map( 'intval', $value );
                        $tax_query[] = array(
                            'taxonomy' => $key,
                            'field'    => 'id',
                            'terms'    => $value,
                            'operator' => 'IN'
                        );
                    }
                }
            $query->set( 'tax_query', $tax_query );
            }
        }
    }
}
add_action( 'pre_get_posts', 'netrics_pre_get_posts' );
