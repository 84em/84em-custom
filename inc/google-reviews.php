<?php

namespace EightyFourEM;

defined( 'ABSPATH' ) || exit;

class GoogleReviewsBlock {
    /**
     * Constructor method for initializing hooks and actions.
     *
     * @return void
     */
    public function __construct() {
        \add_action( 'init', [ $this, 'init' ] );
        \add_action( 'admin_menu', [ $this, 'add_admin_menu' ] );
        \add_action( 'admin_init', [ $this, 'settings_init' ] );
        \add_action( 'wp_ajax_get_google_reviews', [ $this, 'ajax_get_reviews' ] );
        \add_action( 'wp_ajax_nopriv_get_google_reviews', [ $this, 'ajax_get_reviews' ] );
    }

    /**
     * Initializes the block by registering scripts, styles, and attributes.
     *
     * This method is responsible for registering the editor and frontend scripts/styles
     * for the Google Reviews block, setting up its attributes, and ensuring
     * proper block type registration with a render callback. It also localizes
     * the script to enable AJAX functionality.
     *
     * @return void
     */
    public function init(): void {
        // Determine if we should use minified files
        $suffix = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min';

        // Check if minified files exist, otherwise fallback to non-minified
        $js_file = 'block' . $suffix . '.js';
        $js_path = \plugin_dir_path( __FILE__ ) . 'google-reviews-block/' . $js_file;
        if ( $suffix && ! file_exists( $js_path ) ) {
            $js_file = 'block.js';
        }

        $editor_css_file = 'editor' . $suffix . '.css';
        $editor_css_path = \plugin_dir_path( __FILE__ ) . 'google-reviews-block/' . $editor_css_file;
        if ( $suffix && ! file_exists( $editor_css_path ) ) {
            $editor_css_file = 'editor.css';
        }

        $style_css_file = 'style' . $suffix . '.css';
        $style_css_path = \plugin_dir_path( __FILE__ ) . 'google-reviews-block/' . $style_css_file;
        if ( $suffix && ! file_exists( $style_css_path ) ) {
            $style_css_file = 'style.css';
        }

        \wp_register_script(
            handle: 'google-reviews-block-editor',
            src: \plugin_dir_url( __FILE__ ) . 'google-reviews-block/' . $js_file,
            deps: [ 'wp-blocks', 'wp-element', 'wp-editor', 'wp-components', 'wp-i18n' ],
            ver: EIGHTYFOUREM_PLUGIN_VERSION
        );

        \wp_register_style(
            handle: 'google-reviews-block-editor',
            src: \plugin_dir_url( __FILE__ ) . 'google-reviews-block/' . $editor_css_file,
            deps: [ 'wp-edit-blocks' ],
            ver: EIGHTYFOUREM_PLUGIN_VERSION
        );

        \wp_register_style(
            handle: 'google-reviews-block-style',
            src: \plugin_dir_url( __FILE__ ) . 'google-reviews-block/' . $style_css_file,
            deps: [],
            ver: EIGHTYFOUREM_PLUGIN_VERSION
        );

        \register_block_type(
            block_type: 'google-reviews/display',
            args: [
                'editor_script'   => 'google-reviews-block-editor',
                'editor_style'    => 'google-reviews-block-editor',
                'style'           => 'google-reviews-block-style',
                'render_callback' => [ $this, 'render_block' ],
                'attributes'      => [
                    'showLink'          => [
                        'type'    => 'boolean',
                        'default' => true,
                    ],
                    'showReviewContent' => [
                        'type'    => 'boolean',
                        'default' => false,
                    ],
                    'maxReviews'        => [
                        'type'    => 'number',
                        'default' => 3,
                    ],
                    'alignment'         => [
                        'type'    => 'string',
                        'default' => 'left',
                    ],
                    'backgroundColor'   => [
                        'type'    => 'string',
                        'default' => '#f9f9f9',
                    ],
                    'textColor'         => [
                        'type'    => 'string',
                        'default' => '#333333',
                    ],
                ],
            ] );

        \wp_localize_script(
            handle: 'google-reviews-block-editor',
            object_name: 'googleReviewsAjax',
            l10n: [
                'ajax_url' => \admin_url( 'admin-ajax.php' ),
                'nonce'    => \wp_create_nonce( 'google_reviews_nonce' ),
            ] );
    }

