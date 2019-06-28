<?php
/*
 * Plugin Name: News Netrics
 * Plugin URI: https://news.pubmedia.us/
 * Description: Net Netrics customizations and post types.
 * Author:  Barrett Golding
 * Version: 0.1.0
 * Author URI: https://hearingvoices.com/
 * License: GPL2+
 * Text Domain: newsnetrics
 * Domain Path: /languages/
 * Plugin Prefix: newsnetrics
*/

/*
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>
 */

/* ------------------------------------------------------------------------ *
 * Plugin init and uninstall text change
 * ------------------------------------------------------------------------ */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
    die;
}

if ( defined( 'NEWSNETRICS_VERSION' ) ) {
    return;
}

/* ------------------------------------------------------------------------ *
 * Constants: plugin version, name, and the path and URL to directory.
 *
 * NEWSNETRICS_BASENAME news-netrics-master/news-netrics.php
 * NEWSNETRICS_DIR      /path/to/wp-content/plugins/news-netrics-master/
 * NEWSNETRICS_URL      https://example.com/wp-content/plugins/news-netrics-master/
 * ------------------------------------------------------------------------ */
define( 'NEWSNETRICS_VERSION', '0.1.0' );
define( 'NEWSNETRICS_BASENAME', plugin_basename( __FILE__ ) );
define( 'NEWSNETRICS_DIR', trailingslashit( plugin_dir_path( __FILE__ ) ) );
define( 'NEWSNETRICS_URL', trailingslashit( plugin_dir_url( __FILE__ ) ) );

/**
 * Adds "Settings" link on plugin page (next to "Activate").
 */
//
function newsnetrics_plugin_settings_link( $links ) {
  $settings_link = '<a href="' . esc_url( admin_url( 'options-general.php?page=newsnetrics' ) ) . '">' . __( 'Settings', 'newsnetrics' ) . '</a>';
  array_unshift( $links, $settings_link );
  return $links;
}
add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), 'NEWSNETRICS_plugin_settings_link' );

/**
 * Redirect to Settings screen upon plugin activation.
 *
 * @param  string $plugin Plugin basename (e.g., "my-plugin/my-plugin.php")
 * @return void
 */
function newsnetrics_activation_redirect( $plugin ) {
    if ( $plugin === NEWSNETRICS_BASENAME ) {
        $redirect_uri = add_query_arg(
            array(
                'page' => 'newsnetrics' ),
                admin_url( 'options-general.php' )
            );
        wp_safe_redirect( $redirect_uri );
        exit;
    }
}
add_action( 'activated_plugin', 'NEWSNETRICS_activation_redirect' );

/**
 * Load the plugin text domain for translation.
 *
 * @since   0.1.0
 */
