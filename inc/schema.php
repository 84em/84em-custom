<?php

defined( 'ABSPATH' ) || exit;

add_action(
	hook_name: 'save_post',
	callback: function ( $post_id ) {
		// Prevent autosave and revision updates
		if ( wp_is_post_autosave( $post_id ) || wp_is_post_revision( $post_id ) ) {
			return;
		}

		// Check user permissions
		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}

		// Get post data
		$post      = get_post( $post_id );
		$post_type = get_post_type( $post_id );
		$post_url  = get_permalink( $post_id );
		$site_url  = get_site_url();

		// Only generate schema for posts, pages, and projects
		if ( ! in_array( $post_type, [ 'post', 'page', 'project' ] ) ) {
			return;
		}

		// Base schema structure
		$schema = [
			'@context'      => 'https://schema.org',
			'@type'         => 'WebPage',
			'@id'           => $post_url . '#webpage',
			'url'           => $post_url,
			'name'          => get_the_title( $post_id ),
			'description'   => get_post_meta( $post_id, '_genesis_description', true ) ?: wp_trim_words( strip_tags( $post->post_content ), 25 ),
			'inLanguage'    => 'en-US',
			'datePublished' => get_the_date( 'c', $post_id ),
			'dateModified'  => get_the_modified_date( 'c', $post_id ),
			'isPartOf'      => [
				'@type' => 'WebSite',
				'@id'   => $site_url . '/#website',
				'url'   => $site_url,
				'name'  => '84EM',
			],
			'breadcrumb'    => [
				'@type'           => 'BreadcrumbList',
				'itemListElement' => [],
			],
		];

		// Generate breadcrumbs
		$breadcrumbs = [
			[
				'@type'    => 'ListItem',
				'position' => 1,
				'name'     => 'Home',
				'item'     => $site_url,
			],
		];

		// Add post type specific breadcrumb
		if ( $post_type === 'project' ) {
			$breadcrumbs[] = [
				'@type'    => 'ListItem',
				'position' => 2,
				'name'     => 'Projects',
				'item'     => $site_url . '/project/',
			];
			$breadcrumbs[] = [
				'@type'    => 'ListItem',
				'position' => 3,
				'name'     => get_the_title( $post_id ),
				'item'     => $post_url,
			];
		} elseif ( $post_type === 'post' ) {
			$breadcrumbs[] = [
				'@type'    => 'ListItem',
				'position' => 2,
				'name'     => 'Blog',
				'item'     => get_permalink( get_option( 'page_for_posts' ) ),
			];
			$breadcrumbs[] = [
				'@type'    => 'ListItem',
				'position' => 3,
				'name'     => get_the_title( $post_id ),
				'item'     => $post_url,
			];
		} else {
			$breadcrumbs[] = [
				'@type'    => 'ListItem',
				'position' => 2,
				'name'     => get_the_title( $post_id ),
				'item'     => $post_url,
			];
		}

		$schema['breadcrumb']['itemListElement'] = $breadcrumbs;

		// Post type specific schema enhancements
		switch ( $post_type ) {
			case 'project':
				$schema['@type']   = 'CreativeWork';
				$schema['creator'] = [
					'@type' => 'Organization',
					'@id'   => $site_url . '/#organization',
					'name'  => '84EM',
				];
				$schema['genre']   = 'WordPress Development';

				// Extract keywords from content
				$content  = strip_tags( $post->post_content );
				$keywords = [];

				// Common WordPress development keywords to look for
				$tech_keywords = [
					'WordPress',
					'plugin',
					'API',
					'WooCommerce',
					'custom',
					'integration',
					'security',
					'database',
					'PHP',
					'JavaScript',
					'migration',
					'multisite',
					'theme',
					'Gravity Forms',
					'LearnDash',
					'financial',
					'healthcare',
					'banking',
					'enterprise',
					'white label',
				];

				foreach ( $tech_keywords as $keyword ) {
					if ( stripos( $content, $keyword ) !== false ) {
						$keywords[] = $keyword;
					}
				}

				if ( ! empty( $keywords ) ) {
					$schema['keywords'] = implode( ', ', array_unique( $keywords ) );
				}

				break;

			case 'post':
				$schema['@type']     = 'BlogPosting';
				$schema['author']    = [
					'@type' => 'Person',
					'name'  => get_the_author_meta( 'display_name', $post->post_author ),
					'url'   => get_author_posts_url( $post->post_author ),
				];
				$schema['publisher'] = [
					'@type' => 'Organization',
					'@id'   => $site_url . '/#organization',
					'name'  => '84EM',
				];

				// Add categories and tags
				$categories = get_the_category( $post_id );
				if ( ! empty( $categories ) ) {
					$schema['articleSection'] = $categories[0]->name;
				}

				$tags = get_the_tags( $post_id );
				if ( ! empty( $tags ) ) {
					$schema['keywords'] = implode( ', ', wp_list_pluck( $tags, 'name' ) );
				}

				break;

			case 'page':
				// Handle specific pages
				$page_slug = $post->post_name;

				switch ( $page_slug ) {
					case 'contact':
						$schema['@type']      = 'ContactPage';
						$schema['mainEntity'] = [
							'@type'        => 'Organization',
							'@id'          => $site_url . '/#organization',
							'name'         => '84EM',
							'contactPoint' => [
								'@type'             => 'ContactPoint',
								'contactType'       => 'customer service',
								'areaServed'        => 'Worldwide',
								'availableLanguage' => 'English',
							],
						];
						break;

					case 'services':
						$schema['mainEntity'] = [
							'@type'           => 'Organization',
							'@id'             => $site_url . '/#organization',
							'hasOfferCatalog' => [
								'@type'           => 'OfferCatalog',
								'name'            => 'WordPress Development Services',
								'itemListElement' => [
									[
										'@type'       => 'Offer',
										'itemOffered' => [
											'@type'    => 'Service',
											'name'     => 'Custom WordPress Plugin Development',
											'provider' => [
												'@type' => 'Organization',
												'@id'   => $site_url . '/#organization',
											],
										],
									],
								],
							],
						];
						break;

					case 'people':
						$schema['mainEntity'] = [
							'@type'      => 'Person',
							'name'       => 'Andrew Miller',
							'jobTitle'   => 'Founder & Lead WordPress Developer',
							'worksFor'   => [
								'@type' => 'Organization',
								'@id'   => $site_url . '/#organization',
							],
							'knowsAbout' => [
								'WordPress Plugin Development',
								'PHP Programming',
								'API Integration',
								'WordPress Security',
							],
						];
						break;

					case 'now':
						$schema['mainEntity'] = [
							'@type'       => 'ItemList',
							'name'        => 'Current Development Projects',
							'description' => 'Projects we\'re actively involved with, updated frequently',
						];
						break;

					case 'privacy-policy':
						$schema['mainEntity'] = [
							'@type'       => 'DigitalDocument',
							'name'        => '84EM Privacy Policy',
							'description' => 'Privacy policy governing WordPress development services and data protection practices',
							'author'      => [
								'@type' => 'Organization',
								'@id'   => $site_url . '/#organization',
							],
						];
						break;
				}
				break;
		}

		// Add featured image if available
		$featured_image_id = get_post_thumbnail_id( $post_id );
		if ( $featured_image_id ) {
			$image_url  = wp_get_attachment_image_url( $featured_image_id, 'full' );
			$image_meta = wp_get_attachment_metadata( $featured_image_id );

			$schema['image'] = [
				'@type'  => 'ImageObject',
				'url'    => $image_url,
				'width'  => $image_meta['width'] ?? 1200,
				'height' => $image_meta['height'] ?? 630,
			];
		}

		// Convert to JSON and save to post meta
		$schema_json = wp_json_encode( $schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE );
		update_post_meta( $post_id, 'schema', $schema_json );

	},
	priority: 10,
	accepted_args: 1 );

// Function to output schema in head
add_action(
	hook_name: 'wp_head',
	callback: function () {
		if ( is_singular() ) {
			$schema_json = get_post_meta( get_the_ID(), 'schema', true );
			if ( ! empty( $schema_json ) ) {
				echo '<script type="application/ld+json">' . $schema_json . '</script>' . "\n";
			}
		}
	} );