    /**
     * Registers a new admin menu item in the WordPress dashboard under the "Settings" menu.
     *
     * @return void
     */
    public function add_admin_menu(): void {
        \add_options_page(
            page_title: 'Google Reviews Settings',
            menu_title: 'Google Reviews',
            capability: 'manage_options',
            menu_slug: 'google-reviews-block',
            callback: [ $this, 'options_page' ]
        );
    }

    /**
     * Initializes the settings for the Google Reviews block by registering options, sections, and fields.
     *
     * @return void
     */
    public function settings_init(): void {
        \register_setting(
            option_group: 'google_reviews_block',
            option_name: 'google_reviews_block_settings' );

        \add_settings_section(
            id: 'google_reviews_block_section',
            title: 'Google Reviews Configuration',
            callback: [ $this, 'settings_section_callback' ],
            page: 'google_reviews_block'
        );

        \add_settings_field(
            id: 'business_name',
            title: 'Business Name',
            callback: [ $this, 'business_name_render' ],
            page: 'google_reviews_block',
            section: 'google_reviews_block_section'
        );

        \add_settings_field(
            id: 'place_id',
            title: 'Google Place ID',
            callback: [ $this, 'place_id_render' ],
            page: 'google_reviews_block',
            section: 'google_reviews_block_section'
        );

        \add_settings_field(
            id: 'api_key',
            title: 'Google Places API Key',
            callback: [ $this, 'api_key_render' ],
            page: 'google_reviews_block',
            section: 'google_reviews_block_section'
        );
    }

    /**
     * Renders the input field for the business name in the plugin settings.
     *
     * @return void
     */
    public function business_name_render(): void {
        $options = \get_option( 'google_reviews_block_settings' );
        ?>
        <input type='text' name='google_reviews_block_settings[business_name]' value='<?php echo isset( $options['business_name'] ) ? \esc_attr( $options['business_name'] ) : ''; ?>' class='regular-text'>
        <p class='description'>Enter your business name as it appears on Google</p>
        <?php
    }

    /**
     * Renders the input field for entering the Google Place ID in the plugin settings.
     *
     * Provides a text input field for users to specify their Google Place ID and includes
     * a description with a link to the Google Place ID Finder for reference.
     *
     * @return void
     */
    public function place_id_render(): void {
        $options = \get_option( 'google_reviews_block_settings' );
        ?>
        <input type='text' name='google_reviews_block_settings[place_id]' value='<?php echo isset( $options['place_id'] ) ? \esc_attr( $options['place_id'] ) : ''; ?>' class='regular-text'>
        <p class='description'>Your Google Place ID (find it at <a href="https://developers.google.com/maps/documentation/places/web-service/place-id" target="_blank">Google Place ID Finder</a>)</p>
        <?php
    }

    /**
     * Renders the input field for the Google Places API key in the plugin settings page.
     *
     * @return void
     */
    public function api_key_render(): void {
        $options = \get_option( 'google_reviews_block_settings' );
        ?>
        <input type='text' name='google_reviews_block_settings[api_key]' value='<?php echo isset( $options['api_key'] ) ? \esc_attr( $options['api_key'] ) : ''; ?>' class='regular-text'>
        <p class='description'>Google Places API key (get one from <a href="https://console.cloud.google.com/" target="_blank">Google Cloud Console</a>)</p>
        <?php
    }

    /**
     * Callback function for rendering the content of the settings section in the plugin settings page.
     *
     * @return void
     */
    public function settings_section_callback(): void {
        echo 'Configure your Google Reviews settings below:';
    }

