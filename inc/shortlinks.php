<?php

defined( 'ABSPATH' ) || exit;

add_filter(
	hook_name: 'after_setup_theme',
	callback: function () {
		remove_action(
			hook_name: 'wp_head',
			callback: 'wp_shortlink_wp_head',
			priority: 10 );

		remove_action(
			hook_name: 'template_redirect',
			callback: 'wp_shortlink_header',
			priority: 11 );
	} );

