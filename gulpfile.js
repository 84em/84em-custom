/**
 * Gulp configuration file for 84EM Custom plugin
 *
 * This file defines tasks for optimizing JS and CSS files in the plugin.
 */

const gulp = require('gulp');
const cleanCSS = require('gulp-clean-css');
const terser = require('gulp-terser');
const rename = require('gulp-rename');
const sourcemaps = require('gulp-sourcemaps');
const { series, parallel, watch } = require('gulp');
const del = require('del');

// File paths
const paths = {
  styles: {
    src: [
      './inc/google-reviews-block/style.css',
      './inc/google-reviews-block/editor.css'
    ],
    dest: './inc/google-reviews-block/'
  },
  scripts: {
    src: './inc/google-reviews-block/block.js',
    dest: './inc/google-reviews-block/'
  },
  // Add paths for any future JS/CSS files here
};

// Clean task - removes previously generated .min files
function clean() {
  return del([
    './inc/google-reviews-block/*.min.css',
    './inc/google-reviews-block/*.min.js'
  ]);
}

// CSS optimization task
function styles() {
  return gulp.src(paths.styles.src)
    .pipe(sourcemaps.init())
    .pipe(cleanCSS({
      compatibility: 'ie8',
      level: {
        1: {
          specialComments: 0
        }
      }
    }))
    .pipe(rename({ suffix: '.min' }))
    .pipe(sourcemaps.write('./'))
    .pipe(gulp.dest(paths.styles.dest));
}

// JavaScript optimization task
function scripts() {
  return gulp.src(paths.scripts.src)
    .pipe(sourcemaps.init())
    .pipe(terser({
      compress: {
        drop_console: true
      }
    }))
    .pipe(rename({ suffix: '.min' }))
    .pipe(sourcemaps.write('./'))
    .pipe(gulp.dest(paths.scripts.dest));
}

// Watch task for development
function watchFiles() {
  watch(paths.styles.src, styles);
  watch(paths.scripts.src, scripts);
}

// Define complex tasks
const build = series(clean, parallel(styles, scripts));
const dev = series(build, watchFiles);

// Export tasks
exports.clean = clean;
exports.styles = styles;
exports.scripts = scripts;
exports.watch = watchFiles;
exports.build = build;
exports.default = dev;
