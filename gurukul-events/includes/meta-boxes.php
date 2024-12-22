<?php
if (!defined('ABSPATH')) {
    exit;
}

class Gurukul_Event_Meta_Box {
    
    private $categories = array(
        'Anusthan' => 'Anusthan',
        'Deeksha' => 'Deeksha',
        'Teaching' => 'Teaching',
        'Sadhana Shivir' => 'Sadhana Shivir',
        'Online Workshop' => 'Online Workshop',
        'Meditation' => 'Meditation',
        'Satsang' => 'Satsang',
        'Yagya' => 'Yagya',
        'Retreat' => 'Retreat',
        'Special Event' => 'Special Event'
    );

    public function __construct() {
        add_action('add_meta_boxes', array($this, 'add_meta_box'));
        add_action('save_post_gurukul_event', array($this, 'save_meta_box'));
    }

    public function add_meta_box() {
        add_meta_box(
            'gurukul_event_details',
            'Event Details',
            array($this, 'render_meta_box'),
            'gurukul_event',
            'normal',
            'high'
        );
    }

    public function render_meta_box($post) {
        wp_nonce_field('gurukul_event_save', 'gurukul_event_nonce');

        // Get saved values
        $category = get_post_meta($post->ID, '_category', true);
        $event_date = get_post_meta($post->ID, '_event_date', true);
        $event_time = get_post_meta($post->ID, '_event_time', true);
        $end_date = get_post_meta($post->ID, '_end_date', true);
        $end_time = get_post_meta($post->ID, '_end_time', true);
        $location = get_post_meta($post->ID, '_location', true);
        $organizer = get_post_meta($post->ID, '_organizer', true);
        $dakshina_amount = get_post_meta($post->ID, '_dakshina_amount', true);
        $is_free = get_post_meta($post->ID, '_is_free', true);
        
        // Get current categories
        $current_categories = get_the_terms($post->ID, 'event_category');
        $current_cat_ids = ($current_categories) ? wp_list_pluck($current_categories, 'term_id') : array();

        // Build form fields
        $this->render_category_field($category);
        $this->render_datetime_fields($event_date, $event_time, $end_date, $end_time);
        $this->render_location_field($location);
        $this->render_organizer_field($organizer);
        $this->render_is_free_field($is_free);
        $this->render_dakshina_field($dakshina_amount, $is_free);
        $this->render_categories_field($current_cat_ids);
        $this->render_styles();
        $this->render_scripts();
    }

    private function render_category_field($category) {
        $output = '<div class="gurukul-event-meta">';
        $output .= '<p><label><strong>Category:</strong></label>';
        $output .= '<select name="category" class="widefat">';
        
        foreach ($this->categories as $value => $label) {
            $output .= sprintf(
                '<option value="%s"%s>%s</option>',
                esc_attr($value),
                selected($category, $value, false),
                esc_html($label)
            );
        }
        
        $output .= '</select></p>';
        echo $output;
    }

    private function render_datetime_fields($event_date, $event_time, $end_date, $end_time) {
        echo '<div class="event-datetime-fields">';
        
        // Start Date & Time
        echo '<p><label><strong>Start Date & Time:</strong></label>';
        printf(
            '<input type="date" name="event_date" class="widefat" value="%s" style="margin-bottom: 10px;">
            <input type="time" name="event_time" class="widefat" value="%s">',
            esc_attr($event_date),
            esc_attr($event_time)
        );
        echo '</p>';
        
        // End Date & Time
        echo '<p><label><strong>End Date & Time:</strong></label>';
        printf(
            '<input type="date" name="end_date" class="widefat" value="%s" style="margin-bottom: 10px;">
            <input type="time" name="end_time" class="widefat" value="%s">',
            esc_attr($end_date),
            esc_attr($end_time)
        );
        echo '</p>';
    }

    private function render_location_field($location) {
        printf(
            '<p><label><strong>Location:</strong></label>
            <input type="text" name="location" class="widefat" value="%s" placeholder="Enter event location"></p>',
            esc_attr($location)
        );
    }

    private function render_organizer_field($organizer) {
        printf(
            '<p><label><strong>Organizer:</strong></label>
            <input type="text" name="organizer" class="widefat" value="%s" placeholder="Enter organizer name"></p>',
            esc_attr($organizer)
        );
    }

    private function render_is_free_field($is_free) {
        printf(
            '<p><label class="is-free-label">
            <input type="checkbox" name="is_free" value="yes"%s>
            <strong>Is Free Event?</strong></label></p>',
            checked($is_free, 'yes', false)
        );
    }

    private function render_dakshina_field($dakshina_amount, $is_free) {
        printf(
            '<p class="dakshina-field"%s>
            <label><strong>Dakshina Amount (₹):</strong></label>
            <input type="number" name="dakshina_amount" class="widefat" value="%s"></p>',
            $is_free == 'yes' ? ' style="display:none;"' : '',
            esc_attr($dakshina_amount)
        );
    }

