<?php
/**
 * Plugin Name: Client Project Manager
 * Description: Adds a Client Project custom post type with custom fields and a shortcode.
 * Version: 1.0
 * Author: Obitope Eniola Nathaniel
 */

// Register Custom Post Type
function cpm_register_client_project_cpt() {
    register_post_type('client_project', [
        'labels' => [
            'name' => 'Client Projects',
            'singular_name' => 'Client Project',
        ],
        'public' => true,
        'has_archive' => true,
        'supports' => ['title', 'editor'],
        'show_in_rest' => true,
        'menu_icon' => 'dashicons-portfolio',
    ]);
}
add_action('init', 'cpm_register_client_project_cpt');

// Add Meta Boxes
function cpm_add_meta_boxes() {
    add_meta_box('cpm_project_fields', 'Project Details', 'cpm_render_fields', 'client_project');
}
add_action('add_meta_boxes', 'cpm_add_meta_boxes');

// Render Meta Fields
function cpm_render_fields($post) {
    $client_name = get_post_meta($post->ID, 'client_name', true);
    $status = get_post_meta($post->ID, 'status', true);
    $deadline = get_post_meta($post->ID, 'deadline', true);

    ?>
    <p>
        <label>Client Name:</label><br>
        <input type="text" name="client_name" value="<?php echo esc_attr($client_name); ?>" class="widefat">
    </p>
    <p>
        <label>Status:</label><br>
        <select name="status" class="widefat">
            <option value="Pending" <?php selected($status, 'Pending'); ?>>Pending</option>
            <option value="In Progress" <?php selected($status, 'In Progress'); ?>>In Progress</option>
            <option value="Completed" <?php selected($status, 'Completed'); ?>>Completed</option>
        </select>
    </p>
    <p>
        <label>Deadline:</label><br>
        <input type="date" name="deadline" value="<?php echo esc_attr($deadline); ?>" class="widefat">
    </p>
    <?php
}

// Save Meta Fields
function cpm_save_project_meta($post_id) {
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;

    if (isset($_POST['client_name'])) {
        update_post_meta($post_id, 'client_name', sanitize_text_field($_POST['client_name']));
    }
    if (isset($_POST['status'])) {
        update_post_meta($post_id, 'status', sanitize_text_field($_POST['status']));
    }
    if (isset($_POST['deadline'])) {
        update_post_meta($post_id, 'deadline', sanitize_text_field($_POST['deadline']));
    }
}
add_action('save_post', 'cpm_save_project_meta');

// Shortcode to Display Projects
function cpm_display_projects() {
    $projects = new WP_Query([
        'post_type' => 'client_project',
        'posts_per_page' => -1,
    ]);

    $output = '<div class="client-projects">';
    while ($projects->have_posts()) {
        $projects->the_post();
        $client = get_post_meta(get_the_ID(), 'client_name', true);
        $status = get_post_meta(get_the_ID(), 'status', true);
        $deadline = get_post_meta(get_the_ID(), 'deadline', true);

        $output .= '<div class="project" style="border:1px solid #ddd; padding:15px; margin-bottom:10px">';
        $output .= '<h3>' . get_the_title() . '</h3>';
        $output .= '<p><strong>Client:</strong> ' . esc_html($client) . '</p>';
        $output .= '<p><strong>Status:</strong> ' . esc_html($status) . '</p>';
        $output .= '<p><strong>Deadline:</strong> ' . esc_html($deadline) . '</p>';
        $output .= '<div>' . get_the_content() . '</div>';
        $output .= '</div>';
    }
    wp_reset_postdata();
    $output .= '</div>';

    return $output;
}
add_shortcode('client_projects', 'cpm_display_projects');
