<?php
/**
 * General functions to read/write plugin settings and calculate stats.
 *
 * @link    http://hearingvoices.com/tools/
 * @since   0.1.1
 *
 * @package    News Netrics
 * @subpackage news-netrics/includes
 */

/* ------------------------------------------------------------------------ *
 * Functions to get/set options array.
 * ------------------------------------------------------------------------ */
/**
 * Retrieves an option, and array of plugin settings, from database.
 *
 * Option functions based on Jetpack Stats:
 * @link https://github.com/Automattic/jetpack/blob/master/modules/stats.php
 *
 * @since   0.1.0
 *
 * @uses    netrics_upgrade_options()
 * @return  array   $options    Array of plugin settings
 */
function netrics_get_options() {
    $options = get_option( 'newsnetrics' );

    // Set version if not the latest.
    if ( ! isset( $options['version'] ) || $options['version'] < NEWSNETRICS_VERSION ) {
        $options = netrics_upgrade_options( $options );
    }

    return $options;
}

/**
 * Makes array of plugin settings, merging default and new values.
 *
 * @since   0.1.0
 *
 * @uses    netrics_set_options()
 * @param   array   $options        Array of plugin settings
 * @return  array   $new_options    Merged array of plugin settings
 */
function netrics_upgrade_options( $options ) {
    $defaults = array(
        'awis'           => '',
        'awissecret'     => '',
        'gmaps'          => '',
        'psi'            => '',
        'veracity'       => '',
        'veracitysecret' => '',
    );

    if ( is_array( $options ) && ! empty( $options ) ) {
        $new_options = array_merge( $defaults, $options );
    } else {
        $new_options = $defaults;
    }

    $new_options['version'] = NEWSNETRICS_VERSION;

    netrics_set_options( $new_options );

    return $new_options;
}

/*
Array
(
    [awis] => {KEY}
    [awissecret] => {KEY}
    [gmaps] => {KEY}
    [psi] => {KEY}
    [veracity] => {KEY}
    [version] => {vers#}
)
*/

/**
 * Sets an option in database (an array of plugin settings).
 *
 * Note: update_option() adds option if it doesn't exist.
 *
 * @since   0.1.0
 *
 * @param   array   $option     Array of plugin settings
 */
function netrics_set_options( $options ) {
    $options_clean = netrics_sanitize_data( $options );
    update_option( 'newsnetrics', $options_clean );
}

/* ------------------------------------------------------------------------ *
 * Functions to get/set a specific options array item.
 * ------------------------------------------------------------------------ */
/**
 * Retrieves a specific setting (an array item) from an option (an array).
 *
 * @since   0.1.0
 *
 * @uses    netrics_get_options()
 * @param   array|string    $option     Array item key
 * @return  array           $option_key Array item value
 */
function netrics_get_option( $option_key = NULL ) {
    $options = netrics_get_options();

    // Returns valid inner array key ($options[$option_key]).
    if ( isset( $options ) && $option_key != NULL && isset( $options[ $option_key ] ) ) {
            return $options[ $option_key ];
    } else { // Inner array key not valid.
    return NULL;
    }
}

/**
 * Sets a specified setting (array item) in the option (array of plugin settings).
 *
 * @since   0.1.0
 *
 * @uses    netrics_set_options()
 *
 * @param   string  $option     Array item key of specified setting
 * @param   string  $value      Array item value of specified setting
 * @return  array   $options    Array of plugin settings
 */
function netrics_set_option( $option, $value ) {
    $options = netrics_get_options();

    $options[$option] = $value;

    netrics_set_options( $options );
}

/**
 * Sanitizes values in an one- and multi- dimensional arrays.
 *
 * Used by post meta-box form before writing post-meta to database
 * and by Settings API before writing option to database.
 *
 * @link https://tommcfarlin.com/input-sanitization-with-the-wordpress-settings-api/
 *
 * @since    0.4.0
 *
 * @param    array    $input        The address input.
 * @return   array    $input_clean  The sanitized input.
 */
