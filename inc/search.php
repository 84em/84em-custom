<?php

defined( 'ABSPATH' ) || exit;

add_filter(
	hook_name: 'pre_get_posts',
	callback: function ( $query ) {
		if ( $query->is_search && ! is_admin() && $query->is_main_query() ) {
			$query->set( 'post_type', [ 'project' ] );
		}

		return $query;
	} );
