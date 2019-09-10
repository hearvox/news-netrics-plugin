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
 * Get PageSpeed averages for all articles of a Publication with results.
 *
 * @param  int   $post_id  ID of a post.
 * @return array $pub_ps   Array of PageSpeed averages.
 */
function netrics_pagespeed_avgs( $post_id, $date = '2019-08' ) {
    $data    = get_post_meta( $post_id, 'nn_articles', true);
    $items   = $data[ $date ];
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
 * Get PageSpeed averages for all articles of a Publication with results.
 *
 * @param  int   $post_id  ID of a post.
 * @return array $pub_ps   Array of PageSpeed averages.
 */
function netrics_site_pagespeed( $post_id, $meta_key = 'nn_articles_201908' ) {
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

        $list .= ( isset( $item['url'] ) ) ? "<li><a href=\"{$item['url']}\">{$item['title']}</a>" : '<li>';
        if ( isset( $item['pagespeed']['error'] ) ) {

            $pgspeed = $item['pagespeed'];
            $list .=  '<br><small>';

            if ( ! $pgspeed['error'] ) {

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
 * $data = netrics_site_pagespeed( $post_id );
 * foreach ( $data as $k => $v ) {
 *     echo $k . ': ' . netrics_pagespeed_format( $k, $v ). "\n";
 * }
 *
 *
 */
function netrics_pagespeed_format( $metric, $num, $size = 0 ) {
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
            if ( $size ) {
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
 * Get
 *
 * @param  int   $post_id  ID of a post.
 * @return array $pub_bw   Array of BuiltWith tech counts.
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
 * Get
 *
 * @param  int   $post_id  ID of a post.
 * @return array $pub_bw   Array of BuiltWith tech counts.
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
 * Get
 *
 *
 * @since   0.1.0
 *
 * @return array $pub_data Array of data for all CPT posts.
 */
function netrics_get_pagespeed_metrics() {
    $metrics = array( 'dom', 'requests', 'size', 'speed', 'tti', 'score' );

    return $metrics;
}

/**
 * Get
 *
 *
 * @since   0.1.0
 *
 * @return array $pub_data Array of data for all CPT posts.
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
        $pub_data = netrics_site_pagespeed( $post_id ); // PSI averages.

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

/* ------------------------------------------------------------------------ *
 * Alexa Web Information Service (Amazon) API
 * ------------------------------------------------------------------------ */
/**
 * Get the AWIS data for publication, stored in post meta.
 *
 * @param  int   $post_id  ID of a post.
 * @return array $awis     Array of AWIS data.
 */
function netrics_get_site_awis_data( $post_id ) {
    $awis          = array();
    $nn_site       = get_post_meta( $post_id , 'nn_site' );
    $awis['desc']  = ( isset( $nn_site[0]['alexa']['desc']  ) && $nn_site[0]['alexa']['desc'] )
        ? '&mdash; ' . $nn_site[0]['alexa']['desc'] : '';
    $awis['rank']  = ( isset( $nn_site[0]['alexa']['rank'] ) && $nn_site[0]['alexa']['rank'] )
        ? number_format( floatval($nn_site[0]['alexa']['rank'] ) ) : '--';
    $awis['since'] = ( isset( $nn_site[0]['alexa']['since']  ) && $nn_site[0]['alexa']['since'] )
        ? date_parse_from_format( 'd-M-Y', $nn_site[0]['alexa']['since'] ) : false;
    $awis['year']  = ( $awis_since )
        ? absint( $awis_since['year'] ) : '--';
    $awis['links'] = ( isset( $nn_site[0]['alexa']['links']  ) && $nn_site[0]['alexa']['links'] )
        ? number_format( (int) $nn_site[0]['alexa']['links'] ) : '--';

    return $awis;
}

/**
 * Get Alexa data for site (global rank, and Year online.)
 *
 * @param  int   $post_id  ID of a post.
 * @return array $pub_ps   Array of PageSpeed averages.
 */
function netrics_site_alexa( $post_id ) {
    // Get site data (inclding Alexa and BuiltWith).
    $nn_site  = get_post_meta( $post_id, 'nn_site', true);
    $site_awis = array();
    // Get Alexa data.
    $site_awis['rank']  = ( isset ( $nn_site['alexa']['rank'] ) )
        ?  $nn_site['alexa']['rank'] : null;
    $site_awis['desc']  = ( isset ( $nn_site['alexa']['desc'] ) )
        ?  $nn_site['alexa']['desc'] : null;
    $since = ( isset ( $nn_site['alexa']['since'] ) && $nn_site['alexa']['since'] )
        ? date_parse_from_format( 'd-M-Y', $nn_site['alexa']['since'] ) : false;
    $site_awis['year']  = ( $since ) ? absint( $since['year'] ) : null;

    return $site_awis;

}

/**
 * Get BuiltWith counts of web technologies used at site.
 *
 * @param  int   $post_id  ID of a post.
 * @return array $pub_bw   Array of BuiltWith tech counts.
 */
function netrics_site_bulltwith( $post_id ) {
        // Get site data (inclding Alexa and BuiltWith).
        $nn_site  = get_post_meta( $post_id, 'nn_site', true);
        $site_bw  = array ();

        // BuiltWith data, remove item from sum.
        if ( isset( $nn_site['builtwith'] ) ) {
            unset( $nn_site['builtwith']['date'] );
            unset( $nn_site['builtwith']['error'] );

        }

        // Sum and category counts.
        $site_bw['techs']   = ( isset ( $nn_site['builtwith'] ) )
            ? array_sum( $nn_site['builtwith'] ) : '';
        $site_bw['ad']      = ( isset ( $nn_site['builtwith']['ads'] ) )
            ? $nn_site['builtwith']['ads'] : '';
        $site_bw['tracks']  = ( isset ( $nn_site['builtwith']['analytics'] ) )
            ? $nn_site['builtwith']['analytics'] : '';
        $site_bw['scripts'] = ( isset ( $nn_site['builtwith']['javascript'] ) )
            ? $nn_site['builtwith']['javascript'] : '';

        return $site_bw;

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