function netrics_sanitize_data( $data = array() ) {
    // Initialize a new array to hold the sanitized values.
    $data_clean = array();

    // Check for non-empty array.
    if ( ! is_array( $data ) || ! count( $data )) {
        return array();
    }

    // Traverse the array and sanitize each value.
    foreach ( $data as $key => $value) {
        // For one-dimensional array.
        if ( ! is_array( $value ) && ! is_object( $value ) ) {
            // Remove blank lines and whitespaces.
            $value = preg_replace( '/^\h*\v+/m', '', trim( $value ) );
            $value = str_replace( ' ', '', $value );
            $data_clean[ $key ] = sanitize_text_field( $value );
        }

        // For multidimensional array.
        if ( is_array( $value ) ) {
            $data_clean[ $key ] = netrics_sanitize_data( $value );
        }
    }

    return $data_clean;
}

/**
 * Sanitizes values in an one-dimensional array.
 * (Used by post meta-box form before writing post-meta to database.)
 *
 * @link https://tommcfarlin.com/input-sanitization-with-the-wordpress-settings-api/
 *
 * @since    0.4.0
 *
 * @param    array    $input        The address input.
 * @return   array    $input_clean  The sanitized input.
 */
function netrics_sanitize_array( $input ) {
    // Initialize a new array to hold the sanitized values.
    $input_clean = array();

    // Traverse the array and sanitize each value.
    foreach ( $input as $key => $val ) {
        $input_clean[ $key ] = sanitize_text_field( $val );
    }

    return $input_clean;
}

function netrics_remove_empty_lines( $string ) {
    return preg_replace( "/(^[\r\n]*|[\r\n]+)[\s\t]*[\r\n]+/", "\n", $string );
    // preg_replace( '/^\h*\v+/m', '', $string );
}

function netrics_object_to_array($d) {
        if (is_object($d))
            $d = get_object_vars($d);

        return is_array($d) ? array_map(__FUNCTION__, $d) : $d;
}


/* ------------------------------------------------------------------------ *
 * Functions to check values.
 * ------------------------------------------------------------------------ */
/**
 * Checks if URL exists. (Not used yet.)
 * @todo Add status code as tax-meta upon settings wp_insert_term.
 *
 * @since   0.1.0
 *
 * @param  $url         URL to be checked.
 * @return int|string   URL Sstatus repsonse code number, or WP error on failure.
 */
function netrics_url_exists( $url = '' ) {
    // Make absolute URLs for WP core scripts (from their registered relative 'src' URLs)
    if ( substr( $url, 0, 13 ) === '/wp-includes/' || substr( $url, 0, 10 ) === '/wp-admin/' ) {
        $url = get_bloginfo( 'wpurl' ) . $url;
    }

    // Make protocol-relative URLs absolute  (i.e., from "//example.com" to "https://example.com" )
    if ( substr( $url, 0, 2 ) === '//' ) {
        $url = 'https:' . $url;
    }

    if ( has_filter( 'netrics_url_exists' ) ) {
        $url = apply_filters( 'netrics_url_exists', $url );
    }

    // Sanitize
    $url = esc_url_raw( $url );

    // Get URL header
    $response = wp_remote_head( $url );
    if ( is_wp_error( $response ) ) {
        return 'Error: ' . is_wp_error( $response );
    }

    // Request success, return header response code
    return wp_remote_retrieve_response_code( $response );
}

/**
 * Checks if array item index exists and holds a non-empty value.
 *
 * @since   0.1.0
 *
 * @param  $value   Array item key (e.g., $arr['key'][0]).
 * @return $value   Array item key's value, or false on failure.
 */
function newsstats_check_val( $value ) {
    if ( isset( $value ) && 0 < strlen( trim( $value ) ) ) {
        return $value;
    } else {
        return false;
    }
}

/* Test action */
function echo_yo() {
    echo '<h2>yo yo yo yo</h2>';
}
add_action( 'test_action', 'echo_yo' );


/* Check value */
function yono( $val ) {
    $yono = ( $val ) ? 'yo' : 'no';
    return $yono;
}


