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
global  $post,$current_user,$workreap_settings;
$hide_languages       = !empty($workreap_settings['hide_languages']) ? $workreap_settings['hide_languages'] : 'no';
$currentuser_id  = !empty($current_user->ID) ? intval($current_user->ID) : 0;
$user_type      = !empty($currentuser_id) ? apply_filters('workreap_get_user_type', $currentuser_id ) : '';
$post_id        = !empty($args['post_id']) ? intval($args['post_id']) : $post->ID;
$post_author    = get_post_field( 'post_author', $post_id );
$user_name      = workreap_get_username($post_id);
$wr_post_meta   = get_post_meta( $post_id,'wr_post_meta',true );
$wr_post_meta   = !empty($wr_post_meta) ? $wr_post_meta : array();

$wr_location    = get_post_meta( $post_id,'location',true );
$wr_location    = !empty($wr_location) ? $wr_location : array();
$profile_views  = get_post_meta( $post_id,'workreap_profile_views',true );
$profile_views  = !empty($profile_views) ? intval($profile_views) : 0;
$address        = apply_filters( 'workreap_user_address', $post_id );

$user_id        = workreap_get_linked_profile_id($post_id,'post');
$description	= !empty($wr_post_meta['description']) ? $wr_post_meta['description'] : get_the_content($post_id);


$completed_rate         = workreap_complete_task_count($user_id);
$freelancer_type        = wp_get_post_terms($post_id, 'freelancer_type');
$freelancer_type        = !empty($freelancer_type[0]) ? $freelancer_type[0]->name : '';

$languages              = wp_get_post_terms($post_id, 'languages');
$languages              = !empty($languages) ? wp_list_pluck($languages, 'name') : array();
$languages              = !empty($languages) ? implode(', ', $languages) : '';

$english_level              = wp_get_post_terms($post_id, 'english_level');
$english_level              = !empty($english_level[0]) ? $english_level[0]->name : '';

$login_user_class   = 'wr_btn_checkout';
$wr_msgform         = 'data-type="task" data-url="'.get_the_permalink( $post ).'"';
if(!empty($currentuser_id)){
    $login_user_class   = '';
    $wr_msgform         = 'data-bs-toggle="modal" data-bs-target="#wr_msgform"';
}
$user_name      = workreap_get_username($post_id);
$user_name      = !empty($user_name) ? $user_name : '';
$user_id        = get_post_meta($post_id, '_linked_profile', true);

