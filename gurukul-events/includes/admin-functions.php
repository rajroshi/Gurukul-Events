<?php
function gurukul_update_registration_status() {
    if (!current_user_can('manage_options')) {
        return;
    }

    $registration_id = intval($_POST['registration_id']);
    $new_status = sanitize_text_field($_POST['status']);
    
    update_post_meta($registration_id, '_status', $new_status);
    
    // Send email notification to user
    $user_id = get_post_meta($registration_id, '_user_id', true);
    $user = get_userdata($user_id);
    
    $message = $new_status === 'approved' 
        ? 'Your registration has been approved.' 
        : 'Your registration has been declined.';
        
    wp_mail($user->user_email, 'Event Registration Update', $message);
}
add_action('wp_ajax_update_registration_status', 'gurukul_update_registration_status'); 