function netrics_get_csv_data( $csv ) {
    // home/wp_wugkzz/news.pubmedia.us/wp-content/plugins/news-netrics/import/us-census-2018-county-wp.csv
    echo $exists = ( file_exists( $csv ) ) ? $csv . "\n" : "N'existe pas.\n";
    $csv_array = array();
    if ( ( $handle = fopen( $csv, 'r' ) ) !== FALSE ) {
        $csv_array = array_map( 'str_getcsv', file( $csv ) );
        fclose($handle);
    } else {
        echo 'Did not open.';
    }
    return $csv_array;
}

/*******************************
 =STATISTICS
 ******************************/
/* ------------------------------------------------------------------------ *
 * Display array evaluations
 * ------------------------------------------------------------------------ */

/**
 * Outputs HTML with array averages, quartiles, and standard deviations.
 *
 */
function nstats_array_eval( $array ) {
    ?>
<table class="tabular" style="">
    <tbody>
        <tr>
            <th scope="row"><?php esc_attr_e( 'Mean&nbsp;', 'statsclass' ); ?></th>
            <td><?php echo number_format( nstats_mean( $array ), 2, '.', ',' ); ?></td>
        </tr>
        <tr>
            <th scope="row"><?php esc_attr_e( 'Maximum&nbsp;', 'statsclass' ); ?></th>
            <td><?php echo number_format( nstats_max( $array ), 2, '.', ',' ); ?></td>
        </tr>
        <tr>
            <th scope="row"><?php esc_attr_e( 'Minimum&nbsp;', 'statsclass' ); ?></th>
            <td><?php echo number_format( nstats_min( $array ), 2, '.', ',' ); ?></td>
        </tr>
        <tr>
            <th scope="row"><?php esc_attr_e( 'Range&nbsp;', 'statsclass' ); ?></th>
            <td><?php echo number_format( nstats_range( $array ), 2, '.', ',' ); ?></td>
        </tr>
        <tr>
            <th scope="col"><?php esc_attr_e( 'Quartile 1&nbsp;', 'statsclass' ); ?></th>
            <td><?php echo number_format( nstats_q1( $array ), 2, '.', ',' ); ?></td>
        </tr>
        <tr>
            <th scope="col"><?php esc_attr_e( 'Q2/Median&nbsp;', 'statsclass' ); ?></th>
            <td><?php echo number_format( nstats_q2( $array ), 2, '.', ',' ); ?></td>
        </tr>
        <tr>
            <th scope="col"><?php esc_attr_e( 'Quartile 3&nbsp;', 'statsclass' ); ?></th>
            <td><?php echo number_format( nstats_q3( $array ), 2, '.', ',' ); ?></td>
        </tr>
        <tr>
            <th scope="col"><?php esc_attr_e( 'Interquartile Range&nbsp;', 'statsclass' ); ?></th>
            <td><?php echo number_format( nstats_iqr( $array ), 2, '.', ',' ); ?></td>
        </tr>
        <tr>
            <th scope="col"><?php esc_attr_e( 'Standard Deviation&nbsp;', 'statsclass' ); ?></th>
            <td><?php echo number_format( nstats_sd( $array ), 2, '.', ',' ); ?></td>
        </tr>
        <tr>
            <th scope="col"><?php esc_attr_e( 'SD Population&nbsp;', 'statsclass' ); ?></th>
             <td><?php echo number_format( nstats_sd_pop( $array ), 2, '.', ',' ); ?></td>
        </tr>
    </tbody>
    <tfoot>
        <tr>
            <th scope="row"><?php esc_attr_e( 'Count&nbsp;', 'statsclass' ); ?></th>
            <td><?php echo number_format( count( $array ) ); ?></td>
        </tr>
    </tfoot>
</table>

   <?php
}


/**
 * Use filemod timestamp for version number.
 *
 * For setting cache-busting version number in script registrations.
 * wp_register_script( 'handle', $file_url, array(), netrics_filemod_vers( $file_path ) );
 *
 * @param  string $path Path of script/style file
 * @return string $vers File-modification timestamp or WordPress version
 */
