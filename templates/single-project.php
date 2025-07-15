<?php
/**
 * Workreap Child Theme - Clean Dynamic Project Requirements
 * Place this file in: wp-content/themes/workreap-child/workreap/single-product.php
 */

global $post, $thumbnail, $current_user;
do_action('workreap_post_views', $post->ID, 'workreap_project_views');
get_header();

while (have_posts()) : the_post();
    $product = wc_get_product($post->ID);
    $description = !empty($product) ? $product->get_description() : "";
    $user_id = get_post_field('post_author', $product->get_id());
    $user_id = !empty($user_id) ? intval($user_id) : 0;
    $profile_id = !empty($user_id) ? workreap_get_linked_profile_id($user_id, '', 'employers') : 0;
    $user_name = !empty($profile_id) ? workreap_get_username($profile_id) : '';
    $product_data = get_post_meta($product->get_id(), 'wr_project_meta', true);
    $downloadable = get_post_meta($product->get_id(), '_downloadable', true);
    $vid_url = !empty($product_data['video_url']) ? esc_url($product_data['video_url']) : '';

    // --- DYNAMIC ACF FIELDS ---
    $post_id = $product->get_id();
    $academic_style_guide = get_field('academic_style_guide', $post_id);
    $specify_other_academic_style_guide = get_field('specify_other_academic_style_guide', $post_id);
    $word_count = get_field('word_count', $post_id);
    $professional_level = get_field('professional_level', $post_id);
    $specify_other_professional_level = get_field('specify_other_professional_level', $post_id);
    $deadline_selection = get_field('deadline_selection', $post_id);
    $select_custom_deadline = get_field('select_custom_deadline', $post_id);

    // Rest of the ACF fields
    $document_type = get_field('document_type', $post_id);
    $specify_other_document_type = get_field('specify_other_document_type', $post_id);
    $service_type = get_field('service_type', $post_id);
    $specify_other_service_type = get_field('specify_other_service_type', $post_id);
    $industry_discipline = get_field('industry_discipline', $post_id);
    $academic_disciplines = get_field('academic_disciplines', $post_id);
    $professional_business_fields = get_field('professional_business_fields', $post_id);
    $other_industry_discipline = get_field('other_industry_discipline', $post_id);
    $specify_other_industry_discipline = get_field('specify_other_industry_discipline', $post_id);
    $document_upload = get_field('document_upload', $post_id);

?>
<section class="wr-main-section overflow-hidden wr-main-bg">
    <div class="container">
        <div class="row gy-4">
            <div class="col-lg-7 col-xl-8">
                <div class="wr-projectbox">
                    <div class="wr-project-box">
                        <div class="wr-servicedetailtitle">
                            <h3><?php echo esc_html($product->get_name()); ?></h3>
                            <ul class="wr-blogviewdates">
                                <?php do_action('workreap_posted_date_html', $product); ?>
                                <?php do_action('workreap_location_html', $product); ?>
                            </ul>
                        </div>
                    </div>
                    <div class="wr-project-box">
                        <?php if (!empty($vid_url)) { ?>
                            <div class="wr-project-holder">
                                <!-- (Keep your video embed code here if needed) -->
                            </div>
                        <?php } ?>
                        <?php if (!empty($description)) { ?>
                            <div class="wr-project-holder wr-project-description">
                                <div class="wr-project-title">
                                    <h4><?php esc_html_e('Job description', 'workreap'); ?></h4>
                                </div>
                                <div class="wr-jobdescription">
                                    <?php echo do_shortcode(nl2br($description)); ?>
                                </div>
                            </div>
                        <?php } ?>

                        <!-- Additional ACF fields shown after description -->
                       <!-- Full Project Preferences (after description, in main column) -->
<!--<div style="background:#ffffff;border:1px solid #e1e1e1;border-left:5px solid #0a7d49;border-radius:8px;padding:1.5rem;box-shadow:0 4px 12px rgba(0, 0, 0, 0.04);margin:2rem 0;">-->
<!--    <div style="display:flex;align-items:center;gap:0.5rem;margin-bottom:1rem;">-->
<!--        <i class="fas fa-cogs" style="font-size:1.1rem;color:#0a7d49;"></i>-->
<!--        <h4 style="font-size:1.25rem;font-weight:600;color:#0a7d49;margin:0;"><?php esc_html_e('Full Project Preferences', 'workreap'); ?></h4>-->
<!--    </div>-->

