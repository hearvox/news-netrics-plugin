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
function netrics_settings_menu() {
    $netrics_options_page = add_options_page(
        __('News Netrics', 'newsnetrics' ),
        __( 'News Netrics', 'newsnetrics' ),
        'manage_options',
        'newsnetrics',
        'netrics_settings_display'
    );

    // Adds contextual Help tab on Settings page.
    add_action( "load-$netrics_options_page", 'netrics_help_tab');
}
add_action('admin_menu', 'netrics_settings_menu');

/**
 * Adds tabs, sidebar, and content to contextual Help tab on Settings page.
 *
 * Sets Settings page screen ID: 'settings_page_postscript'.
 * @since   0.1.0
 */
function netrics_help_tab() {
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
function netrics_settings_display() {
    ?>
    <div class="wrap">
        <h1>News Netrics (<?php echo NEWSNETRICS_VERSION; ?>)</h1>
        <section style="padding-bottom: 2rem;">
            <header>
                <h2>Display New Data</h2>
            </header>
            <form method="post">
                <fieldset>
                    <label><?php _e( 'Choose month for which to display PSI data:', 'newsnetrics' ); ?></label]>
                    <input id="month" type="month" name="month" min="<?php echo date( "Y-m", strtotime('-2 month') ); ?>" max="<?php echo date( "Y-m" ); ?>" required pattern="[0-9]{4}-[0-9]{2}"><br>
                    <?php wp_nonce_field( 'netrics_input_data_month', 'netrics_month' ); ?>
                    <input type="submit" name="nn_data_month" id="nn-data-month" class="button button-primary" value="Change Month">
                </fieldset>
            </form>
            <hr>
        </section>
        <section style="padding-bottom: 2rem;">
            <header>
                <h2>Update Data</h2>
            </header>
            <?php $data_to_update = array( 'publication', 'city', 'county', 'state' );  ?>
            <form method="post">
                <fieldset>
                    <legend><?php _e( 'Choice type of data (used for maps, tables, etc.) to update:', 'newsnetrics' ); ?></legend>
                    <ul class="inside">
                        <?php foreach ( $data_to_update  as $key ): ?>
                            <li><label><input type="checkbox" id="nn-update-<?php echo $key; ?>" value="1" name="nn_data[<?php echo $key; ?>]" /> <?php echo ucfirst( $key ); ?></label></li>
                        <?php endforeach ?>
                    </ul>
                <?php wp_nonce_field( 'netrics_data', 'netrics_update_data' ); ?>
                <input type="submit" name="nn_update_data" id="nn-submit-import-tax" class="button button-primary" value="Update">
                </fieldset>
            </form>
            <?php
            // Run update scripts.
            if ( isset( $_POST['nn_update_data'] ) && isset( $_POST['nn_data'] ) && ! empty( $_POST['nn_data'] ) ) {
                if ( wp_verify_nonce( $_POST['netrics_update_data'], 'netrics_data' ) ) {
                    echo '<p class="description">Nonce verified.</p>';
                } else {
                    echo '<p class="description">Not allowed.</p>';
                }
            } else {
                    // echo '<p class="description">Select data to update.</p>';
            }
            print_r( $_POST );
            ?>
        </section>
        <hr>
        <!-- Settings option (array) form -->
        <form method="post" action="options.php">
            <?php settings_fields( 'newsnetrics' ); ?>
            <?php do_settings_sections( 'newsnetrics' ); ?>
            <?php submit_button(); ?>
        </form>
    </div><!-- .wrap -->
    <?php
}

/* ------------------------------------------------------------------------ *
 * Setting Registrations
 * ------------------------------------------------------------------------ */

/**
 * Creates settings fields via WordPress Settings API.
 *
 * @since   0.1.0
 */
function netrics_options_init() {

    // Array to pass to $callback functions as add_settings_field() $args (last param).
    $options = netrics_get_options(); // Options array: 'newsnetrics'.

    add_settings_section(
        'netrics_api_settings_section',
        __( 'Enter API Keys', 'newsnetrics' ),
        'netrics_section_callback',
        'newsnetrics'
    );

    add_settings_field(
        'netrics_psi',
        __( 'PageSpeed Insights', 'newsnetrics' ),
        'netrics_psi_callback',
        'newsnetrics',
        'netrics_api_settings_section',
        $args = array(
        	'label_for' => 'newsnetrics-psi',
        	'value'     => ( isset( $options['psi'] ) ) ? $options['psi'] : ''
        )
    );

    add_settings_field(
        'netrics_gmaps',
        __( 'Google Maps', 'newsnetrics' ),
        'netrics_gmaps_callback',
        'newsnetrics',
        'netrics_api_settings_section',
        $args = array(
            'label_for' => 'newsnetrics-gmaps',
            'value'     => ( isset( $options['gmaps'] ) ) ? $options['gmaps'] : ''
        )
    );

    add_settings_field(
        'netrics_veracity',
        __( 'Veracity AI Key', 'newsnetrics' ),
        'netrics_veracity_callback',
        'newsnetrics',
        'netrics_api_settings_section',
        $args = array(
        	'label_for' => 'newsnetrics-veracity',
        	'value'     => ( isset( $options['veracity'] ) ) ? $options['veracity'] : '',
        )
    );

    add_settings_field(
        'netrics_veracitysecret',
        __( 'Veracity Secret', 'newsnetrics' ),
        'netrics_veracitysecret_callback',
        'newsnetrics',
        'netrics_api_settings_section',
        $args = array(
            'label_for' => 'newsnetrics-veracitysecret',
            'value'     => ( isset( $options['veracitysecret'] ) ) ? $options['veracitysecret'] : '',
            'key'    => ( isset( $options['veracity'] ) ) ? $options['veracity'] : '',
        )
    );

    add_settings_field(
        'netrics_awis',
        __( 'AWIS Key', 'newsnetrics' ),
        'netrics_awis_callback',
        'newsnetrics',
        'netrics_api_settings_section',
        $args = array(
            'label_for' => 'newsnetrics-awis',
            'value'     => ( isset( $options['awis'] ) ) ? $options['awis'] : '',
        )
    );

    add_settings_field(
        'netrics_awissecret',
        __( 'AWIS Secret', 'newsnetrics' ),
        'netrics_awissecret_callback',
        'newsnetrics',
        'netrics_api_settings_section',
        $args = array(
        	'label_for' => 'newsnetrics-awissecret',
        	'value'     => ( isset( $options['awissecret'] ) ) ? $options['awissecret'] : '',
            'key'    => ( isset( $options['awis'] ) ) ? $options['awis'] : '',
        )
    );

    register_setting(
        'newsnetrics',
        'newsnetrics',
        'netrics_sanitize_data'
    );

}
add_action('admin_init', 'netrics_options_init');

