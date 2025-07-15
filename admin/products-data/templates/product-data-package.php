<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * 
 * Template to display package tab meta fields
 *
 * @package     Workreap
 * @subpackage  Workreap/admin/products_data
 * @author      Amentotech <info@amentotech.com>
 * @link        http://amentotech.com/
 * @version     1.0
 * @since       1.0
 */

global $woocommerce, $post, $workreap_settings;
$enable_ai          = !empty($workreap_settings['enable_ai']) ? $workreap_settings['enable_ai'] : false;
$enable_ai_packages = !empty($workreap_settings['enable_ai_packages']) ? $workreap_settings['enable_ai_packages'] : false;
$workreap_subtask_fields_values = get_post_meta($post->ID, 'workreap_product_subtasks', TRUE);
$workreapInputFields    = array();
if(function_exists('workreap_woocommerce_get_fields')){
    $workreapInputFields    = workreap_woocommerce_get_fields($post->ID,'freelancers');
}
?>
<div class="options_group product-data-packages-feilds">
    <?php do_action('workreap_packages_fields_before', $workreap_product_tasks_fields);
    
    woocommerce_wp_select( array(
        'id'          => 'package_type',
        'value'       => get_post_meta( get_the_ID(), 'package_type', true ),
        'label'       => esc_html__('Package type', 'workreap'),
        'options'     => array( 'days' => esc_html__('Day(s)', 'workreap'), 'month' => esc_html__('Month(s)', 'workreap'), 'year' => esc_html__('Year(s)', 'workreap')),
    ) );
    if(!empty($workreapInputFields)){
        foreach($workreapInputFields as $workreapInputField){
            woocommerce_wp_text_input($workreapInputField);
        }
    }
    woocommerce_wp_checkbox( array(
	    'id'            => 'task_plans_allowed',
	    'value'         => get_post_meta( get_the_ID(), 'task_plans_allowed', true ),
	    'label'         => esc_html__('Allow task plans', 'workreap'),
	    'description'   => '',
    ) );
    if(!empty($enable_ai) && !empty($enable_ai_packages) ){
        woocommerce_wp_checkbox( array(
            'id'            => 'allow_ai',
            'value'         => get_post_meta( $post->ID, 'allow_ai', true ),
            'label'         => esc_html__('Allow AI feature', 'workreap'),
            'description'   => '',
        ) );
    }
    woocommerce_wp_checkbox(
	    array(
		    'id'          => 'most_popular_task_package',
		    'label'       => __( 'Most Popular', 'workreap' ),
		    'value'       => get_post_meta($post->ID, 'most_popular_task_package', true),
	    )
    );
    
    do_action('workreap_packages_fields_after', $workreap_product_tasks_fields);?>
</div>