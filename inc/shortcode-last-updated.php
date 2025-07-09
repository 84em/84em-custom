<?php

defined( 'ABSPATH' ) || exit;

add_shortcode(
	tag: 'last_updated',
	callback: function ( $atts, $content ) {
		global $post;
		return date( "F j, Y", strtotime( $post->post_modified ) );
	} );
