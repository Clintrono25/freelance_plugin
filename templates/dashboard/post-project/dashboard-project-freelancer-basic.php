<?php
global $workreap_settings;

$employer_dispute_issues  = !empty($workreap_settings['employer_project_dispute_issues']) ? $workreap_settings['employer_project_dispute_issues'] : array();
$tpl_terms_conditions     = !empty($workreap_settings['tpl_terms_conditions']) ? get_the_permalink($workreap_settings['tpl_terms_conditions']) : '';
$tpl_privacy              = !empty($workreap_settings['tpl_privacy']) ? get_the_permalink($workreap_settings['tpl_privacy']) : '';

$term_link    = $tpl_terms_conditions ? '<a target="_blank" href="'.$tpl_terms_conditions.'">'.get_the_title($workreap_settings['tpl_terms_conditions']).'</a>' : '';
$privacy_link = $tpl_privacy ? '<a target="_blank" href="'.$tpl_privacy.'">'.get_the_title($workreap_settings['tpl_privacy']).'</a>' : '';

$proposal_id     = !empty($args['proposal_id']) ? intval($args['proposal_id']) : 0;
$project_id      = !empty($args['project_id']) ? intval($args['project_id']) : 0;
$project_title   = !empty($args['project_title']) ? esc_attr($args['project_title']) : '';
$freelancer_id   = !empty($args['freelancer_id']) ? intval($args['freelancer_id']) : 0;
$proposal_status = !empty($args['proposal_status']) ? esc_attr($args['proposal_status']) : '';
$complete_option = !empty($args['complete_option']) ? esc_attr($args['complete_option']) : '';
$proposal_type   = get_post_meta($proposal_id, 'proposal_type', true);

$profile_id      = workreap_get_linked_profile_id($freelancer_id, '','freelancers');
$user_name       = workreap_get_username($profile_id);
$avatar          = apply_filters('workreap_avatar_fallback', workreap_get_user_avatar(['width' => 50, 'height' => 50], $profile_id), ['width' => 50, 'height' => 50]);
$proposal_price  = isset($args['proposal_meta']['price']) ? $args['proposal_meta']['price'] : 0;

do_action('workreap_project_completed_form', $args);
?>

<style>
@media (max-width: 768px) {
    .wr-projectsstatus_wrapper {
        flex-direction: column !important;
        gap: 1.5rem !important;
        padding: 20px 15px !important;
        text-align: center;
    }

    .wr-projectsstatus_wrapper > div {
        flex: 1 1 100% !important;
        max-width: 100% !important;
    }

    .wr-projectsstatus_info {
        display: flex;
        flex-direction: column;
        align-items: center;
    }

    .wr-projectsstatus_info figure {
        margin: 0 auto;
    }

    .wr-projectsstatus_name {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        text-align: center;
    }

    .wr-projectsstatus_name .wr-project-tag {
        margin-bottom: 5px;
    }

    .wr-projectsstatus_actions button {
        width: 100% !important;
    }
    
    .wr-projectsstatus_budget {
  flex: 1;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  text-align: center;
}

.wr-projectsstatus_budget strong > div:first-child {
  font-size: 1.25rem;
  font-weight: 600;
}

.wr-projectsstatus_budget strong > div:last-child {
  font-size: 0.9rem;
  color: #999;
}

}
</style>


