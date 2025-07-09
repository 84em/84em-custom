(function(blocks, element, editor, components, i18n) {
	const { registerBlockType } = blocks;
	const { createElement, Component } = element;
	const { InspectorControls } = editor;
	const { PanelBody, ToggleControl, SelectControl, ColorPicker, RangeControl } = components;
	const { __ } = i18n;

	class GoogleReviewsEdit extends Component {
		constructor(props) {
			super(props);
			this.state = {
				reviews: null,
				loading: true,
				error: null
			}
		}

		formatReviewTime(timestamp) {
			const reviewDate = new Date(timestamp * 1000);
			const now = new Date();
			const daysAgo = Math.floor((now - reviewDate) / (24 * 60 * 60 * 1000));

			if (daysAgo < 7) {
				if (daysAgo === 0) {
					return 'Today';
				} else if (daysAgo === 1) {
					return 'Yesterday';
				} else {
					return daysAgo + ' days ago';
				}
			} else {
				return reviewDate.toLocaleDateString('en-US', {
					year: 'numeric',
					month: 'long',
					day: 'numeric'
				});
			}
		};

		componentDidMount() {
			this.fetchReviews();
		}

		fetchReviews() {
			const formData = new FormData();
			formData.append('action', 'get_google_reviews');
			formData.append('nonce', googleReviewsAjax.nonce);

			fetch(googleReviewsAjax.ajax_url, {
				method: 'POST',
				body: formData
			})
				.then(response => response.json())
				.then(data => {
					if (data.success) {
						this.setState({
							reviews: data.data,
							loading: false
						});
					}
					else {
						this.setState({
							error: data.data || 'Failed to load reviews',
							loading: false
						});
					}
				})
				.catch(error => {
					this.setState({
						error: 'Network error',
						loading: false
					});
				});
		}

		renderStars(rating) {
			const stars = [];
			for (let i = 1; i <= 5; i++) {
				let starClass = 'star empty';
				if (i <= rating) {
					starClass = 'star filled';
				}
				else if (i - 0.5 <= rating) {
					starClass = 'star half';
				}

				stars.push(
					createElement('span', {
						key: i,
						className: starClass
					}, '★')
				);
			}
			return stars;
		}

		render() {
			const { attributes, setAttributes } = this.props;
			const { showLink, showReviewContent, maxReviews, alignment, backgroundColor, textColor } = attributes;
			const { reviews, loading, error } = this.state;

			const inspectorControls = createElement(InspectorControls, {},
				createElement(PanelBody, {
						title: __('Display Settings'),
						initialOpen: true
					},
					createElement(ToggleControl, {
						label: __('Show Google Link'),
						checked: showLink,
						onChange: (value) => setAttributes({ showLink: value })
					}),
					createElement(ToggleControl, {
						label: __('Show Individual Reviews'),
						checked: showReviewContent,
						onChange: (value) => setAttributes({ showReviewContent: value })
					}                    ),
					showReviewContent && createElement(RangeControl, {
						label: __('Max Reviews to Show'),
						value: maxReviews,
						onChange: (value) => setAttributes({ maxReviews: value }),
						min: 1,
						max: 5
					}),
					createElement(SelectControl, {
						label: __('Alignment'),
						value: alignment,
						options: [
							{ label: 'Left', value: 'left' },
							{ label: 'Center', value: 'center' },
							{ label: 'Right', value: 'right' }
						],
						onChange: (value) => setAttributes({ alignment: value })
					})
				),
				createElement(PanelBody, {
						title: __('Color Settings'),
						initialOpen: false
					},
					createElement('div', { style: { marginBottom: '15px' } },
						createElement('label', {}, __('Background Color')),
						createElement(ColorPicker, {
							color: backgroundColor,
							onChangeComplete: (color) => setAttributes({ backgroundColor: color.hex })
						})
					),
					createElement('div', {},
						createElement('label', {}, __('Text Color')),
						createElement(ColorPicker, {
							color: textColor,
							onChangeComplete: (color) => setAttributes({ textColor: color.hex })
						})
					)
				)
			);

			if (loading) {
				return [
					inspectorControls,
					createElement('div', {
						className: 'google-reviews-block loading',
						style: {
							backgroundColor: backgroundColor,
							color: textColor,
							textAlign: alignment
						}
					}, __('Loading Google Reviews...'))
				];
			}

			if (error) {
				return [
					inspectorControls,
					createElement('div', {
							className: 'google-reviews-block error',
							style: {
								backgroundColor: backgroundColor,
								color: textColor,
								textAlign: alignment
							}
						},
						createElement('p', {}, __('Error: ') + error),
						createElement('p', {}, __('Please check your settings in Settings → Google Reviews'))
					)
				];
			}

			return [
				inspectorControls,
				createElement('div', {
						className: 'google-reviews-block',
						style: {
							backgroundColor: backgroundColor,
							color: textColor,
							textAlign: alignment
						}
					},
					createElement('div', { className: 'review-header' },
						createElement('h3', {}, reviews.name)
					),
					createElement('div', { className: 'review-rating' },
						createElement('div', { className: 'stars' },
							this.renderStars(reviews.rating)
						),
						createElement('div', { className: 'rating-text' },
							createElement('span', { className: 'rating-number' }, reviews.rating),
							createElement('span', { className: 'rating-count' },
								'(' + reviews.total_ratings + ' reviews)'
							)
						)
					),
					showReviewContent && reviews && reviews.reviews && reviews.reviews.length > 0 ?
					createElement('div', { className: 'individual-reviews' },
						reviews.reviews.slice(0, maxReviews).map((review, index) =>
							createElement('div', {
									key: index,
									className: 'review-item'
								},
								createElement('div', { className: 'review-header' },
									createElement('div', { className: 'reviewer-info' },
										review.profile_photo_url ? createElement('img', {
											src: review.profile_photo_url,
											alt: review.author_name,
											className: 'reviewer-photo'
										}) : null,
										createElement('div', { className: 'reviewer-details' },
											createElement('span', { className: 'reviewer-name' }, review.author_name),
											createElement('div', { className: 'review-rating-individual' },
												createElement('div', { className: 'stars' },
													this.renderStars(review.rating)
												),
												createElement('span', { className: 'review-time' },
													this.formatReviewTime(review.time)
												)
											)
										)
									)
								),
								review.text ? createElement('div', { className: 'review-text' }, review.text) : null
							)
						)
					) : null,
					showLink && reviews && reviews.url ? createElement('div', { className: 'review-link' },
						createElement('a', {
							href: reviews.url,
							target: '_blank',
							rel: 'noopener'
						}, __('View on Google'))
					) : null
				)
			];
		}

		formatReviewTime(timestamp) {
			const reviewDate = new Date(timestamp * 1000);
			const now = new Date();
			const daysAgo = Math.floor((now - reviewDate) / (24 * 60 * 60 * 1000));

			if (daysAgo < 7) {
				if (daysAgo === 0) {
					return 'Today';
				} else if (daysAgo === 1) {
					return 'Yesterday';
				} else {
					return daysAgo + ' days ago';
				}
			} else {
				return reviewDate.toLocaleDateString('en-US', {
					year: 'numeric',
					month: 'long',
					day: 'numeric'
				});
			}
		}
	}

	registerBlockType('google-reviews/display', {
		title: __('Google Reviews'),
		description: __('Display your Google business reviews with overall rating'),
		icon: 'star-filled',
		category: 'widgets',
		keywords: [
			__('google'),
			__('reviews'),
			__('rating'),
			__('business')
		],
		attributes: {
			showLink: {
				type: 'boolean',
				default: true
			},
			showReviewContent: {
				type: 'boolean',
				default: false
			},
			maxReviews: {
				type: 'number',
				default: 3
			},
			alignment: {
				type: 'string',
				default: 'left'
			},
			backgroundColor: {
				type: 'string',
				default: '#f9f9f9'
			},
			textColor: {
				type: 'string',
				default: '#333333'
			}
		},
		edit: GoogleReviewsEdit,
		save: function() {
			// Server-side rendering
			return null;
		}
	});

})(
	window.wp.blocks,
	window.wp.element,
	window.wp.blockEditor || window.wp.editor,
	window.wp.components,
	window.wp.i18n
);