$skills                         = get_the_terms($post->ID, 'skills');
$app_task_base      		    = workreap_application_access('task');
$skills_base                    = 'project';
if( !empty($app_task_base) ){
    $skills_base    = 'service';
}
$is_activeMeeting   = in_array('workreap-meetings/workreap-meetings.php', apply_filters('active_plugins', get_option('active_plugins'))) ? true : false;
if(!empty($is_activeMeeting)){
    $meetings_calendar      = get_user_meta($user_id, 'meetings_calendar_settings',true);
    $meetings_calendar      = !empty($meetings_calendar) ? $meetings_calendar : array();
    if(!empty($meetings_calendar['slots'])  || !empty($meetings_calendar['override'])){
        $is_activeMeeting   = true;
    } else {
        $is_activeMeeting   = false;
    }
}
?>
<div class="wr-asideholder wr-freelancer-profile-three">
    <div class="wr-asidebox">
        
        <?php do_action('workreap_get_freelancer_profile_basic', $post_id); ?>
        <div class="wr-rating-profile">
            <ul class="wr-profile-rating">
                <?php do_action('workreap_get_freelancer_rating_count', $post_id); ?>
                <?php do_action('workreap_get_freelancer_views', $post_id); ?>
            </ul>
            <?php do_action('workreap_show_badges', $post_id);?>
        </div>
        <div class="wr-tags-list">
            <ul class="wr-profile-options">
                <?php if( !empty($address) ){?>
                    <li class="wr-profile-location">
                        <em>
                            <i class="wr-icon-map-pin"></i>
                            <?php esc_html_e('Location','workreap');?>
                        </em>
                        <span>
                            <?php echo esc_html($address);?>
                        </span>
                    </li>
                <?php } ?>
                <?php if(!empty($freelancer_type)){?>
                    <li class="wr-profile-freelancer_type">
                        <em>
                            <i class="wr-icon-user"></i>
                            <?php esc_html_e('Freelancer type','workreap');?>
                        </em>
                        <span>
                            <?php echo esc_html($freelancer_type);?>
                        </span>
                    </li>
                <?php } ?>
                <?php if(!empty($languages)){?>
                    <li class="wr-profile-languages">
                        <em>
                           <i class="wr-icon-globe"></i>
                            <?php esc_html_e('Languages','workreap');?>
                        </em>
                        <span>
                            <?php echo esc_html($languages);?>
                        </span>
                    </li>
                <?php } ?>
                <?php if(!empty($english_level)){?>
                    <li class="wr-profile-english_level">
                        <em>
                            <i class="wr-icon-flag"></i>
                            <?php esc_html_e('English level','workreap');?>
                        </em>
                        <span>
                            <?php echo esc_html($english_level);?>
                        </span>
                    </li>
                <?php } ?>
            </ul>
        </div>
        <?php if( (!empty($user_type) && $user_type === 'employers' || !is_user_logged_in()) 
            && !empty($post_author) && $post_author != $currentuser_id
            && (in_array('wp-guppy/wp-guppy.php', apply_filters('active_plugins', get_option('active_plugins')))  || !empty($is_activeMeeting) || in_array('wpguppy-lite/wpguppy-lite.php', apply_filters('active_plugins', get_option('active_plugins'))))){?>
            <div class="wr-sidebarcontent">
                <div class="wr-sidebarinnertitle <?php echo !empty($is_activeMeeting) ? 'wr-active-meeting' : '';?>">
                    <?php if((in_array('wp-guppy/wp-guppy.php', apply_filters('active_plugins', get_option('active_plugins'))) || in_array('wpguppy-lite/wpguppy-lite.php', apply_filters('active_plugins', get_option('active_plugins'))))){?>
                        <a href="javascript:;" class="wr-btn <?php echo esc_attr($login_user_class);?>" <?php echo do_shortcode( $wr_msgform );?>><i class="wr-icon-message-square"></i><?php !empty($is_activeMeeting) ? '' : esc_html_e('Send Message','workreap');?></a>
                    <?php } ?>
                    <?php 
                        if(!empty($is_activeMeeting)){
                            do_action('workreap_show_bookings', $post_id,$post_author);
                        }
                        ?>
                </div>
            </div>
        <?php } ?>
    </div>
    <?php if( !empty($skills) || !empty($description) ){ ?>
        <div class="wr-asidebox-content">
            <?php  if( !empty($description) ){?>
                <div class="wt-title"><?php esc_html_e("About","workreap");?></div>
                <div class="wr-description-area description-with-more wr-description-container">
	                <?php
//	                echo workreapReadMoreDescription( do_shortcode( nl2br( $description ) ), 560 );
	                echo do_shortcode(nl2br($description));
	                ?>
                </div>
            <?php } ?>
            <?php if( !empty($skills) ){ ?>
                <div class="wr-asidebox wr-freelancerinfo wr-freelancer-skills">
                    <div class="wr-freesingletitle">
                        <h4><?php esc_html_e('Skills','workreap');?></h4>
                    </div>
                    <?php do_action( 'workreap_term_tags', $post->ID,'skills','',5,$skills_base );?>
                </div>
            <?php } ?>
        </div>
    <?php } ?>
</div>

<div class="modal fade wr-startchat wr-chat-modal" id="wr_msgform" role="dialog">
    <div class="modal-dialog wr-modaldialog" role="document">
        <div class="modal-content">
            <div class="wr-popuptitle">
                <h4 id="wr_ratingtitle"><?php echo sprintf(esc_html__('Send a message to “%s“','workreap'),$user_name);?></h4>
                <a href="javascript:void(0);" class="close"><i class="wr-icon-x" data-bs-dismiss="modal"></i></a>
            </div>
            <div class="modal-body" id="wr_startcaht_form">
                <div class="wr-startchat-field">
                    <textarea class="form-control" id="wr_message" name="message" placeholder="<?php esc_attr_e('Type your message','workreap');?>"></textarea>
                    <a href="javascript:void(0);" data-post_id="<?php echo intval($post_id);?>"  class="wr-btn wr_sentmsg_task"><?php esc_html_e('Send message','workreap');?></a>
                </div>
            </div>
        </div>
    </div>
</div>
<?php 
// $script = "WorkreapShowMore();";
// wp_add_inline_script( 'workreap', $script, 'after' );
