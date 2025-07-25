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
$project_id		= !empty($_GET['id']) ? intval($_GET['id']) : '';
$user_type		= apply_filters('workreap_get_user_type', $user_identity);
$linked_profile	= workreap_get_linked_profile_id($user_identity,'',$user_type);
$order_type     = !empty($_GET['order_type']) ? $_GET['order_type'] : 'any';
$workreap_args = array(
    'post_type'         => 'proposals',
    'post_status'       => array('publish','hired','cancelled','rejected','completed','disputed','refunded'),
    'posts_per_page'    => $show_posts,
    'paged'             => $paged,
    'orderby'           => 'date',
    'order'             => 'DESC',
);
if(!empty($order_type) && $order_type!= 'any' ){
    $workreap_args['post_status'] = $order_type;
}
$workreap_args['meta_query'] = array(
    array(
        'key'       => 'project_id',
        'value'     => $project_id,
        'compare'   => '=',
        'type'      => 'NUMERIC',
    )
);
$workreap_query      = new WP_Query( apply_filters('workreap_project_proposal_dashbaord_listings_args', $workreap_args) );
$total_proposals    = !empty($workreap_query->found_posts) ? intval($workreap_query->found_posts) : 0;
$create_project = workreap_get_page_uri('add_project_page');
$page_url       = Workreap_Profile_Menu::workreap_profile_menu_link($ref, $user_identity, true, $mode);
$project_listing= Workreap_Profile_Menu::workreap_profile_menu_link('projects', $user_identity, true, 'listing');
$menu_order     = workreap_list_proposal_status_filter();

if( !empty($menu_order['draft']) ){
    unset($menu_order['draft']);
}
$product 	    = wc_get_product( $project_id );
$project_price  = !empty($project_id) ? workreap_get_project_price($project_id) : '';
$date_format    = get_option( 'date_format' );
$time_format    = get_option( 'time_format' );

