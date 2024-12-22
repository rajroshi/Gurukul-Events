<?php
function gurukul_register_event_submission() {
    if (!isset($_POST['event_registration_nonce']) || 
        !wp_verify_nonce($_POST['event_registration_nonce'], 'submit_event_registration')) {
        return;
    }

    $event_id = intval($_POST['event_id']);
    $user_id = get_current_user_id();
    
    if (!$user_id) {
        wp_die('Please login to register for events.');
    }

    $registration = array(
        'post_title' => 'Registration for ' . get_the_title($event_id),
        'post_type' => 'event_registration',
        'post_status' => 'pending',
        'post_author' => $user_id
    );

    $registration_id = wp_insert_post($registration);

    if ($registration_id) {
        update_post_meta($registration_id, '_event_id', $event_id);
        update_post_meta($registration_id, '_user_id', $user_id);
        update_post_meta($registration_id, '_status', 'pending');
        
        // Send email notification to admin
        do_action('gurukul_new_registration_notification', $registration_id);
    }
}
add_action('init', 'gurukul_register_event_submission'); 