    private function render_categories_field($current_cat_ids) {
        $output = '<p><label><strong>Additional Categories:</strong></label>';
        $output .= '<div class="event-categories-box">';
        
        $categories = get_terms(array(
            'taxonomy' => 'event_category',
            'hide_empty' => false,
        ));
        
        if (!empty($categories) && !is_wp_error($categories)) {
            foreach ($categories as $category) {
                $output .= sprintf(
                    '<label class="category-checkbox">
                    <input type="checkbox" name="event_categories[]" value="%s"%s> %s</label>',
                    esc_attr($category->term_id),
                    checked(in_array($category->term_id, $current_cat_ids), true, false),
                    esc_html($category->name)
                );
            }
        }
        
        $output .= '</div></p></div>';
        echo $output;
    }

    private function render_styles() {
        echo '<style>
            .gurukul-event-meta p { margin: 1.5em 0; }
            .event-categories-box {
                border: 1px solid #ddd;
                padding: 10px;
                max-height: 150px;
                overflow-y: auto;
                background: #fff;
            }
            .category-checkbox { display: block; margin: 5px 0; }
            .is-free-label { font-weight: normal; }
            .widefat { width: 100%; }
            .event-datetime-fields input[type="date"],
            .event-datetime-fields input[type="time"] {
                width: 100%;
                margin-bottom: 5px;
            }
            .event-category {
                display: inline-block;
                padding: 5px 15px;
                border-radius: 20px;
                font-size: 14px;
                margin-bottom: 15px;
            }
            .event-category[data-category="Anusthan"] {
                background: #f0f7ff;
                color: #0073aa;
            }
            .event-category[data-category="Deeksha"] {
                background: #fff0f4;
                color: #d63638;
            }
            .event-category[data-category="Teaching"] {
                background: #f0fff4;
                color: #00a32a;
            }
            .event-category[data-category="Sadhana Shivir"] {
                background: #fff8e5;
                color: #996800;
            }
            .event-category[data-category="Online Workshop"] {
                background: #f6f7ff;
                color: #3858e9;
            }
            .event-category[data-category="Meditation"] {
                background: #f0f7ff;
                color: #2271b1;
            }
            .event-category[data-category="Satsang"] {
                background: #fff0f7;
                color: #8c1749;
            }
            .event-category[data-category="Yagya"] {
                background: #fff2e5;
                color: #cc4a1a;
            }
            .event-category[data-category="Retreat"] {
                background: #f0fff8;
                color: #008a20;
            }
            .event-category[data-category="Special Event"] {
                background: #f6e7ff;
                color: #7e3bd0;
            }
        </style>';
    }

    private function render_scripts() {
        echo '<script>
            jQuery(document).ready(function($) {
                $("input[name=\'is_free\']").change(function() {
                    if($(this).is(":checked")) {
                        $(".dakshina-field").hide();
                    } else {
                        $(".dakshina-field").show();
                    }
                });
            });
        </script>';
    }

    public function save_meta_box($post_id) {
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
        if (!current_user_can('edit_post', $post_id)) return;
        if (!isset($_POST['gurukul_event_nonce']) || !wp_verify_nonce($_POST['gurukul_event_nonce'], 'gurukul_event_save')) return;

        // Save category
        if (isset($_POST['category'])) {
            update_post_meta($post_id, '_category', sanitize_text_field($_POST['category']));
        }

        // Save dates and times
        if (isset($_POST['event_date'])) {
            update_post_meta($post_id, '_event_date', sanitize_text_field($_POST['event_date']));
        }
        if (isset($_POST['event_time'])) {
            update_post_meta($post_id, '_event_time', sanitize_text_field($_POST['event_time']));
        }
        if (isset($_POST['end_date'])) {
            update_post_meta($post_id, '_end_date', sanitize_text_field($_POST['end_date']));
        }
        if (isset($_POST['end_time'])) {
            update_post_meta($post_id, '_end_time', sanitize_text_field($_POST['end_time']));
        }

        // Save location and organizer
        if (isset($_POST['location'])) {
            update_post_meta($post_id, '_location', sanitize_text_field($_POST['location']));
        }
        if (isset($_POST['organizer'])) {
            update_post_meta($post_id, '_organizer', sanitize_text_field($_POST['organizer']));
        }

        // Save is_free
        update_post_meta($post_id, '_is_free', isset($_POST['is_free']) ? 'yes' : 'no');

        // Save dakshina amount
        if (isset($_POST['dakshina_amount'])) {
            update_post_meta($post_id, '_dakshina_amount', absint($_POST['dakshina_amount']));
        }

        // Save categories
        if (isset($_POST['event_categories'])) {
            $categories = array_map('intval', $_POST['event_categories']);
            wp_set_object_terms($post_id, $categories, 'event_category');
        } else {
            wp_set_object_terms($post_id, array(), 'event_category');
        }
    }
}

new Gurukul_Event_Meta_Box();