<div class="wr-projectsstatus_wrapper" style="display: flex; flex-wrap: wrap; justify-content: space-between; gap: 2rem; padding: 5px 30px 10px 30px;">

    <!-- Column 1: Freelancer Info -->
    <div class="wr-projectsstatus_info" style="flex: 1;">
        <?php if (!empty($avatar)) { ?>
            <figure class="wr-projectsstatus_img" style="margin: 0 auto;">
                <img src="<?php echo esc_url($avatar); ?>" alt="<?php echo esc_attr($user_name); ?>">
            </figure>
        <?php } ?>
        <div class="wr-projectsstatus_name">
            <?php do_action('workreap_freelancer_proposal_status_tag', $proposal_id); ?>
            <?php if (!empty($user_name)) { ?>
                <h5 style="margin-top: 10px;"><?php echo esc_html($user_name); ?></h5>
            <?php } ?>
        </div>
    </div>

    <!-- Column 2: Budget Info -->
    <div class="wr-projectsstatus_budget" style="flex: 1; text-align: center;">
        <strong>
            <div style="font-size: 1.25rem; font-weight: 600; text-align: center;">
                <?php
                if (empty($proposal_type) || $proposal_type === 'fixed') {
                    workreap_price_format($proposal_price);
                } else {
                    do_action('workreap_proposal_listing_price', $proposal_id);
                }
                ?>
            </div>
            <?php do_action('workreap_project_estimation_html', $project_id); ?>
            <div style="font-size: 0.9rem; color: #999; text-align: center;">
                <?php esc_html_e('Total project budget', 'workreap'); ?>
            </div>
        </strong>
    </div>

    <!-- Column 3: Action Buttons -->
    <div class="wr-projectsstatus_actions">
        <?php if (!empty($proposal_status) && in_array($proposal_status, ['hired', 'cancelled'])) { ?>

            <?php if ($proposal_status === 'hired' && !empty($complete_option) && $complete_option === 'yes') { ?>
                <button class="wr-btn wr-btn-primary wr_proposal_completed" 
                    style="margin-bottom: 10px;"
                    data-proposal_id="<?php echo intval($proposal_id); ?>" 
                    data-title="<?php echo esc_attr($project_title); ?>">
                    <?php esc_html_e('Accept Order', 'workreap'); ?>
                </button>
            <?php } ?>

            <?php if ($proposal_status === 'hired') { ?>
                <button id="taskrefundrequest" class="wr-btn" 
                    style="background: none; border: 1px solid #ccc; color: #888; box-shadow: none;">
                    <?php esc_html_e('Cancel Order', 'workreap'); ?>
                </button>
            <?php } ?>

        <?php } ?>
    </div>

</div>



<!-- Modal Template: Refund Request -->
<script type="text/template" id="tmpl-load-task-refund-request">
    <div class="modal-dialog wr-modaldialog" role="document">
        <div class="modal-content">
            <div class="wr-popuptitle">
                <h4><?php esc_html_e('Create refund request', 'workreap') ?></h4>
                <a href="javascript:void(0);" class="close"><i class="wr-icon-x" data-bs-dismiss="modal"></i></a>
            </div>
            <div class="modal-body">
                <div class="wr-popupbodytitle">
                    <?php esc_html_e('Choose issue you want to highlight', 'workreap') ?>
                </div>
                <form name="refund-request" id="project-refund-request">
                    <input type="hidden" name="proposal_id" value="<?php echo intval($proposal_id); ?>">
                    <div class="wr-disputelist">
                        <ul class="wr-radiolist">
                            <?php if (!empty($employer_dispute_issues)) {
                                foreach ($employer_dispute_issues as $key => $issue) { ?>
                                    <li>
                                        <div class="wr-radio">
                                            <input type="radio" id="f-option-<?php echo esc_attr($key); ?>" value="<?php echo esc_attr($issue); ?>" name="dispute_issue">
                                            <label for="f-option-<?php echo esc_attr($key); ?>"><?php echo esc_html($issue); ?></label>
                                        </div>
                                    </li>
                                <?php }
                            } ?>
                        </ul>
                    </div>
                    <div class="wr-popupbodytitle">
                        <h5><?php esc_html_e('Add dispute details', 'workreap'); ?></h5>
                    </div>
                    <textarea class="form-control" placeholder="<?php esc_attr_e('Enter dispute details', 'workreap'); ?>" id="dispute-details" name="dispute-details"></textarea>
                    <div class="wr-popupbtnarea">
                        <div class="wr-checkterm">
                            <div class="wr-checkbox">
                                <input id="check3" type="checkbox" name="dispute_terms">
                                <label for="check3">
                                    <span>
                                        <?php echo sprintf(esc_html__('By clicking you agree with our %s and %s', 'workreap'), $term_link, $privacy_link); ?>
                                    </span>
                                </label>
                            </div>
                        </div>
                        <a href="javascript:void(0);" id="projectrefundrequest-submit" class="wr-btn">
                            <?php esc_html_e('Submit', 'workreap'); ?>
                            <span class="rippleholder wr-jsripple"><em class="ripplecircle"></em></span>
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</script>

<?php
$scripts = '
jQuery(document).ready(function () {
    jQuery(".wr-projectsstatus_option > a").on("click",function() {
        jQuery(".wr-contract-list").slideToggle();
    });
});
';
wp_add_inline_script('workreap', $scripts, 'after');
?>