function netrics_filemod_vers( $path ) {
    $vers = '';

    if ( file_exists( $path ) ) {
        $vers = filemtime( $path );
    } else {
        $vers = get_bloginfo('version');
    }

    return $vers;
}


/* Multiple select drop-down of taxonomy terms */
// http://wordpress.stackexchange.com/questions/107044/error-sending-array-of-inputs
function get_terms_multi_select( $tax, $args = array(), $rows = 10 ) {
    $output = ''; // Set (or clear) var.
    $tax_arr = get_taxonomy( $tax );
    $tax_single = ( $tax == 'region' )
        ? 'State' : $tax_arr->labels->singular_name;

    $terms = get_terms( $tax, $args ); // Array of tax terms.

    if ( $terms ) {
        $count = count( $terms ) + 1; // Terms in tax (for select size).
        $output .= "<h3 class=\"pub-tax-name\">{$tax_single}</h3>\n";
        $selected_any = ( ! isset( $_POST['tax_input'][$tax] ) || ( $_POST['tax_input'][$tax][0] == '0' ) ) ? ' selected' : ''; // To select option for previous user-choice.
        $output .= "<select multiple name=\"tax_input[{$tax}][]\" id=\"{$tax}\" size=\"$rows\">\n";
        $output .= "\t<option value=\"0\"{$selected_any}> Any {$tax_single}</option>\n";

        foreach ( $terms as $term ) {
            $term_slug = $term->slug;
            $selected = ''; // To select option for previous user-choice.

            if ( isset( $_POST['tax_input'][$tax] ) && in_array( $term->term_id, $_POST['tax_input'][$tax] ) ) {
                $selected = ' selected';
            }

            $termcount = ( $tax == 'region' ) ? '' : " ({$term->count})";

            $output .= "\t<option value=\"{$term->term_id}\"{$selected}> {$term->name}{$termcount}</option>\n";
        }
        $output .= "</select>\n";

        return $output;
    }
}

/* ------------------------------------------------------------------------ *
 * Basic Calculations
 * ------------------------------------------------------------------------ */

/**
 * Calculates percentage check between to numbers.
 *
 *
 * @since    0.1.0
 * @param float $num1 Number
 */
function nstats_percent_change( $num1, $num2 ) {
    $percent_change = ( ( $num2 - $num1 ) / $num1 ) * 100;

    return $percent_change;
}

/* Average numbers in an array.
 *
 * @param
 * @return
 */
function nstats_average( $array ) {
    return array_sum( $array ) / count( $array );
}

/* ------------------------------------------------------------------------ *
 * Averages: Mean, Mode, Median
 * ------------------------------------------------------------------------ */

/**
 * Calculates the mean (average) as a set of numbers.
 *
 *
 * @since    0.1.0
 * @param array $array Array of numbers.
 */
function nstats_mean( $array ) {
    // check_array( $array );
    $mean = array_sum( $array ) / count( $array );
  return $mean;
}

/**
 * Finds the mode number of an array.
 *
 *
 * @param array $array Set of numerical values.
 * @return float|bool The mode or false on error.
 */
function nstats_mode( $array ) {
    // check_array( $array );
    $values = array_count_values( $array );
    $mode   = array_search( max( $values ), $values );

    return $mode;
}

function nstats_median( $array ) {
  return nstats_q2( $array );
}

/* ------------------------------------------------------------------------ *
 * Quantiles: Quartiles, Percentile
 * ------------------------------------------------------------------------ */

/**
 * Calculate Quartiles
 *
 * @link http://blog.poettner.de/2011/06/09/simple-statistics-with-php/
 *
 * @param array $x
 * @return float|bool The correlation or false on error.
 */

function nstats_q1( $array ) {
  return nstats_percentile( $array, 25);
}

function nstats_q2( $array ) {
  return nstats_percentile( $array, 50);
}

function nstats_q3( $array ) {
  return nstats_percentile( $array, 75);
}

