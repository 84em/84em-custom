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

- WordPress 6.8 or higher
- PHP 8.0 or higher

## Development

### Changelog Management

This plugin includes automated changelog generation based on Git commit history and tags.

#### Scripts

- **update-changelog.sh** - Generates `changelog.txt` from Git log, organized by tags/releases
- **tag-and-changelog.sh** - Creates a Git tag and automatically updates the changelog

#### Usage

To create a new release with automatic changelog generation:

```bash
./tag-and-changelog.sh v1.0.3 "Release version 1.0.3 with new features"
```

This will:
1. Create a Git tag with the specified name and message
2. Generate an updated changelog.txt file
3. Commit the changelog changes
4. Provide instructions for pushing to remote repository

**Note:** The changelog is automatically updated only when creating Git tags. Manual execution of `update-changelog.sh` is not supported to ensure changelog updates occur only when tags are added to the repository.

#### Changelog Format

The generated changelog follows the [Keep a Changelog](https://keepachangelog.com/en/1.0.0/) format and includes:
- **[Unreleased]** - Commits since the latest tag
- **[Tag Version] - Date** - Commits included in each tagged release

#### Deployment Integration

The `deploy.sh` script handles deployment to the remote server. Changelog updates are handled separately through the tag creation process to ensure they only occur when Git tags are added.

### Gulp Integration

This plugin uses Gulp to optimize JavaScript and CSS files. The Gulp setup provides tasks for minifying JS files, optimizing CSS files, and watching for changes during development.

#### Setup

1. Make sure you have Node.js and npm installed
2. Navigate to the plugin directory in your terminal
3. Run `npm install` to install dependencies

#### Available Commands

- `npm start` - Starts the development process with file watching
- `npm run build` - Builds optimized files for production

#### Gulp Tasks

- `gulp clean` - Removes previously generated minified files
- `gulp styles` - Optimizes CSS files
- `gulp scripts` - Minifies JavaScript files
- `gulp watch` - Watches for changes in source files
- `gulp build` - Builds all assets for production (cleans, then processes JS and CSS)

#### File Structure

The Gulp setup processes the following files:
- JavaScript: `inc/google-reviews-block/block.js` → `inc/google-reviews-block/block.min.js`
- CSS: 
  - `inc/google-reviews-block/style.css` → `inc/google-reviews-block/style.min.css`
  - `inc/google-reviews-block/editor.css` → `inc/google-reviews-block/editor.min.css`

## Usage

Most features work automatically after activation. The plugin enhances your site's SEO, performance, and functionality without requiring manual configuration.

### Shortcodes

- `[last_updated]` - Displays the last updated date of a post or page

## XML Sitemap

The plugin automatically generates an XML sitemap at the root of your website (`sitemap.xml`). The sitemap is updated whenever a post, page, or project is published, with a 5-minute delay to prevent excessive regeneration.

## Version

Current version: 1.0.8

## Author

Andrew Miller @ 84EM - [https://84em.com/](https://84em.com/)

## License

This plugin is proprietary software developed specifically for 84EM.
