<?php
/**
 * Project listing
 *
 * @package     Workreap
 * @subpackage  Workreap/templates/dashboard
 * @author      Amentotech <info@amentotech.com>
 * @link        https://codecanyon.net/user/amentotech/portfolio
 * @version     1.0
 * @since       1.0
 */
global $current_user;

$show_posts		= get_option('posts_per_page') ? get_option('posts_per_page') : 10;
$paged 			= ( get_query_var( 'paged' ) ) ? get_query_var( 'paged' ) : 1;
$ref		    = !empty($_GET['ref']) ? esc_html($_GET['ref']) : '';
$mode			= !empty($_GET['mode']) ? esc_html($_GET['mode']) : '';
$user_identity	= intval($current_user->ID);
$proposal_id	= !empty($_GET['id']) ? intval($_GET['id']) : 0;
$project_id     = !empty($proposal_id) ? get_post_meta($proposal_id,'project_id',true) : 0;
$project_id     = !empty($project_id) ? $project_id : 0;
$user_type		= apply_filters('workreap_get_user_type', $user_identity);
$linked_profile	= workreap_get_linked_profile_id($user_identity,'',$user_type);

$product 	        = wc_get_product( $project_id );
$project_price      = !empty($project_id) ? workreap_get_project_price($project_id) : '';
$proposal_status    = !empty($proposal_id) ? get_post_status( $proposal_id ) : '';
$project_meta	    = get_post_meta( $project_id, 'wr_project_meta',true);
$project_meta	    = !empty($project_meta) ? $project_meta : array();
$project_type	    = get_post_meta( $proposal_id, 'proposal_type',true);
$project_type       = !empty($project_type) ? $project_type : '';
$proposal_meta	    = get_post_meta( $proposal_id, 'proposal_meta',true);
$proposal_meta	    = !empty($proposal_meta) ? $proposal_meta : array();
$proposal_type      = !empty($proposal_meta['proposal_type']) ? $proposal_meta['proposal_type'] : '';



$no_of_freelancers	    = get_post_meta( $project_id, 'no_of_freelancers',true);
$no_of_freelancers	    = !empty($no_of_freelancers) ? $no_of_freelancers : 0;

$workreap_attr                   = array();
$workreap_attr['proposal_id']    = $proposal_id;
$workreap_attr['user_type']      = $user_type;
$workreap_attr['user_identity']  = $user_identity;
$workreap_attr['project_type']   = $project_type;
$workreap_attr['proposal_status']= $proposal_status;
$workreap_attr['project_id']     = $project_id;
$workreap_attr['freelancer_id']      = get_post_field( 'post_author', $proposal_id );
$workreap_attr['employer_id']       = get_post_field( 'post_author', $project_id );
$workreap_attr['proposal_meta']  = $proposal_meta;
if( !empty($proposal_type) && $proposal_type === 'milestone'){
    $milestone              = !empty($proposal_meta['milestone']) ? $proposal_meta['milestone'] : array(); 
    $mileastone_array       = array();
    $completed_mil_array    = array();
    $hired_milestone        = array();
    $requested_milestone    = array();

    $hired_balance      = 0;
    $earned_balance     = 0;
    $remaning_balance   = 0;
    $milestone_total    = 0;
    if( !empty($milestone) ){
        foreach($milestone as $key => $value ){
            $status = !empty($value['status']) ? $value['status'] : '';
            $price  = !empty($value['price']) ? $value['price'] : 0;
            $milestone_total    = $milestone_total  + $price;
            if( !empty($status) && $status === 'hired'){
                $hired_balance = $hired_balance + $price;
                $hired_milestone[$key] = $value;
            } else if( !empty($status) && $status === 'completed'){
                $earned_balance = $earned_balance + $price;
                $completed_mil_array[$key] = $value;

            }else if( !empty($status) && $status === 'requested'){
                $requested_milestone[$key] = $value;
                $hired_balance       = $hired_balance + $price;
            } else {
                $mileastone_array[$key] = $value;
                
                $remaning_balance       = $remaning_balance + $price;
            }

        }
        
        $requested_milestone    = !empty($requested_milestone) ? array_merge($requested_milestone,$hired_milestone) : $hired_milestone;
        $mileastone_array       = !empty($mileastone_array) ? array_merge($requested_milestone,$mileastone_array): $requested_milestone;

        $workreap_attr['earned_balance']         = $earned_balance;
        $workreap_attr['hired_balance']          = $hired_balance;
        $workreap_attr['remaning_balance']       = $remaning_balance;
        $workreap_attr['mileastone_array']       = $mileastone_array;
        $workreap_attr['completed_mil_array']    = $completed_mil_array;
        $workreap_attr['milestone_total']        = $milestone_total;
    }
 }