// interquartile range (IQR) is the difference between the upper and lower quartiles. (IQR = Q3 - Q1)
function nstats_iqr( $array ) {
    $iqr = nstats_q3( $array ) - nstats_q1( $array );
    return $iqr;
}

function nstats_percentile( $array, $percentile ) {
    sort( $array );
    $index = ( $percentile / 100 ) * count( $array );
    if ( floor( $index ) == $index ) {
         $result = ( $array[ $index-1 ] + $array[ $index] ) / 2;
    }
    else {
        $result = $array[ floor($index) ];
    }
    return $result;
}

/* ------------------------------------------------------------------------ *
 * Range: Minimum, Maximum, Total
 * ------------------------------------------------------------------------ */

/**
 * Finds the highest value in an array.
 *
 * @param array $array Set of numerical values.
 * @return float|bool The mode or false on error.
 */
function nstats_max( $array ) {
    rsort( $array );
    $maximum = $array[0];

    return $maximum;
}

/**
 * Finds the lowest value in an array.
 *
 * @param array $array Set of numerical values.
 * @return float|bool The mode or false on error.
 */
function nstats_min( $array ) {
    sort( $array );
    $minimum = $array[0];

    return $minimum;
}

/**
 * Finds the range of values in an array.
 *
 * @param array $array Set of numerical values.
 * @return float|bool The mode or false on error.
 */
function nstats_range( $array ) {
    $maximum = nstats_max( $array );
    $minimum = nstats_min( $array );

    $range = $maximum - $minimum;

    return $range;
}

/* ------------------------------------------------------------------------ *
 * Variance: Sample Standard Deviation, Population Standard Deviations
 * ------------------------------------------------------------------------ */

/**
 * Finds the sample standard deviation of values in an array.
 *
 * @param array $array Set of numerical values.
 * @return float|bool The mode or false on error.
 */
function nstats_sd( $array ) {
    if( count( $array ) < 2 ) {
        return;
    }

    $mean = nstats_mean( $array );

  $sum = 0;
  foreach ( $array as $value) {
    $sum += pow( $value - $mean, 2); // Exponential expression.
  }

  $result = sqrt( (1 / ( count( $array ) - 1 ) ) * $sum );

  return $result;
}

/**
 * Finds the standard deviation (population) of a set of numbers.
 *
 * The average of the squared differences from the mean.
 * (SD, also represented by the Greek letter sigma σ or s)
 *
 */
function nstats_sd_pop( $array ) {
    $mean  = nstats_mean( $array );
    $count = count( $array );
    $sum   = 0;

    foreach( $array as $value) {
        $diff    = $value - $mean;
        $diff_sq = $diff * $diff;
        $sum += $diff_sq;
    }

    $variance = $sum / $count;
    $std_dev  = sqrt( $variance );

    return $std_dev;
}


/* ------------------------------------------------------------------------ *
 * Comparision of two arrays.
 * ------------------------------------------------------------------------ */
/**
 *
 * @link http://php.net/manual/en/function.stats-stat-correlation.php
 *
 * @param array $x
 * @param array $y
 * @return float|bool The correlation or false on error.
 */

function nstats_correlation( $array1, $array2 ) {

    $length = count( $array1 );
    $mean1  = array_sum( $array1 ) / $length;
    $mean2  = array_sum( $array2 ) / $length;

    $a1  = 0;
    $b1  = 0;
    $axb = 0;
    $a2  = 0;
    $b2  = 0;

    for ( $i=0; $i < $length; $i++ ) {
        // if ( ( isset( $array1[ $i ] )&& $array1[ $i ] ) && ( isset( $array2[ $i ] ) && $array2[ $i ] ) ) {
            $a1  = $array1[ $i ] - $mean1;
            $b1  = $array2[ $i ] - $mean2;
            $axb = $axb + ( $a1 * $b1 );
            $a2  = $a2 + pow( $a1, 2 );
            $b2  = $b2 + pow( $b1, 2 );
        // }
    }


    $correlation = $axb / sqrt( $a2 * $b2 );

    return $correlation;
}

