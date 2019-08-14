<?php
/**
 * General functions for reading and writing plugin settings.
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

