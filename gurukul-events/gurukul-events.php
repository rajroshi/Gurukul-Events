<?php
/*
Plugin Name: Gurukul Events Management
Description: Manage religious events like Anusthan, Deeksha, and Teachings for Hindu Gurukul
Version: 0.9.0-beta
Author: Rajesh Benjwal
Author URI: https://tantragurukul.org
Plugin URI: https://github.com/rajroshi/gurukul-events
Text Domain: gurukul-events
Domain Path: /languages
License: GPL v2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.txt
*/

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Define plugin constants
define('GURUKUL_EVENTS_VERSION', '0.9.0-beta');
define('GURUKUL_EVENTS_PATH', plugin_dir_path(__FILE__));
define('GURUKUL_EVENTS_URL', plugin_dir_url(__FILE__));
define('GURUKUL_EVENTS_BASENAME', plugin_basename(__FILE__));

// Add update checker
require_once GURUKUL_EVENTS_PATH . 'includes/plugin-update-checker/plugin-update-checker.php';
use YahnisElsts\PluginUpdateChecker\v5\PucFactory;

$updateChecker = PucFactory::buildUpdateChecker(
    'https://github.com/rajroshi/gurukul-events/',
    __FILE__,
    'gurukul-events'
);

// Set the branch that contains the stable release
$updateChecker->setBranch('main');

// Optional: If you're using releases, set the license key
$updateChecker->setAuthentication('your-github-token');

// Register Custom Post Type for Events
function gurukul_events_post_type() {
    $labels = array(
        'name' => 'Gurukul Events',
        'singular_name' => 'Gurukul Event',
        'menu_name' => 'Gurukul Events',
        'add_new' => 'Add New Event',
        'add_new_item' => 'Add New Event',
        'edit_item' => 'Edit Event',
        'new_item' => 'New Event',
        'view_item' => 'View Event',
        'search_items' => 'Search Events',
        'not_found' => 'No events found',
        'not_found_in_trash' => 'No events found in Trash',
        'all_items' => 'All Events'
    );

    $args = array(
        'labels' => $labels,
        'public' => true,
        'publicly_queryable' => true,
        'show_ui' => true,
        'show_in_menu' => true,
        'query_var' => true,
        'rewrite' => array('slug' => 'gurukul-event'),
        'capability_type' => 'post',
        'has_archive' => true,
        'hierarchical' => false,
        'menu_position' => 5,
        'menu_icon' => 'dashicons-calendar-alt',
        'supports' => array('title', 'editor', 'thumbnail', 'excerpt'),
        'show_in_rest' => true,
    );

    register_post_type('gurukul_event', $args);

    // Register Event Category Taxonomy
    register_taxonomy(
        'event_category',
        'gurukul_event',
        array(
            'label' => 'Event Categories',
            'hierarchical' => true,
            'show_ui' => true,
            'show_admin_column' => true,
            'query_var' => true,
            'rewrite' => array('slug' => 'event-category'),
            'show_in_rest' => true,
        )
    );
}

// Add columns to admin list
function gurukul_events_columns($columns) {
    $new_columns = array();
    $new_columns['cb'] = $columns['cb'];
    $new_columns['title'] = $columns['title'];
    $new_columns['category'] = 'Category';
    $new_columns['event_date'] = 'Start Date';
    $new_columns['end_date'] = 'End Date';
    $new_columns['location'] = 'Location';
    $new_columns['organizer'] = 'Organizer';
    $new_columns['date'] = $columns['date'];
    return $new_columns;
}
add_filter('manage_gurukul_event_posts_columns', 'gurukul_events_columns');

// Fill the columns with data
function gurukul_events_column_content($column, $post_id) {
    switch ($column) {
        case 'category':
            echo get_post_meta($post_id, '_category', true);
            break;
        case 'event_date':
            $date = get_post_meta($post_id, '_event_date', true);
            $time = get_post_meta($post_id, '_event_time', true);
            echo $date ? date('F j, Y', strtotime($date)) . ' at ' . date('g:i a', strtotime($time)) : '';
            break;
        case 'end_date':
            $date = get_post_meta($post_id, '_end_date', true);
            $time = get_post_meta($post_id, '_end_time', true);
            echo $date ? date('F j, Y', strtotime($date)) . ' at ' . date('g:i a', strtotime($time)) : '';
            break;
        case 'location':
            echo get_post_meta($post_id, '_location', true);
            break;
        case 'organizer':
            echo get_post_meta($post_id, '_organizer', true);
            break;
    }
}
add_action('manage_gurukul_event_posts_custom_column', 'gurukul_events_column_content', 10, 2);

// Make columns sortable
function gurukul_events_sortable_columns($columns) {
    $columns['category'] = 'category';
    $columns['event_date'] = 'event_date';
    $columns['location'] = 'location';
    $columns['organizer'] = 'organizer';
    return $columns;
}
add_filter('manage_edit-gurukul_event_sortable_columns', 'gurukul_events_sortable_columns');

// Activation Hook
function gurukul_events_activate() {
    gurukul_events_post_type();
    flush_rewrite_rules();
}
register_activation_hook(__FILE__, 'gurukul_events_activate');

// Initialize Plugin
function gurukul_events_init() {
    add_action('init', 'gurukul_events_post_type');
    require_once GURUKUL_EVENTS_PATH . 'includes/meta-boxes.php';
    require_once GURUKUL_EVENTS_PATH . 'includes/registration.php';
    require_once GURUKUL_EVENTS_PATH . 'includes/admin-functions.php';
    require_once GURUKUL_EVENTS_PATH . 'includes/shortcodes.php';
    require_once GURUKUL_EVENTS_PATH . 'includes/settings.php';
}
add_action('plugins_loaded', 'gurukul_events_init');

// Enqueue styles
function gurukul_events_enqueue_styles() {
    wp_enqueue_style(
        'gurukul-events-styles',
        GURUKUL_EVENTS_URL . 'assets/css/gurukul-events.css',
        array(),
        '1.0.0'
    );
    wp_enqueue_style(
        'gurukul-events-grid',
        GURUKUL_EVENTS_URL . 'assets/css/gurukul-events-grid.css',
        array(),
        '1.0.0'
    );
}
add_action('wp_enqueue_scripts', 'gurukul_events_enqueue_styles');
add_action('admin_enqueue_scripts', 'gurukul_events_enqueue_styles');

// Add template loader
function gurukul_events_template_loader($template) {
    if (is_singular('gurukul_event')) {
        $custom_template = GURUKUL_EVENTS_PATH . 'templates/single-gurukul_event.php';
        if (file_exists($custom_template)) {
            return $custom_template;
        }
    }
    return $template;
}
add_filter('template_include', 'gurukul_events_template_loader');