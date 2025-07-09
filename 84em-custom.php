<?php
/**
 * Plugin Name:     84EM Custom Code
 * Plugin URI:      https://www.84em.com/
 * Description:     Custom code for 84EM
 * Version:         1.0
 * Author:          84EM
 * Author URI:      https://www.84em.com/
 */

defined( 'ABSPATH' ) or die;

foreach ( glob( dirname( __FILE__ ) . '/inc/*.php' ) as $file ) {
	require_once $file;
}