?>
<div class="wr-project-wrapper">
    <div class="wr-project-box wr-employerproject">
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
                <a href="<?php echo esc_url($project_listing);?>" class="wr-btn-solid-lg"><?php esc_html_e('Go to project listing','workreap');?></a>
            </div>
        </div>
    </div>
    <div class="wr-project-box wr-project-box-two">
        <div class="wr-proposal">
            <div class="wr-propposal_title">
                <h5><?php esc_html_e('All proposals','workreap');?> <span>(<?php echo intval($total_proposals);?>)</span></h5>
            </div>
            <div class="wr-select">
                <select id="wr_order_type" name="order_type" class="form-control wr-selectv">
                    <?php foreach($menu_order as $key => $val ){
                        $selected   = '';

                        if( !empty($order_type) && $order_type == $key ){
                            $selected   = 'selected';
                        }
                        ?>
                        <option data-url="<?php echo esc_url($page_url);?>&order_type=<?php echo esc_attr($key);?>&id=<?php echo intval($project_id);?>" value="<?php echo esc_attr($key);?>" <?php echo esc_attr($selected);?>>
                            <?php echo esc_html($val);?>
                        </option>
                    <?php } ?>
                </select>
            </div>
        </div>
    </div>
    <div class="wr-project-box wr-table-wrapper">
        <div class="wr-project-table-status">
            <?php if ( $workreap_query->have_posts() ) : ?>
                <table class="table wr-table">
                    <thead>
                        <tr>
                            <th><?php esc_html_e('Title','workreap');?></th>
                            <th><?php esc_html_e('Bid price','workreap');?></th>
                            <th><?php esc_html_e('Dated','workreap');?></th>
                            <th><?php esc_html_e('Status','workreap');?></th>
                            <th><?php esc_html_e('Action','workreap');?></th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php 
                        while ( $workreap_query->have_posts() ) : $workreap_query->the_post();
                            global $post;
                            $post_status        = get_post_status( $post->ID );
                            $proposal_meta      = get_post_meta( $post->ID, 'proposal_meta',true );
                            $proposal_meta      = !empty($proposal_meta) ? $proposal_meta : array();
                            $proposal_type      = get_post_meta( $post->ID, 'proposal_type',true );
                            $proposal_type      = !empty($proposal_type) ? $proposal_type : '';
                            $price              = isset($proposal_meta['price']) ? $proposal_meta['price'] : 0;
                            $product_author_id  = get_post_field ('post_author', $post->ID);
                            $linked_profile_id  = workreap_get_linked_profile_id($product_author_id, '','freelancers');
                            $user_name          = workreap_get_username($linked_profile_id);
                            $avatar             = apply_filters( 'workreap_avatar_fallback', workreap_get_user_avatar(array('width' => 100, 'height' => 100), $linked_profile_id), array('width' => 100, 'height' => 100));
                            $wr_total_rating    = get_post_meta( $linked_profile_id, 'wr_total_rating', true );
                            $wr_total_rating	= !empty($wr_total_rating) ? $wr_total_rating : 0;
                            $wr_review_users	= get_post_meta( $linked_profile_id, 'wr_review_users', true );
                            $wr_review_users	= !empty($wr_review_users) ? $wr_review_users : 0;
                            $posted_date        = date_i18n( $date_format.' '. $time_format ,  strtotime(get_the_date($post->post_date)));
                            if( !empty($post) ){ ?>
                            <tr>
                                <td data-label="<?php esc_attr_e('Title','workreap');?>">
                                    <div class="wr-project-table-content">
                                        <?php if( !empty($avatar) ){?>
                                            <img src="<?php echo esc_url($avatar);?>" alt="<?php echo esc_attr($user_name);?>">
                                        <?php } ?>
                                        <div class="wr-project-table-info">
                                            <?php if( !empty($user_name) ){?>
                                                <span><?php echo esc_html($user_name);?></span>
                                            <?php } ?>
                                            <?php if( !empty($wr_review_users) && !empty($wr_total_rating)){?>
                                                <ul class="wr-blogviewdates">
                                                    <li>
                                                        <i class="fas fa-star wr-yellow" style="width: <?php echo intval($wr_total_rating*20);?>%;"></i>
                                                        <em> <?php echo number_format($wr_total_rating,1,'.', '');?> </em>
                                                        <span>(<?php echo intval($wr_review_users);?>)</span>
                                                    </li>
                                                </ul>
                                            <?php } ?>
                                        </div>
                                    </div>
                                </td>
                                <td data-label="<?php esc_attr_e('Bit price','workreap');?>">
                                    <?php 
                                        if( empty($proposal_type) || $proposal_type === 'fixed') {
                                            workreap_price_format($price);
                                        } else {
                                            do_action( 'workreap_proposal_listing_price', $post->ID );
                                        }
                                    ?>
                                </td>
                                <td data-label="<?php esc_attr_e('Dated','workreap');?>"><?php echo esc_html($posted_date);?></td>
                                <td data-label="<?php esc_attr_e('Status','workreap');?>"><?php do_action( 'workreap_proposal_status_tag', $post->ID );?></td>
                                <td data-label="<?php esc_attr_e('Action','workreap');?>">
                                    <?php if( !empty($post_status) && in_array($post_status,array('publish','publish')) ){ ?>
                                        <a href="<?php echo esc_url(Workreap_Profile_Menu::workreap_profile_menu_link('proposals', $user_identity, true, 'detail',$post->ID));?>"><?php esc_html_e('Proposal detail','workreap');?></a>
                                    <?php } elseif( !empty($post_status) && in_array($post_status,array('hired','cancelled','completed','disputed','refunded'))){ ?>
                                        <a href="<?php echo esc_url(Workreap_Profile_Menu::workreap_profile_menu_link('projects', $user_identity, true, 'activity',$post->ID));?>"><?php esc_html_e('View activity','workreap');?></a>
                                    <?php }?>
                                </td>
                            </tr>
                        <?php }
                            endwhile;
                        ?>
                    </tbody>
                </table>
            <?php else: ?>
                <?php do_action( 'workreap_empty_listing', esc_html__('No proposl found', 'workreap')); ?>
            <?php endif; ?>
            <?php 
                if( !empty($total_proposals) && $total_proposals > $show_posts ) {
                    workreap_paginate($workreap_query,'wr-tabfilteritem');
                }
            ?>
        </div>
    </div>
</div>
<?php

wp_reset_postdata();
$script = "
jQuery(document).on('ready', function(){
    jQuery(document).on('change', '#wr_order_type', function (e) {
        let _this       = jQuery(this);
        let page_url = _this.find(':selected').data('url');
		window.location.replace(page_url);
    });    
});
";
wp_add_inline_script( 'workreap', $script, 'after' );