<!--    <ul style="list-style:none;padding:0;margin:0;font-size:1.05rem;color:#333;">-->
<!--        <?php if ($document_type): ?>-->
<!--            <li style="padding:0.6rem 0;border-bottom:1px solid #f0f0f0;">-->
<!--                <strong style="display:inline-block;min-width:160px;color:#555;"><?php esc_html_e('Document Type:', 'workreap'); ?></strong>-->
<!--                <?php-->
<!--                    echo esc_html(is_array($document_type) ? implode(', ', $document_type) : $document_type);-->
<!--                    if (is_array($document_type) && in_array('Other (specify)', $document_type) && $specify_other_document_type) {-->
<!--                        echo " <em>(" . esc_html($specify_other_document_type) . ")</em>";-->
<!--                    }-->
<!--                ?>-->
<!--            </li>-->
<!--        <?php endif; ?>-->

<!--        <?php if ($service_type): ?>-->
<!--            <li style="padding:0.6rem 0;border-bottom:1px solid #f0f0f0;">-->
<!--                <strong style="display:inline-block;min-width:160px;color:#555;"><?php esc_html_e('Service Type:', 'workreap'); ?></strong>-->
<!--                <?php-->
<!--                    echo esc_html(is_array($service_type) ? implode(', ', $service_type) : $service_type);-->
<!--                    if (is_array($service_type) && in_array('Other', $service_type) && $specify_other_service_type) {-->
<!--                        echo " <em>(" . esc_html($specify_other_service_type) . ")</em>";-->
<!--                    }-->
<!--                ?>-->
<!--            </li>-->
<!--        <?php endif; ?>-->

<!--        <?php if ($industry_discipline): ?>-->
<!--            <li style="padding:0.6rem 0;border-bottom:1px solid #f0f0f0;">-->
<!--                <strong style="display:inline-block;min-width:160px;color:#555;"><?php esc_html_e('Industry / Discipline:', 'workreap'); ?></strong>-->
<!--                <?php echo esc_html($industry_discipline); ?>-->
<!--                <?php if ($industry_discipline === 'Academic Disciplines' && $academic_disciplines): ?>-->
<!--                    <em>(<?php echo esc_html($academic_disciplines); ?>)</em>-->
<!--                <?php endif; ?>-->
<!--                <?php if ($industry_discipline === 'Professional & Business Fields' && $professional_business_fields): ?>-->
<!--                    <em>(<?php echo esc_html(is_array($professional_business_fields) ? implode(', ', $professional_business_fields) : $professional_business_fields); ?>)</em>-->
<!--                <?php endif; ?>-->
<!--                <?php if ($industry_discipline === 'Other' && $other_industry_discipline): ?>-->
<!--                    <em>(<?php echo esc_html($other_industry_discipline); ?>)</em>-->
<!--                    <?php if ($other_industry_discipline == 'Other' && $specify_other_industry_discipline): ?>-->
<!--                        <em>(<?php echo esc_html($specify_other_industry_discipline); ?>)</em>-->
<!--                    <?php endif; ?>-->
<!--                <?php endif; ?>-->
<!--            </li>-->
<!--        <?php endif; ?>-->

