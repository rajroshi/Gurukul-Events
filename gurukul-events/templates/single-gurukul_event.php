<?php
get_header();
?>

<div id="primary" class="content-area">
    <main id="main" class="site-main">
        <?php while (have_posts()) : the_post(); ?>
            <article id="post-<?php the_ID(); ?>" <?php post_class('gurukul-event'); ?>>
                <div class="event-container">
                    <header class="entry-header">
                        <div class="event-category" data-category="<?php echo esc_attr(get_post_meta(get_the_ID(), '_category', true)); ?>">
                            <?php echo esc_html(get_post_meta(get_the_ID(), '_category', true)); ?>
                        </div>
                        <?php the_title('<h1 class="entry-title">', '</h1>'); ?>
                        
                        <div class="event-meta-top">
                            <?php
                            $event_date = get_post_meta(get_the_ID(), '_event_date', true);
                            $event_time = get_post_meta(get_the_ID(), '_event_time', true);
                            $end_date = get_post_meta(get_the_ID(), '_end_date', true);
                            $end_time = get_post_meta(get_the_ID(), '_end_time', true);
                            $location = get_post_meta(get_the_ID(), '_location', true);
                            $organizer = get_post_meta(get_the_ID(), '_organizer', true);
                            
                            if ($event_date) : ?>
                                <span class="meta-item">
                                    <i class="dashicons dashicons-calendar-alt"></i>
                                    <?php 
                                    echo date('F j, Y', strtotime($event_date));
                                    if ($event_time) {
                                        echo ' at ' . date('g:i a', strtotime($event_time));
                                    }
                                    ?>
                                </span>
                            <?php endif; ?>

                            <?php if ($location) : ?>
                                <span class="meta-item">
                                    <i class="dashicons dashicons-location"></i>
                                    <?php echo esc_html($location); ?>
                                </span>
                            <?php endif; ?>

                            <?php if ($organizer) : ?>
                                <span class="meta-item">
                                    <i class="dashicons dashicons-groups"></i>
                                    <?php echo esc_html($organizer); ?>
                                </span>
                            <?php endif; ?>
                        </div>
                    </header>

                    <?php if (has_post_thumbnail()) : ?>
                        <div class="event-thumbnail">
                            <?php the_post_thumbnail('large'); ?>
                        </div>
                    <?php endif; ?>

                    <div class="event-content-wrapper">
                        <div class="event-details-sidebar">
                            <div class="event-details-box">
                                <h3>Event Details</h3>
                                <ul>
                                    <li>
                                        <strong>Start:</strong>
                                        <span>
                                            <?php 
                                            echo date('F j, Y', strtotime($event_date));
                                            if ($event_time) echo ' at ' . date('g:i a', strtotime($event_time));
                                            ?>
                                        </span>
                                    </li>
                                    
                                    <?php if ($end_date) : ?>
                                    <li>
                                        <strong>End:</strong>
                                        <span>
                                            <?php 
                                            echo date('F j, Y', strtotime($end_date));
                                            if ($end_time) echo ' at ' . date('g:i a', strtotime($end_time));
                                            ?>
                                        </span>
                                    </li>
                                    <?php endif; ?>
                                    
                                    <?php if ($location) : ?>
                                    <li>
                                        <strong>Location:</strong>
                                        <span><?php echo esc_html($location); ?></span>
                                    </li>
                                    <?php endif; ?>
                                    
                                    <?php if ($organizer) : ?>
                                    <li>
                                        <strong>Organizer:</strong>
                                        <span><?php echo esc_html($organizer); ?></span>
                                    </li>
                                    <?php endif; ?>
                                    
                                    <li>
                                        <strong>Contribution:</strong>
                                        <span>
                                            <?php 
                                            $is_free = get_post_meta(get_the_ID(), '_is_free', true);
                                            if ($is_free == 'yes') {
                                                echo 'Free Event';
                                            } else {
                                                $dakshina = get_post_meta(get_the_ID(), '_dakshina_amount', true);
                                                echo '₹' . number_format($dakshina) . ' Dakshina';
                                            }
                                            ?>
                                        </span>
                                    </li>
                                </ul>

                                <?php
                                $settings = get_option('gurukul_event_settings');
                                $registration_url = isset($settings['registration_page']) ? $settings['registration_page'] : '#';
                                $button_text = isset($settings['registration_button_text']) ? $settings['registration_button_text'] : 'Register Now';

                                // Replace placeholders in URL
                                $registration_url = str_replace(
                                    array('{event_id}', '{event_title}'),
                                    array(get_the_ID(), urlencode(get_the_title())),
                                    $registration_url
                                );

                                // Always show the register button
                                echo '<a href="' . esc_url($registration_url) . '" class="register-button">' . esc_html($button_text) . '</a>';
                                ?>
                            </div>
                        </div>

                        <div class="entry-content">
                            <?php the_content(); ?>
                        </div>
                    </div>
                </div>
            </article>
        <?php endwhile; ?>
    </main>
</div>

<style>
.gurukul-event {
    max-width: 1200px;
    margin: 0 auto;
    padding: 40px 20px;
}

.event-container {
    background: #fff;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    overflow: hidden;
}

.entry-header {
    padding: 30px 40px;
    border-bottom: 1px solid #eee;
}

.event-category {
    display: inline-block;
    background: #f0f7ff;
    color: #0073aa;
    padding: 5px 15px;
    border-radius: 20px;
    font-size: 14px;
    margin-bottom: 15px;
}

.entry-title {
    margin: 0 0 20px 0;
    font-size: 32px;
    color: #333;
}

.event-meta-top {
    display: flex;
    flex-wrap: wrap;
    gap: 20px;
    font-size: 14px;
    color: #666;
}

.meta-item {
    display: flex;
    align-items: center;
    gap: 5px;
}

.meta-item i {
    color: #0073aa;
}

.event-thumbnail img {
    width: 100%;
    height: auto;
    display: block;
}

.event-content-wrapper {
    display: grid;
    grid-template-columns: 1fr 300px;
    gap: 30px;
    padding: 40px;
}

.event-details-sidebar {
    order: 2;
}

.event-details-box {
    background: #f8f9fa;
    border-radius: 8px;
    padding: 25px;
}

.event-details-box h3 {
    margin: 0 0 20px 0;
    font-size: 18px;
    color: #333;
}

.event-details-box ul {
    list-style: none;
    margin: 0;
    padding: 0;
}

.event-details-box li {
    display: flex;
    flex-direction: column;
    margin-bottom: 15px;
    padding-bottom: 15px;
    border-bottom: 1px solid #eee;
}

.event-details-box li:last-child {
    border-bottom: none;
}

.event-details-box li strong {
    color: #666;
    font-size: 13px;
    margin-bottom: 5px;
}

.event-details-box li span {
    color: #333;
    font-size: 15px;
}

.register-button {
    display: block;
    background: #0073aa;
    color: white;
    text-align: center;
    padding: 12px 20px;
    border-radius: 4px;
    text-decoration: none;
    margin-top: 20px;
    font-weight: 500;
    transition: background-color 0.3s ease;
}

.register-button:hover {
    background: #005177;
    color: white;
}

.entry-content {
    order: 1;
    font-size: 16px;
    line-height: 1.6;
    color: #444;
}

@media (max-width: 768px) {
    .event-content-wrapper {
        grid-template-columns: 1fr;
    }
    
    .event-details-sidebar {
        order: 1;
    }
    
    .entry-content {
        order: 2;
    }
    
    .entry-header {
        padding: 20px;
    }
    
    .event-meta-top {
        flex-direction: column;
        gap: 10px;
    }
}
</style>

<?php
get_footer();