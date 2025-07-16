<?php
/**
 * Plugin Name:     84EM Custom Code
 * Plugin URI:      https://84em.com/
 * Description:     A WordPress plugin that provides custom functionality for the 84EM website.
 * Version:         1.0.1
 * Author:          84EM
 * Author URI:      https://84em.com/
 */

namespace EightyFourEM;

defined( 'ABSPATH' ) or die;

// Define plugin constants
define( 'EIGHTYFOUREM_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'EIGHTYFOUREM_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'EIGHTYFOUREM_PLUGIN_VERSION', '1.0.1' );

// Include all PHP files from the inc directory
require_once EIGHTYFOUREM_PLUGIN_DIR . 'inc/acf.php';
require_once EIGHTYFOUREM_PLUGIN_DIR . 'inc/author-pages.php';
require_once EIGHTYFOUREM_PLUGIN_DIR . 'inc/dequeue.php';
require_once EIGHTYFOUREM_PLUGIN_DIR . 'inc/disable-comments.php';
require_once EIGHTYFOUREM_PLUGIN_DIR . 'inc/document-title.php';
require_once EIGHTYFOUREM_PLUGIN_DIR . 'inc/google-reviews.php';
require_once EIGHTYFOUREM_PLUGIN_DIR . 'inc/gravity-forms.php';
require_once EIGHTYFOUREM_PLUGIN_DIR . 'inc/meta-tags.php';
require_once EIGHTYFOUREM_PLUGIN_DIR . 'inc/performance.php';
require_once EIGHTYFOUREM_PLUGIN_DIR . 'inc/schema.php';
require_once EIGHTYFOUREM_PLUGIN_DIR . 'inc/search.php';
require_once EIGHTYFOUREM_PLUGIN_DIR . 'inc/shortcode-last-updated.php';
require_once EIGHTYFOUREM_PLUGIN_DIR . 'inc/shortlinks.php';
require_once EIGHTYFOUREM_PLUGIN_DIR . 'inc/sitemap.php';
