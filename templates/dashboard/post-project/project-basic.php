<?php
/**
*  Project basic (Customized for Title Only - Final Version)
*
* @package     Workreap
* @author      Amentotech <info@amentotech.com>
* @link        https://codecanyon.net/user/amentotech/portfolio
* @version     1.0
* @since       1.0
*/
global $workreap_settings, $current_user;
if (!class_exists('WooCommerce')) {
    return;
}

$post_id = !empty($post_id) ? intval($post_id) : "";
$step_id = !empty($step) ? intval($step) : "";

$title = !empty($product) ? $product->get_name() : '';
?>

<div class="row">
    <?php do_action('workreap_project_sidebar', $step_id, $post_id); ?>
    <div class="col-lg-8 col-xl-9">
        <div class="wr-project-wrapper wr-aboutprojectstep">
            <div class="wr-project-box">
                <div class="wr-maintitle">
                    <h4><?php esc_html_e('Tell us about your project', 'workreap'); ?></h4>
                </div>
                <!-- The form tag is required for the theme's JS to pick up the data -->
                <form class="wr-themeform wr-project-form" id="step2-custom-form">
                    <fieldset>
                        <div class="wr-themeform__wrap">
                            <div class="form-group">
                                <label class="wr-label" for="project_title_input"><?php esc_html_e('Add your project title', 'workreap'); ?></label>
                                <div class="wr-placeholderholder">
                                    <input type="text" name="title" id="project_title_input" class="form-control wr-themeinput" value="<?php echo esc_attr($title); ?>" placeholder="<?php esc_attr_e('Enter your project title', 'workreap'); ?>">
                                </div>
                            </div>
                        </div>
                    </fieldset>
                </form>
            </div>
            <div class="wr-project-box">
                <div class="wr-projectbtns">
                    <!-- This button uses the theme's original class 'wr-save-project' -->
                    <a href="javascript:void(0);" class="wr-btn-solid-lg-lefticon wr-save-project" data-step_id="2" data-project_id="<?php echo intval($post_id); ?>">
                        <?php esc_html_e('Save & continue', 'workreap'); ?>
                        <i class="wr-icon-chevron-right"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// This script works with the theme's existing AJAX handler
jQuery(document).ready(function($) {
    'use strict';

    // Hijack the theme's AJAX call to add our custom flag
    if (typeof wp.ajax !== 'undefined' && typeof wp.ajax.callbacks !== 'undefined' && wp.ajax.callbacks.hasOwnProperty('workreap_save_project_callback')) {
        
        // Find the original callback
        const original_callback = wp.ajax.callbacks.workreap_save_project_callback;

        // Override it with our custom version
        wp.ajax.callbacks.workreap_save_project_callback = {
            ...original_callback,
            data: function(data) {
                // Get the original data from the form
                let originalData = original_callback.data.call(this, data);
                
                // Add our custom flag to bypass validation
                originalData.save_title_only = 'true';
                
                return originalData;
            }
        };
    }
});
</script>