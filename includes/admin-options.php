<?php
/**
 * Admin Settings Page (Dashboard> Settings> News Netrics)
 *
 * @link    http://hearingvoices.com/tools/
 * @since   0.1.0
 *
 * @package    News Netrics
 * @subpackage news-netrics/includes
 */

/* ------------------------------------------------------------------------ *
 * Wordpress Settings API
 * ------------------------------------------------------------------------ */

/**
 * Adds submenu item to Settings dashboard menu.
 *
 * @since   0.1.0
 *
 * Sets Settings page screen ID: 'settings_page_postscript'.
 */
function newsnetrics_settings_menu() {
    $newsnetrics_options_page = add_options_page(
        __('News Netrics', 'newsnetrics' ),
        __( 'News Netrics', 'newsnetrics' ),
        'manage_options',
        'newsnetrics',
        'newsnetrics_settings_display'
    );

    // Adds contextual Help tab on Settings page.
    add_action( "load-$newsnetrics_options_page", 'newsnetrics_help_tab');
}
add_action('admin_menu', 'newsnetrics_settings_menu');

/**
 * Adds tabs, sidebar, and content to contextual Help tab on Settings page.
 *
 * Sets Settings page screen ID: 'settings_page_postscript'.
 * @since   0.1.0
 */
function newsnetrics_help_tab() {
    $current_screen = get_current_screen();

    // Default tab.
    $current_screen->add_help_tab(
        array(
            'id'        => 'settings',
            'title'     => __( 'Settings', 'ssppod' ),
            'content'   =>
                '<p>' . __( 'News Netrics (Author: Barrett Golding &lt;bg@hearingvoices.com&gt;.)', 'ssppod' ) . '</p>',
        )
    );

    // Sidebar.
    $current_screen->set_help_sidebar(
        '<p><strong>' . __( 'Links:', 'newsnetrics' ) . '</strong></p>' .
        '<p><a href="https://www.rjionline.org/">'     . __( 'Reynolds Journalism Institute', 'newsnetrics' ) . '</a></p>' .
        '<p><a href="https://journalism.missouri.edu/">' . __( 'Missouri School of Journalism', 'newsnetrics' ) . '</a></p>' );
}

/**
 * Renders settings menu page.
 *
 * @since   0.1.0
 */
function newsnetrics_settings_display() {
    ?>
    <div class="wrap">
        <h1>News Netrics (<?php echo NEWSNETRICS_VERSION; ?>)</h1>
        <section style="padding-bottom: 2rem;">
            <header>
                <h2>Import Taxonomy Terms from CSV</h2>
            </header>
            <!-- Update feed form -->
            <form method="post">
                <p>File dir: <?php echo NEWSNETRICS_DIR; ?>import/<br>
                CSV files: us-state.csv, us-county.csv, us-city.csv, us-newspapers.csv</p>
                <label for="nn_csv">Enter filename</label><br>
                <input type="text" name="nn_csv" id="nn-csv" value="" class="regular-text">
                <input type="submit" name="nn_submit_import_tax" id="nn-submit-import-tax" class="button button-primary" value="Import">
                <?php wp_nonce_field( 'newsnetrics_tax', 'newsnetrics_import_tax' ); ?>
            </form>
            <?php
            //
            if ( isset( $_POST['nn_submit_import_tax'] ) && isset( $_POST['nn_csv'] ) && ! empty( $_POST['nn_csv'] ) ) {
                if ( wp_verify_nonce( $_POST['newsnetrics_import_tax'], 'newsnetrics_tax' ) ) {
                    $csv = NEWSNETRICS_DIR . 'import/' . $_POST['nn_csv'];
                    newsnetrics_import_tax_terms( $csv );
                } else {
                    echo '<p class="description">Not allowed.</p>';
                }
            } else {
                    echo '<p class="description">Enter CSV.</p>';
            }
            // print_r( $_POST );
            ?>
        </section>
        <hr>
        <!-- Settings option (array) form -->
        <form method="post" action="options.php">
            <?php settings_fields( 'newsnetrics' ); ?>
            <?php do_settings_sections( 'newsnetrics' ); ?>
            <?php submit_button(); ?>
        </form>
        <pre>
        <?php

        /*
        $terms = get_terms( array(
            'taxonomy'   => 'region',
            'hide_empty' => false,
            'number'     => 3500,
            'orderby'   => 'id',
        ) );
        foreach ($terms as $term ) {
            if ( $term->parent ) {
                $term_meta = get_term_meta( $term->term_id );
                echo "{$term->name},{$term->slug},{$term->term_id},\"{$term->description}\",";
                echo "{$term_meta['nn_region_fips'][0]},{$term_meta['nn_region_pop'][0]},{$term->parent}\n";
            }
        }
        */

        ?>
        </pre>
    </div><!-- .wrap -->
    <?php
}