    /**
     * Renders the options page for the Google Reviews Block plugin in the WordPress admin area.
     *
     * @return void
     */
    public function options_page(): void {
        ?>
        <form action='options.php' method='post'>
            <h1>Google Reviews Block Settings</h1>
            <?php
            \settings_fields( 'google_reviews_block' );
            \do_settings_sections( 'google_reviews_block' );
            \submit_button();
            ?>
        </form>
        <?php
    }

    /**
     * Fetches Google Reviews data for a specific Place ID using the Google Places API.
     * Verifies the presence of API key and Place ID from settings, attempts to retrieve cached data,
     * and if not available, makes an API request to fetch the details. Caches the result for 24 hours.
     *
     * @return mixed Returns an associative array containing reviews data if successful.
     *               Returns false if API key or Place ID is missing, the request fails,
     *               or the API response is invalid.
     */
    public function get_google_reviews(): mixed {
        $options = \get_option( 'google_reviews_block_settings' );

        if ( empty( $options['place_id'] ) || empty( $options['api_key'] ) ) {
            return false;
        }

        $place_id = $options['place_id'];
        $api_key  = $options['api_key'];

        // Check for cached data (24 hour cache)
        $cache_key   = 'google_reviews_block_' . \md5( $place_id );
        $cached_data = \get_transient( $cache_key );

        if ( $cached_data !== false ) {
            return $cached_data;
        }

        $url = "https://maps.googleapis.com/maps/api/place/details/json?place_id={$place_id}&fields=name,rating,user_ratings_total,url,reviews&key={$api_key}";

        $response = \wp_remote_get( $url );

        if ( \is_wp_error( $response ) ) {
            return false;
        }

        $body = \wp_remote_retrieve_body( $response );
        $data = \json_decode( $body, true );

        if ( $data['status'] !== 'OK' ) {
            return false;
        }

        $result = $data['result'];

        $reviews_data = [
            'name'          => $result['name'],
            'rating'        => $result['rating'] ?? 0,
            'total_ratings' => $result['user_ratings_total'] ?? 0,
            'url'           => $result['url'] ?? '',
            'reviews'       => $result['reviews'] ?? [],
        ];

        // Cache for 24 hours
        \set_transient( $cache_key, $reviews_data, 24 * \HOUR_IN_SECONDS );

        return $reviews_data;
    }

    /**
     * Handles an AJAX request to retrieve Google reviews.
     * Verifies the request's nonce for security and fetches reviews.
     * Returns the reviews data in JSON format or an error message if the fetch fails.
     *
     * @return void
     */
    public function ajax_get_reviews(): void {
        if ( ! \wp_verify_nonce( $_POST['nonce'], 'google_reviews_nonce' ) ) {
            \wp_die( 'Security check failed' );
        }

        $reviews = $this->get_google_reviews();

        if ( $reviews ) {
            \wp_send_json_success( $reviews );
        } else {
            \wp_send_json_error( 'Unable to fetch reviews' );
        }
    }

