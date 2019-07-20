<?php
/**
 * Custom taxonomies.
 *
 * @since   0.1.0
 *
 * @package    News Netrics
 * @subpackage news-netrics/includes
 */


/*******************************
 =REGISTER TAXONOMIES
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


/*******************************
 =TERM META
 ******************************/
// Term meta for custom taxonomy.
$nn_region_meta = array(
    'nn_region_fips'   => array( 'FIPS', 'string', 'sanitize_text_field', 'region' ),
    'nn_region_pop'    => array( 'Population', 'number', 'absint', 'region' ),
    'nn_region_latlon' => array( 'Latitude|Longitude', 'string', 'sanitize_text_field', 'region' ),
    'nn_region_misc'   => array( 'Timezone|SimpleMapsID', 'string', 'sanitize_text_field', 'region'),
);

/**
 * Register term meta for taxonomies.
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

/**
 * Make custom taxonomy admin post columns sortable.
 *
 * @param array $columns
 * @return array
 */
function netrics_add_sortable_admin_columns( $columns ) {
    $columns['taxonomy-owner'] = 'owner';
    $columns['taxonomy-cms']   = 'cms';

    return $columns;
}
// add_filter( 'manage_edit-publication_sortable_columns', 'netrics_add_sortable_admin_columns' );


/*******************************
 =DATA (stored in Page post meta)
 ******************************/
/**
 * Get data for all States (Region taxonomy term parent = 0).
 *
 * Date includes Population, total Circulation, Counties (with and w/o Publications).
 *
 * Save as post meta in 'region' Page (ID: 7594):
 * https://news.pubmedia.us/region/
 *
 * @since   0.1.0
 *
 * @param bool $set      Save data in post meta.
 * @param int  $post_id  Default Post ID of Page for post meta.
 *
 * @return array $state_data Array of data for all Region: States.
 */
function netrics_get_region_data( $set = 1, $page_id = 7594 ) {
    $args_regions = array(
        'taxonomy'    => 'region',
        'hide_empty'  => false,
        'pad_counts'  => 1,
    );
    $terms_region = get_terms( $args_regions );
    $terms_state  = wp_list_filter( $terms_region, array( 'parent' => 0 ) );

    // Sort by 'count' number.
    usort( $terms_state, function (  $a, $b ) {
        return -( $a->count <=> $b->count ); // Uses '-' to make order descending.
    } );

    foreach ($terms_state as $key => $state ) {
        $population   = get_term_meta( $state->term_id, 'nn_region_pop', true );
        // Counties in state.
        $terms_county = wp_list_filter( $terms_region, array( 'parent' => $state->term_id ) );
        $county_count = count( $terms_county );

        // Counties in state without a daily newspaper.
        $terms_county_0 = wp_list_filter( $terms_county, array( 'count' => 0 ) );
        $county_0_count = count( $terms_county_0 );

        // Get all state's daily papers.
        $args_state = array(
            'post_type'      => 'publication',
            'posts_per_page' => 100,
            'fields'         => 'ids',
            'tax_query'      => array(
                array(
                    'taxonomy' => 'region',
                    'field'    => 'id',
                    'terms'    => $state->term_id,
                )
            )
        );
        $state_pubs = new WP_Query( $args_state );

        // Sum Circulation for all state papers.
        $circ_sum = 0;
        foreach ( $state_pubs->posts as $post_id ) {
            $circ_sum += get_post_meta( $post_id, 'nn_circ', true );
        }
        wp_reset_postdata();

        // Sum of population of counties without a daily.
        $county_0_pop = 0;
        foreach ( $terms_county_0 as $county ) {
            $county_0_pop += absint( get_term_meta( $county->term_id, 'nn_region_pop', true ) );
        }

        // Calculated metrics for State:
        // Perecentage of Counties with Publication(s).
        // Perecentage of Population in Counties with Publication(s).
        // Ratio of number of daily papers to Population: every 10,000 people (Pub/Pop).
        // Ratio of total Circulation for all papers to Population (Circ/Pop).
        $county_pub_pc     = ( $county_count - $county_0_count ) / $county_count * 100; // Count % of Counties with Pub.
        $county_pub_pop_pc = ( $population - $county_0_pop ) / $population * 100; // Pop. % of Counties with Pub.
        $pub_per_pop       = ( $state->count ) ? $population / $state->count / 10000 : 0; // Pub/Pop.-10K.
        $circ_per_pop      = ( $circ_sum ) ? $population / $circ_sum : 0; // Circ./Pop.

        // $county_0_pc     = ( $county_0_count ) ? $county_0_count / $county_count * 100 : 1.0; // County w/o Pub.
        // $county_0_pop_pc = ( $county_0_pop ) ? $county_0_pop / $population * 100 : 1.0; // County0Pop%.

        $data = array(
            'term_link'         => get_term_link( $state->term_id ),
            'population'        => $population,
            'circ_sum'          => $circ_sum,
            'counties'          => $county_count,
            'county_0_count'    => $county_0_count,
            'county_0_pop'      => $county_0_pop,
            'county_pub_pc'     => $county_pub_pc,
            'county_pub_pop_pc' => $county_pub_pop_pc,
            'circ_per_pop'      => $circ_per_pop,
            'pub_per_pop'       => $pub_per_pop,
        );

        $state_data[ $key ] = array_merge( (array)$terms_state[ $key ], $data );
    }

    if ( $set ) {
        $meta = update_post_meta( $page_id, 'nn_states', $state_data );
        return $meta;
    }

    return $state_data;
}

