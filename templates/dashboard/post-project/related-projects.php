<?php
global $workreap_settings, $current_user;

if ( !class_exists('WooCommerce') ) {
    return;
}

$post_id = !empty($post_id) ? intval($post_id) : "";
$step_id = !empty($step) ? intval($step) : "";
$pg_page = get_query_var('page') ? get_query_var('page') : 1; //rewrite the global var
$pg_paged = get_query_var('paged') ? get_query_var('paged') : 1;
$per_page = !empty($per_page) ? $per_page : 10;

// --- GET BUYER REQUEST SKILLS ---
// Let's assume you store the buyer's requested skills as post meta array:
$buyer_requested_skills = get_post_meta($post_id, 'requested_skills', true); // Example: array('Journal Article','Presentation')
if(!is_array($buyer_requested_skills)) $buyer_requested_skills = array();

// --- FIND FREELANCERS MATCHING ANY OF THESE SKILLS ---
$query_args = array(
    'posts_per_page'      => $per_page * 3, // get more, filter down after
    'paged'               => $pg_paged,
    'post_type'           => 'freelancers',
    'post_status'         => 'publish',
    'ignore_sticky_posts' => 1,
);

// We'll filter below
$freelancer_query = new WP_Query($query_args);

$matching_freelancers = [];
if ($freelancer_query->have_posts() && !empty($buyer_requested_skills)) {
    while ($freelancer_query->have_posts()) {
        $freelancer_query->the_post();
        $freelancer_id = get_the_ID();
        $user_id = get_post_field('post_author', $freelancer_id);

        // Get the freelancer's skills from usermeta (array)
        $freelancer_skills = get_user_meta($user_id, 'document_type', true);
        if(!is_array($freelancer_skills)) $freelancer_skills = [];

        // Check if any of the buyer's requested skills match freelancer's
        $skill_match = array_intersect($buyer_requested_skills, $freelancer_skills);

        if (!empty($skill_match)) {
            $matching_freelancers[] = [
                'post_id' => $freelancer_id,
                'user_id' => $user_id,
                'skills'  => $freelancer_skills,
                'match'   => $skill_match,
            ];
            if (count($matching_freelancers) >= $per_page) break;
        }
    }
    wp_reset_postdata();
}

// Pagination (basic, you can improve)
$total_posts = count($matching_freelancers);
?>
<div class="row">
    <?php do_action( 'workreap_project_sidebar', $step_id, $post_id );?>
    <div class="col-xl-9 col-lg-8">
        <div class="wr-maintitle">
            <h4><?php esc_html_e('Recommended freelancers','workreap');?></h4>
        </div>
        <div class="wr-freelancers-list">
            <?php
            if ($total_posts > 0) {
                foreach ($matching_freelancers as $freelancer) {
                    $freelancer_id = $freelancer['post_id'];
                    $freelancer_name = workreap_get_username($freelancer_id);
                    $wr_post_meta = get_post_meta($freelancer_id, 'wr_post_meta', true);
                    $freelancer_tagline = !empty($wr_post_meta['tagline']) ? $wr_post_meta['tagline'] : '';
                    $matched_skills = implode(', ', $freelancer['match']);
                    ?>
                    <div class="wr-bestservice">
                        <div class="wr-bestservice__content wr-bestservicedetail">
                            <div class="wr-bestservicedetail__user">
                                <div class="wr-asideprostatus">
                                    <?php do_action('workreap_profile_image', $freelancer_id);?>
                                    <div class="wr-bestservicedetail__title">
                                        <?php if( !empty($freelancer_name) ){?>
                                            <h6><a href="<?php echo esc_url( get_permalink($freelancer_id) ); ?>"><?php echo esc_html($freelancer_name); ?></a></h6>
                                        <?php } ?>
                                        <?php if( !empty($freelancer_tagline) ){?>
                                            <h5><?php echo esc_html($freelancer_tagline); ?></h5>
                                        <?php } ?>
                                        <div>
                                            <small><?php echo esc_html__('Matched Skills: ', 'workreap') . esc_html($matched_skills); ?></small>
                                        </div>
                                        <ul class="wr-rateviews">
                                            <?php do_action('workreap_get_freelancer_rating_count', $freelancer_id); ?>
                                            <?php do_action('workreap_get_freelancer_views', $freelancer_id); ?>
                                            <?php do_action('workreap_save_freelancer_html', $current_user->ID, $freelancer_id, '_saved_freelancers', '', 'freelancers'); ?>
                                        </ul>
                                    </div>
                                </div>
                                <?php do_action('workreap_freelancer_invitation', $post_id, $freelancer_id); ?>
                            </div>
                        </div>
                    </div>
                <?php
                }
            } else {
                do_action('workreap_empty_records_html', 'wr-empty-saved-items', esc_html__('No recommended freelancers found.', 'workreap'));
            }
            ?>
        </div>
    </div>
</div>