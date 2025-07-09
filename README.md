# 84EM Custom Code

A WordPress plugin that provides custom functionality for the 84EM website.

## Description

This plugin contains various customizations and enhancements for the 84EM WordPress website. It extends WordPress functionality with SEO improvements, performance optimizations, and custom features tailored specifically for 84EM.

## Features

- **SEO Enhancements**
  - Custom meta tags implementation
  - Schema.org structured data for posts, pages, and projects
  - XML sitemap generation
  - Document title optimizations
  - Search functionality customizations

- **Performance Optimizations**
  - Script and style dequeuing
  - Various performance improvements

- **Content Enhancements**
  - Shortcode for displaying last updated date
  - Author page customizations
  - Shortlinks management

- **Integrations**
  - Advanced Custom Fields (ACF) integration
  - Gravity Forms customizations
  - Google Reviews implementation with custom block

- **Security & Functionality**
  - Comments system management (disable comments)

## Components

The plugin includes the following components:

- **acf.php** - Advanced Custom Fields integration
- **author-pages.php** - Customizations for author pages
- **dequeue.php** - Removes unnecessary scripts and styles
- **disable-comments.php** - Disables WordPress comments functionality
- **document-title.php** - Customizes document titles
- **google-reviews-block** - Custom block for displaying Google Reviews
- **google-reviews.php** - Google Reviews integration
- **gravity-forms.php** - Gravity Forms integration and customizations
- **meta-tags.php** - Handles meta tags for SEO
- **performance.php** - Performance optimizations
- **schema.php** - Schema.org markup implementation
- **search.php** - Search functionality customizations
- **shortcode-last-updated.php** - Shortcode for displaying last updated date
- **shortlinks.php** - Custom shortlinks functionality
- **sitemap.php** - XML sitemap generation

## Installation

1. Upload the `84em-custom` folder to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. No additional configuration is needed as the plugin works automatically

## Requirements

- WordPress 5.0 or higher
- PHP 7.2 or higher

## Usage

Most features work automatically after activation. The plugin enhances your site's SEO, performance, and functionality without requiring manual configuration.

### Shortcodes

- `[last_updated]` - Displays the last updated date of a post or page

## XML Sitemap

The plugin automatically generates an XML sitemap at the root of your website (`sitemap.xml`). The sitemap is updated whenever a post, page, or project is published, with a 5-minute delay to prevent excessive regeneration.

## Version

Current version: 1.0

## Author

Andrew Miller @ 84EM - [https://84em.com/](https://84em.com/)

## License

This plugin is proprietary software developed specifically for 84EM.