/**
 * Opens CSV file with data (tax terms and post meata) for new posts, then inserts posts..
 *
 * @since   0.1.0
 */
function newsnetrics_import_tax_terms( $csv ) {
    echo '<pre><ol>';
    echo $exists = ( file_exists( $csv ) ) ? $csv . "\n" : "N'existe pas.\n";
    $row = 0;
    if ( ( $handle = fopen( $csv, 'r' ) ) !== FALSE ) {
        while ( ( $data = fgetcsv( $handle, 300, ',' ) ) !== FALSE ) {
            // $obj_id = newsstats_insert_term_city( $data );
            $obj_id = newsstats_insert_post_pub( $data );
            $num    = count( $data );
            $row++;
            echo '<li>';

            for ( $c = 0; $c < $num; $c++ ) {
                // echo $c . '-' . $data[$c] . ","; // Print array index#,
                echo $data[$c] . ",";
            }

            echo $obj_id;
            echo '</li>';
        }
        fclose($handle);
    } else {
        echo 'no';
    }
    echo '</ol></pre>';
}

/**
 *  Inserts new posts with data from array.
 *
 * @since   0.1.0
 */
function newsstats_insert_post_pub( $data ) {
    // Add new CPT post. $data =
    // 0-domain,1-name,2-city,3-state,4-city_id,5-owner,6-cms,7-url,8-rss,9-circ,10-year,11-circ_paid|circ_free|fmp_id
    $post_arr = array(
        'post_title'   => $data[0],
        'post_excerpt' => $data[1],
        'post_content' => $data[1] . ' &lt;' . $data[0] . '&gt;, ' . $data[2] . ', ' . $data[3],
        'post_type'    => 'publication',
        'post_status'  => 'publish',
        'tax_input'    => array(
            'region'   => $data[4],
            'owner'    => $data[5],
            'cms'      => $data[6],
        ),
        'meta_input'   => array(
            'nn_pub_site' => $data[0],
            'nn_pub_name' => $data[1],
            'nn_pub_url'  => $data[7],
            'nn_pub_rss'  => $data[8],
            'nn_pub_circ' => $data[9],
            'nn_pub_year' => $data[10],
            'nn_pub_misc' => $data[11],
        ),
    );
    $post_id = wp_insert_post( $post_arr, true );

    if ( is_wp_error( $post_id ) ) { // If new term created.
        return 'Error:' . $post_id->get_error_message();
    } else {
        return $post_id;
    }
}

/**
 * Add new CPT post with tax terms.
 *
 * @uses    wp_insert_term()
 *
 * @param   int  $user_id  ID of newly registered user.
 * @return  void
 */
