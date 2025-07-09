<?php

defined( 'ABSPATH' ) || exit;

// disable author pages
add_action(
	hook_name: 'template_redirect',
	callback: function () {
		if ( is_author() ) {
			wp_redirect( location: home_url(), status: 301 );
			exit();
		}
	} );
