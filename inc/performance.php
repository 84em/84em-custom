<?php

defined( 'ABSPATH' ) || exit;

add_action(
	hook_name: 'wp_head',
	callback: function () {
		if ( is_front_page() ) {
			echo '<link rel="preload" as="image" href="' . esc_url( site_url( '/wp-content/uploads/2017/07/84embackground.jpg' ) ) . '">';
		}
	},
	priority: 1 );

add_action(
	hook_name: 'wp_enqueue_scripts',
	callback: function () {

		wp_dequeue_style( 'spectra-pro-block-css' );
	},
	priority: 99999 );