/*
netrics_get_options() returns:
Array
(
    [awis] => {KEY}
    [awissecret] => {SECRET_KEY}
    [gmaps] => {KEY}
    [psi] => {KEY}
    [veracity] => {KEY}
    [veracitysecret] => {SECRET_KEY}
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
function netrics_section_callback() {
    ?>
    <p class="description"><?php _e( 'Test links open in new window.', 'newsnetrics' ); ?><p>
    <?php
}

/* ------------------------------------------------------------------------ *
 * Field Callbacks (Get/Set Admin Option Array)
 * ------------------------------------------------------------------------ */

/**
 * Outputs form field to set API Key.
 *
 * @since   0.1.0
 */
function netrics_psi_callback( $args ) {
    ?>
    <input type="text" required id="newsnetrics-psi" name="newsnetrics[psi]" size="82" value="<?php if ( isset ( $args['value'] ) ) { echo esc_attr( $args['value'] ); } ?>" />
    <?php if ( isset ( $args['value'] ) ) { ?>
    <p class="description">
        Test Key: <a target="_blank" href="https://www.googleapis.com/pagespeedonline/v5/runPagespeed?key=<?php echo "{$args['value']}"; ?>&url=<?php echo site_url(); ?>">
            <?php echo 'PageSpeed Insights'; ?>
        </a>
    <p>
    <?php } ?>
    <?php
}

/**
 * Outputs form field to set API Key.
 *
 * @since   0.1.0
 */
function netrics_gmaps_callback( $args ) {
    ?>
    <input type="text" id="newsnetrics-gmaps" name="newsnetrics[gmaps]" size="82" value="<?php if ( isset ( $args['value'] ) ) { echo esc_attr( $args['value'] ); } ?>" />
    <hr>
    <?php
}

/**
 * Outputs form field to set API Key.
 *
 * @since   0.1.0
 */
function netrics_veracity_callback( $args ) {
    ?>
    <input type="text" id="newsnetrics-veracity" name="newsnetrics[veracity]" size="82" value="<?php if ( isset ( $args['value'] ) ) { echo esc_attr( $args['value'] ); } ?>" />
    <?php
}

/**
 * Outputs form field to set API Key.
 *
 * @since   0.1.0
 */
function netrics_veracitysecret_callback( $args ) {
    ?>
    <input type="text" id="newsnetrics-veracitysecret" name="newsnetrics[veracitysecret]" size="82" value="<?php if ( isset ( $args['value'] ) ) { echo esc_attr( $args['value'] ); } ?>" />
    <?php if ( isset ( $args['key'] ) && isset ( $args['value'] ) ) { ?>
    <p class="description">
        Test Key: <a target="_blank" href="https://<?php echo "{$args['key']}:{$args['value']}"; ?>@dashboard.veracity.ai/api/v1/auth_test/">
            <?php echo 'Veracity.ai'; ?>
        </a>
    <p>
    <?php } ?>
    <hr>
    <?php
}

/**
 * Outputs form field to set API Key.
 *
 * @since   0.1.0
 */
function netrics_awis_callback( $args ) {
    ?>
    <input type="text" id="newsnetrics-awis" name="newsnetrics[awis]" size="82" value="<?php if ( isset ( $args['value'] ) ) { echo esc_attr( $args['value'] ); } ?>" />
    <?php
}

/**
 * Outputs URL form field to set API Key.
 *
 * @since   0.1.0
 */
function netrics_awissecret_callback( $args ) {
    ?>
    <input type="text" id="newsnetrics-awissecret" name="newsnetrics[awissecret]" size="82" value="<?php if ( isset ( $args['value'] ) ) { echo esc_attr( $args['value'] ); } ?>" />
    <?php if ( isset ( $args['key'] ) && isset ( $args['value'] ) ) { ?>
        <?php $awis_url = plugin_dir_url( __FILE__ ) . "api/awis-urlinfo.php?k1={$args['key']}&k2={$args['value']}&site=" . 'rjionline.org'; ?>
    <p class="description">
        Test Key: <a target="_blank" href="<?php echo $awis_url; ?>"><?php echo 'Alexa Web Information Service'; ?></a>
    <p>
    <?php } ?>
    <hr>
    <?php
}
