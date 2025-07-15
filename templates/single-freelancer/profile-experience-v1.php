<?php
/**
 * Provide basic profile inofrmation
 * 
 * @link       https://codecanyon.net/user/amentotech/portfolio
 * @since      1.0.0
 *
 * @package    Workreap
 * @subpackage Workreap_/public/partials
 */
global $post;
$post_id        = !empty($args['post_id']) ? intval($args['post_id']) : $post->ID;
$wr_post_meta   = get_post_meta( $post_id,'wr_post_meta',true );
$wr_post_meta   = !empty($wr_post_meta) ? $wr_post_meta : array();
$experience     = !empty($wr_post_meta['experience']) ? $wr_post_meta['experience'] : array();
$list_num       = 4;
$countries  = workreap_get_countries();
if( !empty($experience) ){ ?>
    <div class="wr-asidebox wr-freelancerinfo">
        <div class="wr-freesingletitle">
            <h4><?php esc_html_e('Experience','workreap');?></h4>
        </div>
        <ul class="wr-themeaccordion">
            <?php $counter   = 0;
             $count_exp      = !empty($experience) && is_array($experience) ? count($experience) : 0;
            foreach($experience as $key => $value ){
                $counter ++;
                $job_title	    = !empty($value['job_title']) ? $value['job_title'] : '';
                $company		= !empty($value['company']) ? $value['company'] : '';
                $location		= !empty($value['location']) ? $value['location'] : '';
                $enddate 		= !empty( $value['end_date'] ) ? $value['end_date'] : '';
                $description    = !empty($value['description']) ? $value['description'] : '';
                $end_date 		= !empty( $enddate ) ? date_i18n(get_option( 'date_format' ), strtotime(apply_filters('workreap_date_format_fix',$enddate ))) : '';
                $li_class       = $counter > $list_num ? 'd-none wt-edu-hide' : '';
                $location       = !empty($location) && !empty($countries[$location]) ? $countries[$location] : '';
               ?>
                <li class="wr-themeaccordion_item <?php echo esc_attr($li_class);?>">
                <div class="wr-themeaccordion_content">
                    <?php 
                        if( !empty($job_title) ){
                            echo '<h6>'.esc_html($job_title).'</h6>';
                        }
                    ?>
                    <?php if( !empty($company) || !empty($location) || !empty($end_date) ){?>
                        <ul class="wt-field-options aaa">
                            <?php if( !empty($company) ){?>
                                <li>
                                    <i class="wr-icon-briefcase"></i>
                                    <?php echo esc_html($company);?>
                                </li>
                            <?php } ?>
                            <?php if( !empty($location) ){?>
                                <li>
                                    <i class="wr-icon-map-pin"></i>
                                    <?php echo esc_html($location);?>
                                </li>
                            <?php } ?>
                            <?php if( !empty($end_date) ){?>
                                <li>
                                    <i class="wr-icon-calendar"></i>
                                    <?php echo esc_html($end_date);?>
                                </li>
                            <?php } ?>

                        </ul>
                        <?php if( !empty($description) ){?>
                            <div class="wr-description-container">
                                <p><?php echo workreapReadMoreDescription(do_shortcode($description),150);?></p>
                            </div>
                        <?php } ?>
                    <?php } ?>
                </div>
                </li>
                <?php if($counter == $list_num && $count_exp > $list_num){ ?>
                        <li class="wr-load-more wr-secondary-btn"><a href="javascript:;"><?php esc_html_e("Load more","workreap");?></a></li>
                <?php } ?>
            <?php } ?>
            </ul>
    </div>
<?php }