function newsnetrics_load_textdomain() {
    load_plugin_textdomain( 'newsnetrics', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
}
add_action( 'plugins_loaded', 'newsnetrics_load_textdomain' );

/**
 * Sets default settings option upon activation, if options doesn't exist.
 *
 * @uses NEWSNETRICS_get_options()   Safely get site option, check plugin version.
 */
function newsnetrics_activate() {
    newsnetrics_get_options();
}
register_activation_hook( __FILE__, 'newsnetrics_activate' );

/**
 * The code that runs during plugin deactivation (not currently used).
 */
/*
function NEWSNETRICS_deactivate() {
}
register_deactivation_hook( __FILE__, 'NEWSNETRICS_deactivate' );
*/

/* ------------------------------------------------------------------------ *
 * Required Plugin Files
 * ------------------------------------------------------------------------ */
include_once( dirname( __FILE__ ) . '/includes/admin-options.php' );
include_once( dirname( __FILE__ ) . '/includes/apis.php' );
include_once( dirname( __FILE__ ) . '/includes/feeds.php' );
include_once( dirname( __FILE__ ) . '/includes/functions.php' );
include_once( dirname( __FILE__ ) . '/includes/results.php' );
include_once( dirname( __FILE__ ) . '/includes/transients.php' );

/*******************************
 =CUSTOM POST TYPES
 ******************************/
/**
 * Register custom post types.
 *
 * @see get_post_type_labels() for label keys.
 */
function newsstats_custom_post_types() {
    $labels = array(
        'name'               => _x( 'Publications', 'newsnetrics' ),
        'singular_name'      => _x( 'Publication', 'newsnetrics' ),
        'add_new'            => __( 'Add Publication', 'newsnetrics' ),
        'add_new_item'       => __( 'Add New Publication', 'newsnetrics' ),
        'new_item'           => __( 'New Publication', 'newsnetrics' ),
        'edit_item'          => __( 'Edit Publication', 'newsnetrics' ),
        'view_item'          => __( 'View Publication', 'newsnetrics' ),
        'view_items'         => __( 'View Publication', 'newsnetrics' ),
        'search_items'       => __( 'Search Publication', 'newsnetrics' ),
        'not_found'          => __( 'No Publication found.', 'newsnetrics' ),
        'not_found_in_trash' => __( 'No Publication found in Trash.', 'newsnetrics' ),
        'parent_item_colon'  => __( 'Parent Publication"', 'newsnetrics' ),
        'all_items'          => __( 'All Publications', 'newsnetrics' ),
        'archives'           => __( 'Publication Archives', 'newsnetrics' ),
        'attributes'         => __( 'Publication Attributes', 'newsnetrics' ),
        'filter_items_list'  => _x( 'Filter Publication', 'newsnetrics' ),
    );

    $args = array(
        'label'        => 'Publications',
        'labels'       => $labels,
        'description'  => 'News outlet websites.',
        'public'       => true,
        'hierarchical' => true,
        'show_in_rest' => true,
        'menu_icon'    => 'dashicons-media-document',
        'supports'     => array( 'title', 'editor', 'revisions', 'excerpt', 'thumbnail', 'custom-fields' ),
        'taxonomies'   => array( 'cms', 'region', 'owner', 'server', 'post_tag' ),
        'has_archive'  => true,
    );

    register_post_type( 'publication', $args );
}
add_action( 'init', 'newsstats_custom_post_types' );


/*******************************
 =TAXONOMY
 ******************************/
/**
 * Create two taxonomies, CMS and Server for the post type 'post'.
 *
 * @see register_post_type() for registering custom post types.
 */
function newsstats_taxonomies() {
    // Hierarchical taxonomies.
    $labels = array(
        'name'                       => _x( 'Region', 'taxonomy general name', 'newsnetrics' ),
        'singular_name'              => _x( 'Region', 'taxonomy singular name', 'newsnetrics' ),
        'search_items'               => __( 'Search Regions', 'newsnetrics' ),
        'all_items'                  => __( 'All Regions', 'newsnetrics' ),
        'edit_item'                  => __( 'Edit Region', 'newsnetrics' ),
        'view_item'                  => __( 'View Region', 'newsnetrics' ),
        'update_item'                => __( 'Update Region', 'newsnetrics' ),
        'add_new_item'               => __( 'Add New Region', 'newsnetrics' ),
        'new_item_name'              => __( 'New Region Name', 'newsnetrics' ),
        'not_found'                  => __( 'No Regions found', 'newsnetrics' ),
        'no_terms'                   => __( 'No Regions', 'newsnetrics' ),
        'items_list_navigation'      => __( 'Region list navigation', 'newsnetrics' ),
        'items_list'                 => __( 'Region list', 'newsnetrics' ),
        'most_used'                  => _x( 'Most Used', 'newsnetrics' ),
        'back_to_items'              => __( '&larr; Back to Regions', 'newsnetrics' ),
        // Hierarchical:
        'parent_item'                => __( 'Parent Region', 'newsnetrics' ),
        'parent_item_colon'          => __( 'Parent Region:', 'newsnetrics' ),
    );

    $args = array(
        'labels'            => $labels,
        'description'       => __( 'Geo location', 'newsnetrics' ),
        'public'            => true,
        'hierarchical'      => true,
        'show_in_rest'      => true,
        'show_admin_column' => true,
    );
    register_taxonomy( 'region', array( 'post', 'publication' ), $args );

    unset( $args );
    unset( $labels );

        $labels = array(
        'name'                       => _x( 'Flag', 'taxonomy general name', 'newsnetrics' ),
        'singular_name'              => _x( 'Flag', 'taxonomy singular name', 'newsnetrics' ),
        'search_items'               => __( 'Search Flags', 'newsnetrics' ),
        'all_items'                  => __( 'All Flags', 'newsnetrics' ),
        'edit_item'                  => __( 'Edit Flag', 'newsnetrics' ),
        'view_item'                  => __( 'View Flag', 'newsnetrics' ),
        'update_item'                => __( 'Update Flag', 'newsnetrics' ),
        'add_new_item'               => __( 'Add New Flag', 'newsnetrics' ),
        'new_item_name'              => __( 'New Flag Name', 'newsnetrics' ),
        'not_found'                  => __( 'No Flags found', 'newsnetrics' ),
        'no_terms'                   => __( 'No Flags', 'newsnetrics' ),
        'items_list_navigation'      => __( 'Flag list navigation', 'newsnetrics' ),
        'items_list'                 => __( 'Flag list', 'newsnetrics' ),
        'most_used'                  => _x( 'Most Used', 'newsnetrics' ),
        'back_to_items'              => __( '&larr; Back to Flags', 'newsnetrics' ),
        // Hierarchical:
        'parent_item'                => __( 'Parent Flag', 'newsnetrics' ),
        'parent_item_colon'          => __( 'Parent Flag:', 'newsnetrics' ),
    );

    $args = array(
        'labels'            => $labels,
        'description'       => __( 'Flags for processing publication tests', 'newsnetrics' ),
        'public'            => true,
        'hierarchical'      => true,
        'show_in_rest'      => true,
        'show_admin_column' => true,
    );
    register_taxonomy( 'flag', array( 'post', 'publication' ), $args );

    // Non-hierarchical taxonomies.
    unset( $args );
    unset( $labels );

    $labels = array(
        'name'                       => _x( 'Owner', 'taxonomy general name', 'newsnetrics' ),
        'singular_name'              => _x( 'Owner', 'taxonomy singular name', 'newsnetrics' ),
        'search_items'               => __( 'Search Owners', 'newsnetrics' ),
        'all_items'                  => __( 'All Owners', 'newsnetrics' ),
        'edit_item'                  => __( 'Edit Owner', 'newsnetrics' ),
        'view_item'                  => __( 'View Owner', 'newsnetrics' ),
        'update_item'                => __( 'Update Owner', 'newsnetrics' ),
        'add_new_item'               => __( 'Add New Owner', 'newsnetrics' ),
        'new_item_name'              => __( 'New Owner Name', 'newsnetrics' ),
        'not_found'                  => __( 'No Owners found', 'newsnetrics' ),
        'no_terms'                   => __( 'No Owners', 'newsnetrics' ),
        'items_list_navigation'      => __( 'Owner list navigation', 'newsnetrics' ),
        'items_list'                 => __( 'Owner list', 'newsnetrics' ),
        'most_used'                  => _x( 'Most Used', 'newsnetrics' ),
        'back_to_items'              => __( '&larr; Back to Owners', 'newsnetrics' ),
        // Non-hierarchical:
        'popular_items'              => __( 'Popular Owners', 'newsnetrics' ),
        'separate_items_with_commas' => __( 'Separate Owners with commas', 'newsnetrics' ),
        'add_or_remove_items'        => __( 'Add or remove Owners', 'newsnetrics' ),
        'choose_from_most_used'      => __( 'Choose from the most used Owners', 'newsnetrics' ),
    );

    $args = array(
        'labels'            => $labels,
        'description'       => __( 'Owner of publication', 'newsnetrics' ),
        'public'            => true,
        'hierarchical'      => false,
        'show_in_rest'      => true,
        'show_admin_column' => true,
    );
    register_taxonomy( 'owner', array( 'publication' ), $args );

    unset( $args );
    unset( $labels );

    $labels = array(
        'name'                       => _x( 'CMS', 'taxonomy general name', 'newsnetrics' ),
        'singular_name'              => _x( 'CMS', 'taxonomy singular name', 'newsnetrics' ),
        'search_items'               => __( 'Search CMSs', 'newsnetrics' ),
        'all_items'                  => __( 'All CMSs', 'newsnetrics' ),
        'edit_item'                  => __( 'Edit CMS', 'newsnetrics' ),
        'view_item'                  => __( 'View CMS', 'newsnetrics' ),
        'update_item'                => __( 'Update CMS', 'newsnetrics' ),
        'add_new_item'               => __( 'Add New CMS', 'newsnetrics' ),
        'new_item_name'              => __( 'New CMS Name', 'newsnetrics' ),
        'not_found'                  => __( 'No CMSs found', 'newsnetrics' ),
        'no_terms'                   => __( 'No CMSs', 'newsnetrics' ),
        'items_list_navigation'      => __( 'CMS list navigation', 'newsnetrics' ),
        'items_list'                 => __( 'CMS list', 'newsnetrics' ),
        'most_used'                  => _x( 'Most Used', 'newsnetrics' ),
        'back_to_items'              => __( '&larr; Back to CMSs', 'newsnetrics' ),
        // Non-hierarchical:
        'popular_items'              => __( 'Popular CMSs', 'newsnetrics' ),
        'separate_items_with_commas' => __( 'Separate CMSs with commas', 'newsnetrics' ),
        'add_or_remove_items'        => __( 'Add or remove CMSs', 'newsnetrics' ),
        'choose_from_most_used'      => __( 'Choose from the most used CMSs', 'newsnetrics' ),
    );

    $args = array(
        'labels'            => $labels,
        'description'       => __( 'Content Management System', 'newsnetrics' ),
        'public'            => true,
        'hierarchical'      => false,
        'show_in_rest'      => true,
        'show_admin_column' => true,
    );
    register_taxonomy( 'cms', array( 'post', 'publication' ), $args );

    unset( $args );
    unset( $labels );

    $labels = array(
        'name'                       => _x( 'Server', 'taxonomy general name', 'newsnetrics' ),
        'singular_name'              => _x( 'Server', 'taxonomy singular name', 'newsnetrics' ),
        'search_items'               => __( 'Search Servers', 'newsnetrics' ),
        'all_items'                  => __( 'All Servers', 'newsnetrics' ),
        'popular_items'              => __( 'Popular Servers', 'newsnetrics' ),
        'edit_item'                  => __( 'Edit Server', 'newsnetrics' ),
        'view_item'                  => __( 'View Server', 'newsnetrics' ),
        'update_item'                => __( 'Update Server', 'newsnetrics' ),
        'add_new_item'               => __( 'Add New Server', 'newsnetrics' ),
        'new_item_name'              => __( 'New Server Name', 'newsnetrics' ),
        'separate_items_with_commas' => __( 'Separate Servers with commas', 'newsnetrics' ),
        'add_or_remove_items'        => __( 'Add or remove Servers', 'newsnetrics' ),
        'choose_from_most_used'      => __( 'Choose from the most used Servers', 'newsnetrics' ),
        'not_found'                  => __( 'No Servers found', 'newsnetrics' ),
        'no_terms'                   => __( 'No Servers', 'newsnetrics' ),
        'items_list_navigation'      => __( 'Server list navigation', 'newsnetrics' ),
        'items_list'                 => __( 'Server list', 'newsnetrics' ),
        'most_used'                  => _x( 'Most Used', 'newsnetrics' ),
        'back_to_items'              => __( '&larr; Back to Servers', 'newsnetrics' ),
    );

    $args = array(
        'labels'            => $labels,
        'description'       => __( 'Web server', 'newsnetrics' ),
        'public'            => true,
        'hierarchical'      => false,
        'show_in_rest'      => true,
        'show_admin_column' => true,
    );

    register_taxonomy( 'server', array( 'post', 'publication' ), $args );
}
add_action( 'init', 'newsstats_taxonomies', 0 );

// Term meta for custom taxonomy.
$nn_region_meta = array(
    'nn_region_fips'   => array( 'FIPS', 'string', 'sanitize_text_field', 'region' ),
    'nn_region_pop'    => array( 'Population', 'number', 'absint', 'region' ),
    'nn_region_latlon' => array( 'Latitude|Longitude', 'string', 'sanitize_text_field', 'region' ),
    'nn_region_misc'   => array( 'Timezone|SimpleMapsID', 'string', 'sanitize_text_field', 'region'),
);

/**
 * Register term meta for custom.
 *
 * @return void
 */
function newsstats_reg_term_meta() {
    global $nn_region_meta;
    foreach ( $nn_region_meta as $key => $value ) {
        $args = array(
            'description'       => $value[0],
            'type'              => $value[1], // string|boolean|integer|number
            'single'            => true,
            'sanitize_callback' => $value[2],
            'show_in_rest'      => true,
        );
        register_term_meta( $value[3], sanitize_key( $key ), $args );
    }
}
// add_action( 'init', 'newsstats_reg_term_meta', 0 );

/**
 * Add term-meta fields to {Tax} admin screen.
 *
 * @param string $taxonomy The taxonomy slug.
 * @return void
 */
function newsstats_region_add_form_fields( $tax_meta ) {
    global $nn_region_meta;
    // wp_nonce_field( basename( __FILE__ ), 'newsstats_region_nonce' );
    foreach ( $nn_region_meta as $key => $value ) {
        $el_id = str_replace( '_', '-', $key);
    ?>
    <div class="form-field">
        <label for=<?php echo $el_id; ?>><?php _e( $value[0], 'newsnetrics' ); ?></label>
        <input type="text" name="<?php echo $key ?>" id="<?php echo $el_id ?>" value="" />
    </div>
    <?php
    }
}
add_action( 'region_add_form_fields', 'newsstats_region_add_form_fields', 10, 2 );

/**
 * Add term-meta fields to Edit {Term} screen.
 *
 * @param string $tag      Current taxonomy term object.
 * @param string $taxonomy Current taxonomy slug.
 * @return void
 */
function newsstats_region_edit_form_fields( $term ) {
    global $nn_region_meta;

    $term_id   = $term->term_id;
    $term_meta = get_term_meta( $term_id );

    // wp_nonce_field( basename( __FILE__ ), 'newsstats_region_nonce' );
    foreach ( $nn_region_meta as $key => $value ) {
        $el_id = str_replace( '_', '-', $key);
    ?>
    <tr class="form-field">
        <th><label for=<?php echo $el_id; ?>><?php _e( $value[0], 'newsnetrics' ); ?></label></th>
        <td>
            <input type="text" name="<?php echo $key ?>" id="<?php echo $el_id ?>" value="<?php echo isset( $term_meta[$key] ) ? esc_attr( $term_meta[$key][0] ) : ''; ?>">
        </td>
    </tr>
    <?php
    }
}
add_action( 'region_edit_form_fields', 'newsstats_region_edit_form_fields', 10, 2 );

function newsstats_region_save_meta( $term_id ) {
    global $nn_region_meta;

    foreach ( $nn_region_meta as $key => $value ) {
        if ( isset( $_POST[$key] ) ) {
            $meta_val = $_POST[$key];
            if( $meta_val ) {
                 update_term_meta( $term_id, $key, $meta_val );
            }
        }
    }
}
add_action( 'edited_region', 'newsstats_region_save_meta', 10, 2 );
add_action( 'create_region', 'newsstats_region_save_meta', 10, 2 );


/*******************************
 =API
 ******************************/
 /**
 * Display CPT posts on front page
 *
 */
function newsstats_home_cpt_posts( $query ) {

    if( $query->is_main_query() && $query->is_home() ) {
        $query->set( 'post_type', array( 'publication') );
    }
}
add_action( 'pre_get_posts', 'newsstats_home_cpt_posts' );
/*******************************
 =API
 ******************************/
/**
 * Detect RSS feed within HMTL of an URL:
 * <link rel="alternate" type="application/rss+xml" href=...>
 *
 * Used to extract feed URL from a site's homepage.
 *
 */
function detect_feedXXX( $url ) {
    if( $html = @DOMDocument::loadHTML( file_get_contents( $url ) ) ) {
        $xpath   = new DOMXPath( $html );
        $feeds   = $xpath->query( "//head/link[@href][@type='application/rss+xml']/@href" );

        /* Array of URLs.
        $results = array();
        foreach ( $feeds as $feed ) {
            $results[] = $feed->nodeValue;
        }
        */

        // First URL.
        return $feeds[0]->nodeValue;;
    }
    return false;
}

function detect_feed( $url ) {
    $request = wp_remote_get( $url );

    if( is_wp_error( $request ) ) {
        return false; // Bail early
    }

    $body = wp_remote_retrieve_body( $request );
    $dom  = new DOMDocument();

    libxml_use_internal_errors( true ); // Suppress warnings (invalid code in HTML).
    $dom->loadHTML( $body );
    $xpath = new DomXpath( $dom );
    $feeds = $xpath->query( "//head/link[@href][@type='application/rss+xml']/@href" );

    /* Array of URLs.
    $results = array();
    foreach ( $feeds as $feed ) {
        $results[] = $feed->nodeValue;
    }
    */
    // First URL.
    if ( $feeds && isset( $feeds[0]->nodeValue ) )
        return $feeds[0]->nodeValue;
    else {
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