/**
 * Get terms for all States (Region taxonomy term parent = 0).
 *
 *
 * @since   0.1.0
 *
 * @return array $state_data Array of data for all Region: States.
 */
function netrics_get_state_totals( $state_data = array() ) {
    if ( ! $state_data ) {
        $state_data = netrics_get_region_data( 0 );
    }

    $papers = $circ = $pop = $counties = $cnty_0 = $cnty_0_pop = 0;
    foreach ( $state_data as $state ) {
        $papers     += $state['count'];
        $circ       += $state['circ_sum'];
        $pop        += $state['population'];
        $counties   += $state['counties'];
        $cnty_0     += $state['county_0_count'];
        $cnty_0_pop += $state['county_0_pop'];
    }

    $usa_totals = array(
        'papers'     => $papers,
        'pop'        => $pop,
        'circ'       => $circ,
        'counties'   => $counties,
        'cnty_0'     => $cnty_0,
        'cnty_0_pop' => $cnty_0_pop
    );

    return $usa_totals;
}



/**
 * Get terms for all States (Region taxonomy term parent = 0).
 *
 *
 * @since   0.1.0
 *
 * @return array $state_data Array of data for all Region: States.
 */
function netrics_get_state_terms() {
        $args_regions = array(
        'taxonomy'    => 'region',
        'hide_empty'  => false,
        'pad_counts'  => 1,
    );
    $terms_region = get_terms( $args_regions );
    $terms_state  = wp_list_filter( $terms_region, array( 'parent' => 0 ) );

    return $terms_state;
}

/**
 * Get terms for all States (Region taxonomy term parent = 0).
 *
 *
 * @since   0.1.0
 *
 * @return array $state_data Array of data for all Region: States.
 */
function netrics_get_county_terms( $state_id ) {
    $terms_state  = netrics_get_state_terms();

    $terms_county = wp_list_filter( $terms_state, array( 'parent' => $state_id ) );

    return $terms_county;
}


/**
 * Get all Publications (CPT) for Region taxonomy term.
 *
 * @since   0.1.0
 *
 * @return array $state_data Array of data for all Region: States.
 */
function netrics_get_region_pubs( $term_id ) {
        // Get all region's daily papers.
        $args_state = array(
            'post_type'      => 'publication',
            'posts_per_page' => 100,
            'fields'         => 'ids',
            'tax_query'      => array(
                array(
                    'taxonomy' => 'region',
                    'field'    => 'id',
                    'terms'    => $term_id,
                )
            )
        );
        $state_pubs = new WP_Query( $args_state );

        wp_reset_postdata();

        return $state_pubs;
}
