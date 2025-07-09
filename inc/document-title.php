<?php

namespace EightyFourEM;

defined( 'ABSPATH' ) || exit;

\add_filter(
	hook_name: 'document_title',
	callback: function ( $title ) {
		$_genesis_title = \get_post_meta( \get_the_ID(), '_genesis_title', true );
		if ( ! empty( $_genesis_title ) ) {
			$title = \wp_strip_all_tags( $_genesis_title );
		}

		return $title;
	},
	priority: 100 );
