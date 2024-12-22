<?php
if (!defined('ABSPATH')) {
    exit;
}

class Gurukul_Events_Shortcode {
    
    public function __construct() {
        add_shortcode('gurukul_events', array($this, 'render_events'));
        add_action('wp_enqueue_scripts', array($this, 'enqueue_styles'));
    }

    public function enqueue_styles() {
        wp_enqueue_style(
            'gurukul-events-grid',
            GURUKUL_EVENTS_URL . 'assets/css/gurukul-events-grid.css',
            array(),
            '1.0.0'
        );
    }

    public function render_events($atts) {
        $atts = shortcode_atts(array(
            'category' => '',     // Category filter
            'limit' => 6,         // Number of events to show
            'orderby' => 'date',  // date, title, event_date
            'order' => 'ASC',     // ASC or DESC
            'view' => 'grid',     // grid or list
        ), $atts);

        $args = array(
            'post_type' => 'gurukul_event',
            'posts_per_page' => $atts['limit'],
            'orderby' => 'meta_value',
            'meta_key' => '_event_date',
            'order' => $atts['order'],
            'meta_query' => array(
                array(
                    'key' => '_event_date',
                    'value' => date('Y-m-d'),
                    'compare' => '>=',
                    'type' => 'DATE'
                )
            )
        );

        if (!empty($atts['category'])) {
            $args['meta_query'][] = array(
                'key' => '_category',
                'value' => $atts['category'],
                'compare' => '='
            );
        }

        $events = new WP_Query($args);
        
        ob_start();
        
        if ($events->have_posts()) {
            echo '<div class="gurukul-events-grid ' . esc_attr($atts['view']) . '-view">';
            
            while ($events->have_posts()) {
                $events->the_post();
                $event_date = get_post_meta(get_the_ID(), '_event_date', true);
                $event_time = get_post_meta(get_the_ID(), '_event_time', true);
                $location = get_post_meta(get_the_ID(), '_location', true);
                $category = get_post_meta(get_the_ID(), '_category', true);
                $is_free = get_post_meta(get_the_ID(), '_is_free', true);
                $dakshina = get_post_meta(get_the_ID(), '_dakshina_amount', true);
                
                echo '<div class="event-item">';
                
                if (has_post_thumbnail()) {
                    echo '<div class="event-image">';
                    echo '<a href="' . get_permalink() . '">';
                    the_post_thumbnail('medium');
                    echo '</a>';
                    echo '</div>';
                }
                
                echo '<div class="event-content">';
                echo '<div class="event-category" data-category="' . esc_attr($category) . '">' . esc_html($category) . '</div>';
                
                echo '<h3 class="event-title">';
                echo '<a href="' . get_permalink() . '">' . get_the_title() . '</a>';
                echo '</h3>';
                
                if ($event_date) {
                    echo '<div class="event-date">';
                    echo '<i class="dashicons dashicons-calendar-alt"></i>';
                    echo date('F j, Y', strtotime($event_date));
                    if ($event_time) {
                        echo ' at ' . date('g:i a', strtotime($event_time));
                    }
                    echo '</div>';
                }
                
                if ($location) {
                    echo '<div class="event-location">';
                    echo '<i class="dashicons dashicons-location"></i>';
                    echo esc_html($location);
                    echo '</div>';
                }
                
                echo '<div class="event-meta">';
                echo '<span class="event-price">';
                echo ($is_free == 'yes') ? 'Free Event' : '₹' . number_format($dakshina) . ' Dakshina';
                echo '</span>';
                echo '<a href="' . get_permalink() . '" class="event-link">View Details</a>';
                echo '</div>';
                
                echo '</div>'; // end event-content
                echo '</div>'; // end event-item
            }
            
            echo '</div>';
            
            if ($atts['view'] === 'list' && $events->max_num_pages > 1) {
                echo '<div class="event-pagination">';
                echo paginate_links(array(
                    'total' => $events->max_num_pages,
                ));
                echo '</div>';
            }
            
        } else {
            echo '<p class="no-events">No upcoming events found.</p>';
        }
        
        wp_reset_postdata();
        
        return ob_get_clean();
    }
}

new Gurukul_Events_Shortcode(); 