<!--        <?php if ($document_upload): ?>-->
<!--            <li style="padding:0.6rem 0;">-->
<!--                <strong style="display:inline-block;min-width:160px;color:#555;"><?php esc_html_e('Uploaded Document:', 'workreap'); ?></strong>-->
<!--                <?php-->
<!--                    if (is_array($document_upload) && isset($document_upload['url'])) {-->
<!--                        $doc_url = esc_url($document_upload['url']);-->
<!--                        $doc_name = esc_html($document_upload['filename']);-->
<!--                        echo "<a href='{$doc_url}' target='_blank' style='color:#0a7d49;text-decoration:underline;font-weight:500;'>{$doc_name}</a>";-->
<!--                    }-->
<!--                ?>-->
<!--            </li>-->
<!--        <?php endif; ?>-->
<!--    </ul>-->
<!--</div>-->
<style>
.wr-pref-card {
  background: #fff;
  border: 1px solid #e1e1e1;
  border-left: 5px solid #0a7d49;
  border-radius: 14px;
  padding: 1.7rem 1.2rem 1.2rem 1.2rem;
  box-shadow: 0 4px 18px rgba(44, 62, 80, 0.06);
  margin: 2.5rem 0 2rem 0;
  max-width: 680px;
}
.wr-pref-header {
  display: flex;
  align-items: center;
  gap: 0.7rem;
  margin-bottom: 1.4rem;
}
.wr-pref-header i {
  font-size: 1.35rem;
  color: #0a7d49;
}
.wr-pref-header h4 {
  font-size: 1.23rem;
  font-weight: 700;
  color: #0a7d49;
  margin: 0;
  letter-spacing: 0.01em;
}
.wr-pref-grid {
  display: grid;
  grid-template-columns: 1fr 2.3fr;
  row-gap: 0;
  column-gap: 0.2rem;
}
.wr-pref-row {
  display: contents; /* let grid-row/col work naturally */
}
.wr-pref-label {
  display: flex;
  align-items: center;
  font-weight: 600;
  color: #388e3c;
  background: #f7f9f9;
  border-radius: 7px 0 0 7px;
  padding: 0.82rem 0.7rem 0.82rem 0.7rem;
  font-size: 1.06rem;
  min-width: 140px;
  letter-spacing: 0.01em;
}
.wr-pref-value {
  background: #fafdff;
  color: #222;
  padding: 0.82rem 0.9rem;
  border-radius: 0 7px 7px 0;
  font-size: 1.08rem;
  word-break: break-word;
  border-bottom: 1px solid #f0f0f0;
}
.wr-pref-label i {
  margin-right: 0.55em;
  font-size: 1.03em;
  color: #6ecfa8;
}
.wr-pref-value:last-child,
.wr-pref-label:last-child {
  border-bottom: none;
}
.wr-pref-value a {
  color: #0a7d49;
  text-decoration: underline;
  font-weight: 500;
}
.wr-pref-value em {
  color: #888;
  font-size: 0.98em;
  margin-left: 0.1em;
}
@media (max-width: 600px) {
  .wr-pref-card { padding: 1.1rem 0.5rem 0.6rem 0.7rem; }
  .wr-pref-header { gap: 0.35rem; }
  .wr-pref-header h4 { font-size: 1.09rem; }
  .wr-pref-grid {
    grid-template-columns: 1fr;
    row-gap: 0;
  }
  .wr-pref-label, .wr-pref-value {
    border-radius: 7px 7px 0 0 !important;
    padding: 0.65rem 0.7rem 0.4rem 0.7rem;
    font-size: 1em;
    border-bottom: none;
  }
  .wr-pref-row { margin-bottom: 0.8em; }
}
</style>

<div class="wr-pref-card">
  <div class="wr-pref-header">
    <i class="fas fa-cogs"></i>
    <h4><?php esc_html_e('Full Project Preferences', 'workreap'); ?></h4>
  </div>
  <div class="wr-pref-grid">

    <?php
    // Helper for displaying rows
    function wr_display_pref_row($label, $value, $icon = 'fas fa-info-circle') {
        if (!empty($value)) {
            echo '<div class="wr-pref-row">';
            echo '<div class="wr-pref-label"><i class="' . esc_attr($icon) . '"></i>' . esc_html($label) . '</div>';
            echo '<div class="wr-pref-value">' . wp_kses_post($value) . '</div>';
            echo '</div>';
        }
    }

    // Document Type
    $document_type = get_field('document_type');
    $specify_doc_other = get_field('specify_other_document_type');
    if (!empty($document_type)) {
        $display = is_array($document_type) ? implode(', ', $document_type) : $document_type;
        if (is_array($document_type) && in_array('Other (specify)', $document_type) && $specify_doc_other) {
            $display .= ' <em>(' . esc_html($specify_doc_other) . ')</em>';
        }
        wr_display_pref_row('Document Type', $display, 'fas fa-file-alt');
    }

    // Service Type
    $service_type = get_field('service_type');
    $specify_service_other = get_field('specify_other_service_type');
    if (!empty($service_type)) {
        $display = is_array($service_type) ? implode(', ', $service_type) : $service_type;
        if (is_array($service_type) && in_array('Other', $service_type) && $specify_service_other) {
            $display .= ' <em>(' . esc_html($specify_service_other) . ')</em>';
        }
        wr_display_pref_row('Service Type', $display, 'fas fa-concierge-bell');
    }

    // Industry / Discipline
    $industry_discipline = get_field('industry_discipline');
    $academic_disciplines = get_field('academic_disciplines');
    $professional_business_fields = get_field('professional_business_fields');
    $other_industry_discipline = get_field('other_industry_discipline');
    $specify_other_industry_discipline = get_field('specify_other_industry_discipline');

    if (!empty($industry_discipline)) {
        $display = is_array($industry_discipline) ? implode(', ', $industry_discipline) : $industry_discipline;

        if ((is_array($industry_discipline) && in_array('Academic Disciplines', $industry_discipline)) || $industry_discipline === 'Academic Disciplines') {
            if ($academic_disciplines) {
                $display .= ' <em>(' . esc_html($academic_disciplines) . ')</em>';
            }
        }

        if ((is_array($industry_discipline) && in_array('Professional & Business Fields', $industry_discipline)) || $industry_discipline === 'Professional & Business Fields') {
            if ($professional_business_fields) {
                $display .= ' <em>(' . (is_array($professional_business_fields) ? implode(', ', $professional_business_fields) : esc_html($professional_business_fields)) . ')</em>';
            }
        }

        if ((is_array($industry_discipline) && in_array('Other', $industry_discipline)) || $industry_discipline === 'Other') {
            if ($other_industry_discipline) {
                $display .= ' <em>(' . esc_html($other_industry_discipline) . ')</em>';
                if ($other_industry_discipline === 'Other' && $specify_other_industry_discipline) {
                    $display .= ' <em>(' . esc_html($specify_other_industry_discipline) . ')</em>';
                }
            }
        }

        wr_display_pref_row('Industry / Discipline', $display, 'fas fa-briefcase');
    }

    // Academic Style Guide
    $style_guide = get_field('academic_style_guide');
    $specify_other_style = get_field('specify_other_academic_style_guide');
    if ($style_guide) {
        $display = esc_html($style_guide);
        if ($style_guide === 'Other' && $specify_other_style) {
            $display .= ' <em>(' . esc_html($specify_other_style) . ')</em>';
        }
        wr_display_pref_row('Academic Style Guide', $display, 'fas fa-book');
    }

    // Word Count
    $word_count = get_field('word_count');
    wr_display_pref_row('Word Count', esc_html($word_count), 'fas fa-file-word');

    // Professional Level