function newsstats_insert_term_city( $data ) {
    // Add tax term. $data =
    // 0-city,1=slug,2-parent_id,3-county,4-state,5-fips,6-pop,7-lat|lon,8-ascii|timezone|pop|pop_density|simplemaps_id
    $term = term_exists( $data[0], 'region', $data[2] );
    if ( ! $term ) {
        $args = array(
        'description' => $data[0] . ' (' . $data[3] . ') ' . $data[4],
        'parent'      => $data[2],
        'slug'        => $data[1],
        );
        $term = wp_insert_term( $data[0], 'region', $args );

        if ( ! is_wp_error( $term ) ) { // If new term created.
            // Add term ID to CPT meta.
            $term_id = $term['term_id'];
            update_term_meta( $term_id, 'nn_region_fips', $data[5] );
            update_term_meta( $term_id, 'nn_region_pop', $data[6] );
            update_term_meta( $term_id, 'nn_region_latlon', $data[7]  );
            update_term_meta( $term_id, 'nn_region_misc', $data[8] );
        } else {
            echo $term->get_error_message();
        }
        $term_id = $term['term_id'];
    } else {
        $term = get_term_by('name', $data[0], 'region');
        $term_id = $term->term_id;
    }
    return $term_id;
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


/**
 * Update 'region' tax term meta (Census data for Counties).
 *
 * @uses    wp_insert_term()
 *
 * @param   int  $user_id  ID of newly registered user.
 * @return  void
 */
function netrics_update_term_meta_county( $data_array ) {
    /*
    Array (
        [0] => county_term_id
        [1] => GEOID('nn_region_geoid')
        [2] => county_name
        [3] => state_name
        [4] => slug
        [5] => state_id
        [6] => state_term_id
        [7] => census_2018_pop ('nn_region_census'|)
        [8] => pop_density_land ('nn_region_density')
        [9] => area_total ('nn_region_area' and 'nn_region_census'|)
        [10] => area_water ('nn_region_census'|)
        [11] => area_land ('nn_region_census'|)
        [12] => housing_units ('nn_region_census')
        [13] => pop_density_housing
    )
    */

    array_shift( $data_array ); // First element is column names.

    foreach ( $data_array as $key => $data ) {
        $term_id = absint( $data[0] );
        $exists = term_exists( absint( $data[0] ), 'region', absint( $data[0] ) );

        if ( term_exists( $term_id, 'region', absint( $data[0] ) ) ) {
            // $geoid = update_term_meta( $term_id, 'nn_region_geoid', sanitize_text_field( trim( $data[1],  '"' ) ) );
            $dens = update_term_meta( $term_id, 'nn_region_density', floatval( $data[8] ) );
            // update_term_meta( $term_id, 'nn_region_area', floatval( $data[9] ) );

            // Multiple census data values (pop. area, housing), pipe-separated.
            $census =
                absint( $data[7] )  . '|' .  floatval( $data[9] ) . '|' .  floatval( $data[10] ) . '|' .
                floatval( $data[11] ) . '|' .  absint( $data[12] );
            update_term_meta( $term_id, 'nn_region_census', sanitize_text_field( $census ) );
            echo "$term_id $dens {$data[8]}\n";

        } else {
            echo "$term_id n'existe pas.\n";
        }
    }


    return $exists;
}


/**
 * Add new tax term with term meta.
 *
 * @uses    wp_insert_term()
 *
 * @param   int  $user_id  ID of newly registered user.
 * @return  void
 */
function newsstats_insert_term_county( $data ) {
    // Create new tax term with CPT data.
    $args = array(
        'description' => $data[0] . ', ' . $data[3],
        'parent'      => $data[2],
        'slug'        => $data[1],
    );
    $term = wp_insert_term( $data[0], 'region', $args );

    if ( ! is_wp_error( $term ) ) { // If new term created.
        // Add term ID to CPT meta.
        $term_id = $term['term_id'];
        update_term_meta( $term_id, 'nn_region_fips', $data[4] );
        update_term_meta( $term_id, 'nn_region_pop', $data[5] );
        // update_term_meta( $term_id, 'nn_region_latlon', $data[6] );
        // update_term_meta( $term_id, 'nn_region_misc', $data[6] );
        return $term_id;
    }
}

/**
 * Add new tax term with term meta.
 *
 * @uses    wp_insert_term()
 *
 * @param   int  $user_id  ID of newly registered user.
 * @return  void
 */
function newsstats_insert_term_state( $data ) {
    // Create new tax term with CPT data.
    $args = array(
        'description' => $data[2],
        // 'slug'        => $data[1],
    );
    $term = wp_insert_term( $data[0], 'region', $args );

    if ( ! is_wp_error( $term ) ) { // If new term created.
        // Add term ID to CPT meta.
        $term_id = $term['term_id'];
        update_term_meta( $term_id, 'nn_region_fips', $data[3] );
        update_term_meta( $term_id, 'nn_region_pop', $data[4] );
        return $term_id;
    }
}

/**
 * Outputs textarea displaying feed XML.
 *
 * @since   0.1.0
 */
function newsnetrics_update_feed_xml() {
    $xml = $date_pub = $date_build = $item_count = $feed = $items = $write = '';
    $options = newsnetrics_get_options(); // Options array: 'newsnetrics'.

    // URLs in options (empty string if not an URL).
    $feed_pull_url  = esc_url_raw( $options['feed_pull_url'] );
    $feed_push_url  = esc_url_raw( $options['feed_push_url'] );
    $feed_push_path = esc_url_raw( $options['feed_push_path'] );
    $feed_tags_url  = esc_url_raw( $options['feed_tags_url'] );

    // Get external feed, if option value is an URL.
    if ( $feed_pull_url && $feed_tags_url ) {
        $xml = simplexml_load_file( $feed_pull_url );
        // Insert external feed data into XML template.
        if ( isset( $xml->channel->item ) ) {
            // Get external feed data
            $date_pub   = ( isset( $xml->channel->pubDate ) ) ? $xml->channel->pubDate[0] : '';
            $date_build = ( isset( $xml->channel->lastBuildDate ) ) ? $xml->channel->lastBuildDate[0] : '';
            $item_count = count( $xml->channel->item );

            // Get XML template.
            $feed = file_get_contents( $feed_tags_url );
            $feed = str_replace( '<!-- pubDate -->', $date_pub, $feed); // Insert pub date.
            $feed = str_replace( '<!-- lastBuildDate -->', $date_build, $feed); // Insert last build date.

            // Get items as XMl.
            foreach ( $xml->channel->item as $item ) {
                $items .= $item->asXML();
            }

            $items = str_ireplace( ' (full episode)</title>', '</title>', $items); // Clean item titles.
            $feed  = str_replace( '<!-- items -->', $items, $feed); // Insert RSS items.
        }
    }

    // Write to file (combines: open, write, close).
    $write = file_put_contents( $feed_push_path, $feed);

    // Write resulting XML into textarea.
    $feed_new = file_get_contents( $feed_push_path );
    ?>
    <p><?php _e( 'Feed date:', 'newsnetrics' ); ?> <?php echo $date_pub; ?><br>
    <?php _e( 'Number of episodes:', 'newsnetrics' ); ?> <?php echo $item_count; ?><br>
    <?php _e( 'Bytes written:', 'newsnetrics' ); ?> <?php echo $write; ?></p>
    <p><label for="ssppod-feed-xml"><strong>Updated feed XML</strong></label><br>
    <textarea readonly autofocus id="newsnetrics-feed-xml" name="newsnetrics-feed-xml" rows="12" cols="80" style="max-width: 90%;" onClick="this.setSelectionRange(0, this.value.length)"><?php echo htmlentities( $feed_new ); ?></textarea></p><br>
    <?php
    // echo '<pre>'; print_r( $options ); echo '</pre>';
}

/* ------------------------------------------------------------------------ *
 * Setting Registrations
 * ------------------------------------------------------------------------ */

/**
 * Creates settings fields via WordPress Settings API.
 *
 * @since   0.1.0
 */
function newsnetrics_options_init() {

    // Array to pass to $callback functions as add_settings_field() $args (last param).
    $options = newsnetrics_get_options(); // Options array: 'newsnetrics'.

    add_settings_section(
        'newsnetrics_soth_settings_section',
        __( 'Settings: State of the Human', 'newsnetrics' ),
        'newsnetrics_section_callback',
        'ssppod'
    );

    add_settings_field(
        'newsnetrics_feed_pull_url',
        __( 'External feed URL', 'newsnetrics' ),
        'newsnetrics_feed_pull_url_callback',
        'ssppod',
        'newsnetrics_soth_settings_section',
        $args = array(
        	'label_for' => 'newsnetrics-url-feed-pull',
        	'value'     => ( isset( $options['feed_pull_url'] ) ) ? $options['feed_pull_url'] : ''
        )
    );

    add_settings_field(
        'newsnetrics_feed_push_url',
        __( 'Your feed URL', 'newsnetrics' ),
        'newsnetrics_feed_push_url_callback',
        'ssppod',
        'newsnetrics_soth_settings_section',
        $args = array(
        	'label_for' => 'newsnetrics-feed-push-url',
        	'value'     => ( isset( $options['feed_push_url'] ) ) ? $options['feed_push_url'] : ''
        )
    );

    add_settings_field(
        'newsnetrics_feed_push_path',
        __( 'Path to your feed', 'newsnetrics' ),
        'newsnetrics_feed_push_path_callback',
        'ssppod',
        'newsnetrics_soth_settings_section',
        $args = array(
            'label_for' => 'newsnetrics-feed-push-path',
            'value'     => ( isset( $options['feed_push_path'] ) ) ? $options['feed_push_path'] : ''
        )
    );

    add_settings_field(
        'newsnetrics_feed_tags_url',
        __( 'Feed tags URL', 'newsnetrics' ),
        'newsnetrics_feed_tags_url_callback',
        'ssppod',
        'newsnetrics_soth_settings_section',
        $args = array(
        	'label_for' => 'newsnetrics-feed-tags-url',
        	'value'     => ( isset( $options['feed_tags_url'] ) ) ? $options['feed_tags_url'] : ''
        )
    );

    register_setting(
        'ssppod',
        'ssppod',
        'newsnetrics_sanitize_data'
    );

}
add_action('admin_init', 'newsnetrics_options_init');

/*
newsnetrics_get_options() returns:
Array
(
    [feed_pull_url] => {URL}
    [feed_push_url] => {URL}
    [feed_push_path] => {file path}
    [feed_tags_url] => {URL}
    [version] => {vers#}
)
*/

/* ------------------------------------------------------------------------ *
 * Section Callbacks
 * ------------------------------------------------------------------------ */

/**
 * Outputs text for the top of the Settings screen.
 *
 * @since   0.1.0
 */
function newsnetrics_section_callback() {
    ?>
    <p>SSP Podcast <?php _e('pulls podcast episode data from an external feed (e.g., SoundCloud) then publishes thsoe items in your own podcast feed', 'newsnetrics' ); ?> (<?php _e('version', 'ssppod' ); ?> <?php echo newsnetrics_VERSION; ?>).</p>
    <?php
}

/* ------------------------------------------------------------------------ *
 * Field Callbacks (Get/Set Admin Option Array)
 * ------------------------------------------------------------------------ */

/**
 * Outputs URL form field to set external feed URL.
 *
 * @since   0.1.0
 */
function newsnetrics_feed_pull_url_callback( $args ) {
    ?>
    <input type="url" required id="ssppod-feed-pull-url" name="ssppod[feed_pull_url]" size="82" value="<?php if ( isset ( $args['value'] ) ) { echo esc_url( $args['value'] ); } ?>" pattern="https?://.+" title="Please specify https:// or http://." />
    <p class="description"><?php _e( 'Pull RSS episode items from this URL (e.g., a SoundCloud feed).', 'ssppod' ); ?></p>
    <?php
}

/**
 * Outputs URL form field to sets site feed URL.
 *
 * @since   0.1.0
 */
function newsnetrics_feed_push_url_callback( $args ) {
    ?>
    <input type="url" required id="ssppod-feed-push-url" name="ssppod[feed_push_url]" size="82" value="<?php if ( isset ( $args['value'] ) ) { echo esc_url( $args['value'] ); } ?>" pattern="https?://.+" title="Please specify https:// or http://." />
    <p class="description"><?php _e( 'Write RSS items to this feed file: <strong>Must</strong> be on same server as this WordPress site.', 'ssppod' ); ?><p>
    <?php
}

/**
 * Outputs text form field to set site feed path.
 *
 * @since   0.1.0
 */
function newsnetrics_feed_push_path_callback( $args ) {
    ?>
    <input type="text" required id="ssppod-feed-push-path" name="ssppod[feed_push_path]" size="82" value="<?php if ( isset ( $args['value'] ) ) { echo esc_attr( $args['value'] ); } ?>" />
    <p class="description"><?php _e( 'File path to above feed: <strong>Must</strong> be on same server as this WordPress site', 'ssppod' ); ?><p>
    <p class="description"><?php _e( 'Path to WordPress:', 'ssppod' ); ?> <?php echo ABSPATH; ?>
    <?php
}

/**
 * Outputs URL form field to set file with XML template tags.
 *
 * @since   0.1.0
 */
function newsnetrics_feed_tags_url_callback( $args ) {
    ?>
    <input type="url" required id="ssppod-feed-tags-url" name="ssppod[feed_tags_url]" size="82" value="<?php if ( isset ( $args['value'] ) ) { echo esc_url( $args['value'] ); } ?>" pattern="https?://.+" title="Please specify https:// or http://." />
    <p class="description"><?php _e( 'XML template file with tags that go above and below episode items', 'ssppod' ); ?> (<a href="<?php echo newsnetrics_URL; ?>xml/template-tags.xml"><?php _e( 'default file', 'ssppod' ); ?></a>).</p>
    <?php
}