    /**
     * Renders a block for displaying Google reviews.
     *
     * @param  array  $attributes  {
     *     Optional. A set of attributes passed to the block.
     *
     * @type bool $showLink Whether to display the link to view reviews on Google. Default true.
     * @type bool $showReviewContent Whether to display the review content for individual reviews. Default false.
     * @type int $maxReviews The maximum number of individual reviews to display. Default 3.
     * @type string $alignment Text alignment for the block. Default 'left'.
     * @type string $backgroundColor Background color for the block. Default '#f9f9f9'.
     * @type string $textColor Text color for the block. Default '#333333'.
     * }
     * @return bool|string The rendered block's HTML as a string, or false if the reviews could not be loaded.
     */
    public function render_block( array $attributes ): bool|string {
        $reviews = $this->get_google_reviews();

        if ( ! $reviews ) {
            return '<div class="google-reviews-block error">Unable to load reviews at this time.</div>';
        }

        $show_link           = ! isset( $attributes['showLink'] ) || $attributes['showLink'];
        $show_review_content = $attributes['showReviewContent'] ?? false;
        $max_reviews         = ! isset( $attributes['maxReviews'] ) ? 3 : \intval( $attributes['maxReviews'] );
        $alignment           = $attributes['alignment'] ?? 'left';
        $bg_color            = $attributes['backgroundColor'] ?? '#f9f9f9';
        $text_color          = $attributes['textColor'] ?? '#333333';

        $style = "background-color: {$bg_color}; color: {$text_color}; text-align: {$alignment};";

        \ob_start();
        ?>
        <div class="google-reviews-block" style="<?php echo \esc_attr( $style ); ?>">
            <div class="review-header">
                <h3><?php echo \esc_html( $reviews['name'] ); ?></h3>
            </div>
            <div class="review-rating">
                <div class="stars">
                    <?php echo $this->render_stars( $reviews['rating'] ); ?>
                </div>
                <div class="rating-text">
                    <span class="rating-number"><?php echo \esc_html( $reviews['rating'] ); ?></span>
                    <span class="rating-count">(<?php echo \esc_html( $reviews['total_ratings'] ); ?> reviews)</span>
                </div>
            </div>
            <?php if ( $show_review_content && ! empty( $reviews['reviews'] ) ): ?>
                <div class="individual-reviews">
                    <?php
                    $individual_reviews = \array_slice( $reviews['reviews'], 0, $max_reviews );
                    foreach ( $individual_reviews as $review ):
                        ?>
                        <div class="review-item">
                            <div class="review-header">
                                <div class="reviewer-info">
                                    <div class="reviewer-details">
                                        <span class="reviewer-name"><?php echo \esc_html( $review['author_name'] ); ?></span>
                                        <div class="review-rating-individual">
                                            <?php echo $this->render_stars( $review['rating'] ); ?>
                                            <span class="review-time"><?php echo \esc_html( $this->format_review_time( $review['time'] ) ); ?></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <?php if ( ! empty( $review['text'] ) ): ?>
                                <div class="review-text">
                                    <?php echo \esc_html( $review['text'] ); ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
            <?php if ( $show_link && ! empty( $reviews['url'] ) ): ?>
                <div class="review-link">
                    <a href="<?php echo \esc_url( $reviews['url'] ); ?>" target="_blank" rel="noopener">View on Google</a>
                </div>
            <?php endif; ?>
        </div>
        <?php
        return \ob_get_clean();
    }

    /**
     * Renders a string of star icons based on the provided rating value.
     *
     * @param  float  $rating  The rating value, typically between 0 and 5. Supports decimals for half stars.
     *
     * @return string A string containing the HTML representation of the star icons.
     */
    private function render_stars( float $rating ): string {
        $rating = \floatval( $rating );
        $output = '';

        for ( $i = 1; $i <= 5; $i ++ ) {
            if ( $i <= $rating ) {
                $output .= '<span class="star filled">★</span>';
            } elseif ( $i - 0.5 <= $rating ) {
                $output .= '<span class="star half">★</span>';
            } else {
                $output .= '<span class="star empty">★</span>';
            }
        }

        return $output;
    }

    /**
     * Formats a given timestamp into a human-readable review time.
     *
     * @param  int  $timestamp  The Unix timestamp of the review.
     *
     * @return string The formatted review time. Returns 'Today', 'Yesterday', or '{n} days ago'
     *                if the review is within the last 7 days. Otherwise, returns the date in 'F j, Y' format.
     */
    private function format_review_time( int $timestamp ): string {
        $review_date = \date( 'F j, Y', $timestamp );
        $days_ago    = \floor( ( \time() - $timestamp ) / ( 24 * 60 * 60 ) );

        if ( $days_ago < 7 ) {
            if ( $days_ago == 0 ) {
                return 'Today';
            } elseif ( $days_ago == 1 ) {
                return 'Yesterday';
            } else {
                return $days_ago . ' days ago';
            }
        } else {
            return $review_date;
        }
    }
}

new GoogleReviewsBlock();