$display = '';
if (is_array($professional_level)) {
    $levels = array_map(function($item) {
        return isset($item['label']) ? esc_html($item['label']) : '';
    }, $professional_level);
    $display = implode(', ', $levels);
}

if (!empty($levels) && in_array('Other', array_column($professional_level, 'value')) && $specify_other_level) {
    $display .= ' <em>(' . esc_html($specify_other_level) . ')</em>';
}

wr_display_pref_row('Professional Level', $display, 'fas fa-user-graduate');


    // Deadline
    $deadline_selection = get_field('deadline_selection');
    $custom_deadline = get_field('select_custom_deadline');
    if ($deadline_selection) {
        $display = esc_html($deadline_selection);
        if ($deadline_selection === 'Custom Date' && $custom_deadline) {
            $display .= ' <em>(' . esc_html(date('d M Y, h:i A', strtotime($custom_deadline))) . ')</em>';
        }
        wr_display_pref_row('Deadline', $display, 'fas fa-calendar-alt');
    }

    // Document Upload
    $document_upload = get_field('document_upload');
    if (!empty($document_upload) && !empty($document_upload['url'])) {
        $doc_url = esc_url($document_upload['url']);
        $doc_name = !empty($document_upload['filename']) ? esc_html($document_upload['filename']) : esc_html__('View file', 'workreap');
        $display = "<a href='{$doc_url}' target='_blank'>{$doc_name}</a>";
        wr_display_pref_row('Uploaded Document', $display, 'fas fa-upload');
    }
    ?>

  </div>
</div>


                        <!-- End Additional ACF fields -->

                        <?php do_action('workreap_term_tags_html', $product->get_id(), 'skills', esc_html__('Skills required', 'workreap')); ?>
                        <?php if (!empty($downloadable) && $downloadable === 'yes') { ?>
                            <div class="wr-project-holder">
                                <div class="wr-betaversion-wrap">
                                    <div class="wr-betaversion-info">
                                        <h5><?php esc_html_e('Attachments available to download', 'workreap'); ?></h5>
                                        <p><?php echo sprintf(esc_html__('Download project helping material provided by “%s”', 'workreap'), $user_name); ?></p>
                                    </div>
                                    <div class="wr-downloadbtn">
                                        <span class="wr-btn-solid-lefticon wr-download_files" data-id="<?php echo intval($product->get_id()); ?>" data-order_id=""><?php esc_html_e('Download files', 'workreap'); ?> <i class="wr-icon-download"></i></span>
                                    </div>
                                </div>
                            </div>
                        <?php } ?>
                    </div>
                </div>
            </div>
            <div class="col-lg-5 col-xl-4">
                <aside>
                    <div class="wr-projectbox">
                        <div class="wr-project-box wr-projectprice">
                            <div class="wr-sidebar-title">
                                <?php do_action('workreap_project_type_tag', $product->get_id()); ?>
                                <?php do_action('workreap_get_project_price_html', $product->get_id()); ?>
                                <?php do_action('workreap_project_estimation_html', $product->get_id()); ?>
                            </div>
                            <div class="wr-sidebarpkg__btn">
    <span 
        class="wr-btn-solid-lg-lefticon wr-page-link"
        data-url="<?php echo esc_url(site_url('/submit-proposal/?post_id=' . intval($product->get_id()))); ?>">
        <?php esc_html_e('Apply to this job', 'workreap'); ?>
    </span>
    <?php do_action('workreap_project_saved_item', $product->get_id(), '', '_saved_projects'); ?>
