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
$education      = !empty($wr_post_meta['education']) ? $wr_post_meta['education'] : array();
$list_num       = 4;
if( !empty($education) ){ ?>
    <div class="wr-asidebox wr-freelancerinfo">
        <div class="wr-freesingletitle">
            <h4><?php esc_html_e('Education','workreap');?></h4>
        </div>
        <ul class="wr-themeaccordion">
            <?php $counter  = 0;
            $count_edu      = !empty($education) && is_array($education) ? count($education) : 0;
            foreach($education as $key => $value ){
                $counter ++;
                $degree_title	= !empty($value['title']) ? $value['title'] : '';
                $institute		= !empty($value['institute']) ? $value['institute'] : '';
                $enddate 		= !empty( $value['end_date'] ) ? $value['end_date'] : '';
                $description    = !empty($value['description']) ? $value['description'] : '';
                $end_date 		= !empty( $enddate ) ? date_i18n(get_option( 'date_format' ), strtotime(apply_filters('workreap_date_format_fix',$enddate ))) : '';
                $li_class       = $counter > $list_num ? 'd-none wt-edu-hide' : '';
               ?>
                <li class="wr-themeaccordion_item <?php echo esc_attr($li_class);?>">
                <div class="wr-themeaccordion_content">
                    <?php 
                        if( !empty($degree_title) ){
                            echo '<h6>'.esc_html($degree_title).'</h6>';
                        }
                    ?>
                    <?php if( !empty($institute)  || !empty($end_date)){?>
                        <ul class="wt-field-options">
                            <?php if( !empty($institute) ){?>
                                <li>
                                    <i class="wr-icon-briefcase"></i>
                                    <?php echo esc_html($institute);?>
                                </li>
                            <?php } ?>
                            <?php if( !empty($end_date) ){?>
                                <li>
                                    <i class="wr-icon-calendar"></i>
                                    <?php echo esc_html($end_date);?>
                                </li>
                            <?php } ?>
                        </ul>
                        
                    <?php } ?>
                    <?php if( !empty($description) ){?>
                        <div class="wr-description-container">
                            <p><?php echo workreapReadMoreDescription(do_shortcode($description),150);?></p>
                        </div>
                        <?php } ?>
                </div>
                </li>
                    <?php if($counter == $list_num && $count_edu > $list_num){ ?>
                        <li class="wr-load-more wr-secondary-btn"><a href="javascript:;"><?php esc_html_e("Load more","workreap");?></a></li>
                <?php } ?>
            <?php } ?>
            </ul>
    </div>
<?php }