?>
<?php workreap_get_template_part('dashboard/post-project/dashboard', 'project-dispute-notificaton',$workreap_attr);?>
<div class="wr-project-wrapper wr-sendprojectruq">
    <div class="wr-project-box wr-employerproject">
        <?php do_action('workreap_featured_item', $product,'featured_project');?>
        <div class="wr-employerproject-title">
            <?php do_action( 'workreap_project_type_tag', $product->get_id() );?>
            <?php if($product->get_name()){?>
                <h3><?php echo esc_html($product->get_name());?></h3>
            <?php }?>
            <ul class="wr-blogviewdates">
                <?php do_action( 'workreap_posted_date_html', $product );?>
                <?php do_action( 'workreap_location_html', $product );?>
                <?php do_action( 'workreap_texnomies_html_v2', $product->get_id(),'expertise_level','wr-icon-briefcase' );?>
                <?php do_action( 'workreap_hiring_freelancer_html', $product );?>
            </ul>
        </div>
        <div class="wr-price">
            <?php if( !empty($project_price) ){?>
                <h4><?php echo do_shortcode( $project_price );?></h4>
            <?php } ?>
            <div class="wr-project-detail">
                <a href="<?php echo get_the_permalink($product->get_id());?>" class="wr-btn-solid-lg"><?php esc_html_e('View project','workreap');?></a>
            </div>
        </div>
    </div>
    <div class="wr-projectstatus">
        <div class="wr-projectsstatus">
            <?php workreap_get_template_part('dashboard/post-project/dashboard', 'project-employer-basic',$workreap_attr);?>
            <?php 
                if( !empty($proposal_type) && $proposal_type === 'milestone') {
                    workreap_get_template_part('dashboard/post-project/freelancer/dashboard', 'project-milestone',$workreap_attr);
                } else if( !empty($project_type) && $project_type != 'fixed' ){
                    do_action( 'workreap_project_basic_details', $proposal_id );
                }
            ?>
            <div class="wr-projectsactivity">
                <ul class="nav nav-tabs wr-nav-tabs" id="projectActivitiesTabs" role="tablist">
                    <li>
                        <button class="active" id="project-activities-tab" data-bs-toggle="tab" data-bs-target="#project-activities" aria-controls="project-activities" aria-selected="true">
                            <i class="wr-icon-folder"></i>
                            <?php esc_html_e('Project activity','workreap');?>
                        </button>
                    </li>
                    <li>
                        <button class="" id="proposal-invoices-tab" data-bs-toggle="tab" data-bs-target="#proposal-invoices" aria-controls="proposal-invoices" aria-selected="false">
                            <i class="wr-icon-file-text"></i>
                            <?php esc_html_e('Invoices','workreap');?>
                        </button>
                    </li>
                    <?php do_action( 'workreap_project_freelancer_avtivity_tabs',$workreap_attr );?>
                </ul>
                <div class="tab-content wr-project-type-content" id="projectActivitiesTabsContent">
                    <?php workreap_get_template_part('dashboard/post-project/dashboard', 'project-activities',$workreap_attr);?>
                    <?php 
                        if( !empty($project_type) && $project_type === 'hourly' ) {
                            do_action( 'workreap_hourly_proposal_invoice_listing', $workreap_attr );
                        } else {
                            workreap_get_template_part('dashboard/post-project/dashboard', 'project-invoices',$workreap_attr);
                        }
                        do_action( 'workreap_project_freelancer_avtivity_tab_content', $workreap_attr );
                    ?>
                </div>
            </div>
        </div>
    </div>
</div>
<?php do_action('wpguppy_start_post_widget_chat', $proposal_id);