</div>


                        <!-- Project Requirements Card after Fixed Price Project -->
                        <!-- Sidebar: Project requirements -->
<!--<div class="wr-project-box">-->
<!--    <div class="wr-sidebar-title" style="margin-bottom: 0.7em;">-->
<!--        <h5 style="font-size:1.0rem;font-weight:600;margin:0;display:flex;align-items:center;gap:.5em;">-->
<!--            <i class="fas fa-clipboard-list" style="color:#097746;font-size:1.18em;"></i>-->
<!--            <?php esc_html_e('Project requirements', 'workreap'); ?>-->
<!--        </h5>-->
<!--    </div>-->
<!--    <ul class="wr-list wr-list-fields" style="font-size: 0.8rem; color: #384152; margin-bottom: 0;">-->
<!--        <?php if ($academic_style_guide): ?>-->
<!--            <li style="display:flex;align-items:center;gap:.5em;">-->
<!--                <i class="fas fa-book-open" style="color:#097746;font-size:1em;"></i>-->
<!--                <strong><?php esc_html_e('Academic Style Guide:', 'workreap'); ?></strong>-->
<!--                <span>-->
<!--                <?php-->
<!--                    echo esc_html($academic_style_guide);-->
<!--                    if ($academic_style_guide === "Other" && $specify_other_academic_style_guide) {-->
<!--                        echo " <em>(" . esc_html($specify_other_academic_style_guide) . ")</em>";-->
<!--                    }-->
<!--                ?>-->
<!--                </span>-->
<!--            </li>-->
<!--        <?php endif; ?>-->
<!--        <?php if ($word_count): ?>-->
<!--            <li style="display:flex;align-items:center;gap:.5em;">-->
<!--                <i class="fas fa-sort-numeric-up" style="color:#097746;font-size:1em;"></i>-->
<!--                <strong><?php esc_html_e('Word Count:', 'workreap'); ?></strong>-->
<!--                <span><?php echo esc_html($word_count); ?></span>-->
<!--            </li>-->
<!--        <?php endif; ?>-->
<!--        <?php if ($professional_level): ?>-->
<!--            <li style="display:flex;align-items:center;gap:.5em;">-->
<!--                <i class="fas fa-user-graduate" style="color:#097746;font-size:1em;"></i>-->
<!--                <strong><?php esc_html_e('Professional Level:', 'workreap'); ?></strong>-->
<!--                <span>-->
<!--                <?php-->
<!--                    echo esc_html($professional_level);-->
<!--                    if ($professional_level === "Other" && $specify_other_professional_level) {-->
<!--                        echo " <em>(" . esc_html($specify_other_professional_level) . ")</em>";-->
<!--                    }-->
<!--                ?>-->
<!--                </span>-->
<!--            </li>-->
<!--        <?php endif; ?>-->
<!--        <?php if ($deadline_selection): ?>-->
<!--            <li style="display:flex;align-items:center;gap:.5em;">-->
<!--                <i class="fas fa-calendar-day" style="color:#097746;font-size:1em;"></i>-->
<!--                <strong><?php esc_html_e('Deadline:', 'workreap'); ?></strong>-->
<!--                <span><?php echo esc_html($deadline_selection); ?></span>-->
<!--            </li>-->
<!--        <?php endif; ?>-->
<!--        <?php if ($deadline_selection === "Custom Date" && $select_custom_deadline): ?>-->
<!--            <li style="display:flex;align-items:center;gap:.5em;">-->
<!--                <i class="fas fa-calendar-check" style="color:#097746;font-size:1em;"></i>-->
<!--                <strong><?php esc_html_e('Custom Deadline:', 'workreap'); ?></strong>-->
<!--                <span><?php echo esc_html($select_custom_deadline); ?></span>-->
<!--            </li>-->
<!--        <?php endif; ?>-->
<!--    </ul>-->
<!--</div>-->
                        <!-- End Project Requirements Card -->

                    </div>
                    <?php do_action('workreap_project_freelancer_basic', $product->get_id()); ?>
                </aside>
            </div>
        </div>
    </div>
</section>
<?php endwhile; get_footer(); ?>