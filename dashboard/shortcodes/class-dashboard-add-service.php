<?php

/**
 *
 * Class 'Workreap_Dashboard_Hooks' defines to add tasks
 *
 * @package     Workreap
 * @subpackage  Workreap/Dashboard
 * @author      Amentotech <info@amentotech.com>
 * @link        https://codecanyon.net/user/amentotech/portfolio
 * @version     1.0
 * @since       1.0
*/

class Workreap_Dashboard_Shortcodes_Service {

    public $task_allowed = true;
    public $task_plans_allowed = true;
    public $number_tasks_allowed = 0;
    public $package_detail = array();

	/**
	 * Task shortcode
	 *
	 * @since    1.0.0
	 * @access   public
	 */
	public function __construct() {
        add_action( 'workreap_add_services_steps', array($this, 'workreap_add_services_step_introduction_html'));
        add_action( 'wp_ajax_workreap_add_service_inroduction_save', array($this, 'workreap_add_service_inroduction_save') );
        add_action( 'wp_ajax_workreap_add_service_media_attachments_save', array($this, 'workreap_add_service_media_attachments_save') );
        add_action( 'wp_ajax_workreap_service_subtask_save', array($this, 'workreap_service_subtask_create') );
        add_action( 'wp_ajax_workreap_add_service_plans_save', array($this, 'workreap_add_service_plans_save') );
        add_action( 'wp_ajax_workreap_service_subtask_delete', array($this, 'workreap_service_subtask_delete') );
        add_action( 'wp_ajax_workreap_get_service_subtask', array($this, 'workreap_get_service_subtask') );
        add_action( 'wp_ajax_workreap_add_service_next_step_template', array($this, 'workreap_add_service_step_form') );
        add_action( 'wp_ajax_workreap_add_service_faqs_save', array($this, 'workreap_add_service_faqs_save') );
    }

    /**
	 * Add task steps
	 *
	 * @since    1.0.0
	 * @access   public
	*/
	public function workreap_add_services_step_introduction_html($workreap_args = array()){
	    //add task introduction
		if ( ! empty( $workreap_args ) && is_array( $workreap_args ) ) {
            extract( $workreap_args );
        }

        $workreap_args   = array( 'post_id'=>$post_id, 'step' => $step );
        $service_meta   = $this->workreap_task_step_values($post_id, $step);
        $workreap_args   = array_merge($service_meta, $workreap_args);

		if($step == 2){

			workreap_get_template(
                'dashboard/post-service/add-service-pricing.php',
                $workreap_args
            );

        } elseif ($step == 3){

            workreap_get_template(
                'dashboard/post-service/add-media-attachments.php',
                $workreap_args
            );

        } elseif ($step == 4){

            workreap_get_template(
                'dashboard/post-service/add-service-faqs.php',
                $workreap_args
            );

        } else {
            //add task template
            workreap_get_template(
				'dashboard/post-service/add-service-introduction.php',
				$workreap_args
			);
        }
    }

    /**
	 * add task media attachment form
	 *
	 * @since    1.0.0
	 * @access   public
	*/
    public function workreap_add_service_step_form(){

        $post_id        = !empty($_POST['post_id']) ? intval($_POST['post_id']) : '';
        $step           = !empty($_POST['step']) ? intval($_POST['step']) : '1';


        $workreap_args   = array( 'post_id'=>$post_id, 'step' => $step );

        $service_meta = $this->workreap_task_step_values($post_id, $step);
        $workreap_args = array_merge($service_meta, $workreap_args);

        if($step == 2){

            workreap_get_template(
                'dashboard/post-service/add-service-pricing.php',
                $workreap_args
            );


        } elseif ($step == 3){
            workreap_get_template(
                'dashboard/post-service/add-media-attachments.php',
                $workreap_args
            );

        } elseif ($step == 4){

            workreap_get_template(
                'dashboard/post-service/add-service-faqs.php',
                $workreap_args
            );
        } else {
            //add task template
            workreap_get_template(
                'dashboard/post-service/add-service.php',
                $workreap_args
            );
        }

        exit;
    }

    /**
	 * Task next steps
	 *
	 * @since    1.0.0
	 * @access   public
	*/
    public function workreap_task_step_values($post_id='', $step=1){
        global $current_user;

        if($step == 2){

            $this->task_allowed    = workreap_task_create_allowed($current_user->ID);
            $this->package_detail  = workreap_get_package($current_user->ID);

            $this->task_plans_allowed   = 'yes';

            $package_type   =  !empty($this->package_detail['type']) ? $this->package_detail['type'] : '';

            if($package_type == 'paid'){
                $this->task_plans_allowed   =  !empty($this->package_detail['package']['task_plans_allowed']) ? $this->package_detail['package']['task_plans_allowed'] : 'no';
            }

            $workreap_args = array(
                'service_cat'           =>  0,
                'service_categories'    =>  array(),
                'service_meta'          =>  array(),
            );

            if($post_id){
                $post           = get_post($post_id);
                $product_data   = get_post_meta($post_id, 'wr_service_meta', true);
                $service_cat    = wp_get_post_terms( $post_id, 'product_cat', array( 'fields' => 'ids' ) );
                $workreap_args   = array(
                    'service_cat'           => !empty($service_cat[0])?$service_cat[0]:0,
                    'service_categories'    => $service_cat,
                    'service_meta'          => $product_data,
                    'task_allowed'          => $this->task_allowed,
                    'task_plans_allowed'    => $this->task_plans_allowed,
                );
            }

            $workreap_args   = apply_filters( 'workreap_add_service_plans', $workreap_args );

        } elseif ($step == 3){

            $workreap_args   = array(
                'product_gallery'       => array(),
                'product_gallery'       => 'no',
                '_downloadable_files'   => array(),
                '_product_video'        => array(),
            );

            if(!empty($post_id)){
                $gallery                = get_post_meta($post_id, '_product_attachments', true);
                $_downloadable          = get_post_meta($post_id, '_downloadable', true);
                $_downloadable_files    = get_post_meta($post_id, '_downloadable_files', true);
                $_product_video         = get_post_meta($post_id, '_product_video', true);
                $workreap_args = array(
                    'post_id'               => $post_id,
                    'product_gallery'       => $gallery,
                    '_downloadable'         => $_downloadable,
                    '_downloadable_files'   => $_downloadable_files,
                    '_product_video'        => $_product_video,
                );
            }

            $workreap_args   = apply_filters( 'workreap_add_service_media', $workreap_args );

        } elseif ($step == 4){

            $workreap_args = array(
                'service_faq'   =>  array(),
            );

            if(!empty($post_id)){
                $workreap_faq_meta = array_filter( (array) get_post_meta($post_id, 'workreap_service_faqs', true) );
                $workreap_args   = array(
                    'post_id'       => $post_id,
                    'service_faq'   => $workreap_faq_meta,
                );
            }

            $workreap_args   = apply_filters( 'workreap_add_service_faq', $workreap_args );

        } else {

            $product_tag_terms = get_terms( 'product_tag',array('hide_empty' => false) );
            $product_term_array = array();
            if ( ! empty( $product_tag_terms ) && ! is_wp_error( $product_tag_terms ) ){
                foreach ( $product_tag_terms as $term ) {
                    $product_term_array[] = $term->name;
                }
            }

            $workreap_args = array(
                'service_title'         =>  '',
                'service_content'       =>  '',
                'service_cat'           =>  0,
                'service_languages'     =>  0,
                'service_tag'           =>  '',
                'service_locations'     =>  0,
                'service_meta'          =>  array(),
                '_downloadable'         =>  'yes',
                '_downloadable_files'   =>  array(),
                '_video_links'          =>  'yes',
                'wr_video_links_files'  =>  array(),
                'post'                  =>  array(),
                'zipcode'               => '',
                'product_term_array'    => $product_term_array,
            );

            if($post_id){
                $post = get_post($post_id);
                $product_data   = get_post_meta($post_id, 'wr_service_meta', true);
                $category       = !empty($product_data['category'])? $product_data['category'] :'';
                $subcategory    = !empty($product_data['subcategory'])? $product_data['subcategory'] :'';
                $service_type   = !empty($product_data['service_type'])? $product_data['service_type'] :'';
                $country_region = !empty($product_data['country'])? $product_data['country'] :'';
                $zipcode        = !empty($product_data['zipcode'])? $product_data['zipcode'] :'';

                $category_id       = '';
                $subcategory_id    = '';
                $service_type_ids  = array();

                if($category){
                    foreach($category as $slug=>$name){

                        if($slug){
                            $category = get_term_by('slug', $slug, 'product_cat');

                            if(!empty($category->term_id)){
                                $category_id = $category->term_id;
                            }

                        }

                    }

                }

                if($subcategory){
                    foreach($subcategory as $slug=>$name){

                        if($slug){
                            $category = get_term_by('slug', $slug, 'product_cat');

                            if(!empty($category->term_id)){
                                $subcategory_id = $category->term_id;
                            }
                        }
                    }

                }

                if($service_type){
                    foreach($service_type as $slug=>$name){

                        if($slug){
                            $category = get_term_by('slug', $slug, 'product_cat');
                            if(!empty($category->term_id)){
                                $service_type_ids[] = $category->term_id;
                            }
                        }
                    }

                    $service_type_ids   = implode(',', $service_type_ids);

                }

                $service_product_tags = wp_get_post_terms( $post_id, 'product_tag', array( 'fields' => 'names' ) );
                $service_product_tags = !empty($service_product_tags) ? $service_product_tags : '';

                if(!empty($service_product_tags) && is_array($service_product_tags)){
                    $service_product_tags   = implode(',', $service_product_tags);
                }

                $workreap_args = array(
                    'service_title'         => get_the_title($post_id),
                    'service_content'       => $post->post_content,
                    'service_cat'           => $category_id,
                    'sub_cat'               => $subcategory_id,
                    'sub_cat2'              => $service_type_ids,
                    'service_tag'           => $service_product_tags,
                    'billing_country'       => $country_region,
                    'zipcode'               => $zipcode,
                    'service_meta'          => $product_data,
                    'product_term_array'    => $product_term_array,
                );
            }

            $workreap_args   = apply_filters( 'workreap_add_service_introduction', $workreap_args );
        }

        return $workreap_args;
    }

    /**
	 * Task faq save
	 *
	 * @since    1.0.0
	 * @access   public
	*/
    public function workreap_add_service_faqs_save(){
        global $workreap_settings, $current_user;
        $post_data  = !empty($_POST['data']) ?  $_POST['data'] : '';
		parse_str($post_data,$data);
        if( function_exists('workreap_is_demo_site') ) {
            workreap_is_demo_site();
        }
        if( function_exists('workreap_verified_user') ) {
            workreap_verified_user();
        }
        $json   = array();

        if (!wp_verify_nonce($_POST['security'], 'ajax_nonce')) {
            $json['type']           = 'error';
            $json['message']        = esc_html__('Restricted Access', 'workreap');
            $json['message_desc']   = esc_html__('You are not allowed to perform this action.', 'workreap');
            wp_send_json($json);
        }

        $service_status   = !empty( $workreap_settings['service_status'] ) ? $workreap_settings['service_status'] : 'publish';
        $post_id          = !empty( $data['post_id'] ) ? intval( $data['post_id'] ) : '';
        $resubmit_service_status    = !empty($workreap_settings['resubmit_service_status']) ? $workreap_settings['resubmit_service_status'] : 'no';

        if(!empty($post_id)){

            if( function_exists('workreap_verify_post_author') ){
                workreap_verify_post_author($post_id);
            }

            $servicefaq_id  = !empty( $data['servicefaq_id'] ) ? intval( $data['servicefaq_id'] ) : '';
            $workreap_faqs   = !empty( $data['faq'] ) ? wp_unslash( $data['faq'] ) : array();
            $workreap_faqs   = workreap_recursive_sanitize_text_field($workreap_faqs);
            $profile_id     = workreap_get_linked_profile_id($current_user->ID,'','freelancers');
            update_post_meta($post_id, 'workreap_service_faqs', $workreap_faqs);
            $this->task_allowed     = workreap_task_create_allowed($current_user->ID);
            $post_status            = get_post_status ( $post_id );
            $post_status            = !empty($post_status) ? $post_status : '';
            if(empty($this->task_allowed) && ($post_status == 'draft' && $service_status !== 'draft')){
                $service_status = 'draft';
            }

            // Update the post status the database
            if ( !empty($post_status) && ($post_status == 'draft' || $post_status == 'pending' || $post_status == 'rejected') ) {
                $service_post = array(
                    'ID'            => $post_id,
                    'post_status'   => $service_status,
                );
                wp_update_post( $service_post );
                if( !empty($service_status) && $service_status === 'pending' && !empty($resubmit_service_status) && $resubmit_service_status === 'yes'){
                    update_post_meta( $post_id, '_post_task_status', 'requested' );
                }
                wp_set_object_terms( $post_id, 'tasks', 'product_type', true );

              /* Send Email to freelancer and admin */
              if (class_exists('Workreap_Email_helper') && !empty( $post_id )) {
                $emailData	= array();

                if (class_exists('WorkreapTaskStatuses')) {

                  $emailData['freelancer_name']		      = workreap_get_username($profile_id);
                  $emailData['freelancer_email']		  = get_userdata( $current_user->ID )->user_email;
                  $emailData['task_name']			  = get_the_title( $post_id );
                  $emailData['task_link']			  = get_permalink($post_id);
                  $emailData['notification_type']     = 'noty_admin_approval';
                  $emailData['sender_id']             = $current_user->ID; //freelancer id
                  $emailData['receiver_id']           = workreap_get_admin_user_id(); //admin id
                  $email_helper                       = new WorkreapTaskStatuses();

                  if($workreap_settings['email_post_task'] == true){
                    $email_helper->post_task_freelancer_email($emailData);
                  }

                  if($workreap_settings['email_admin_task_approval'] == true){
                    $email_helper->post_task_approval_admin_email($emailData);
                    $notifyData						= array();
                    $notifyDetails					= array();
                    $notifyDetails['task_id']       = $post_id;
                    $notifyDetails['freelancer_id']     = $profile_id;
                    $notifyData['receiver_id']		= $current_user->ID;
                    $notifyData['type']				= 'submint_task';
                    $notifyData['linked_profile']	= $profile_id;
                    $notifyData['user_type']		= 'freelancers';
                    $notifyData['post_data']		= $notifyDetails;
                    do_action('workreap_notification_message', $notifyData );
                  } else {
                    $notifyData						= array();
                    $notifyDetails					= array();
                    $notifyDetails['task_id']       = $post_id;
                    $notifyDetails['freelancer_id']     = $profile_id;
                    $notifyData['receiver_id']		= $current_user->ID;
                    $notifyData['type']				= 'task_approved';
                    $notifyData['linked_profile']	= $profile_id;
                    $notifyData['user_type']		= 'freelancers';
                    $notifyData['post_data']		= $notifyDetails;
                    do_action('workreap_notification_message', $notifyData );
                  }
                }

              }
            }

            $service_page_link	= Workreap_Profile_Menu::workreap_profile_menu_link('task', $current_user->ID, true, 'listing');

            $json['type']           = 'success';
            $json['post_id']        = (int)$post_id;
            $json['faq_id']         = (int)$servicefaq_id;
            $json['step']           = 4;
            $json['redirect']       = $service_page_link;
            $json['message']        = esc_html__('Woohoo!', 'workreap');
            $json['message_desc']   = esc_html__('Task has been added successfully!', 'workreap');

            do_action('workreap_add_service_faqs_save_activity', $post_id);

            wp_send_json($json);
        } else {
            $json['type']           = 'error';
            $json['message'] 		= esc_html__('Oops!', 'workreap');
            $json['message_desc'] 	= esc_html__('There is an error occur while saving into database.', 'workreap');
            wp_send_json($json);
        }
    }


    /**
	 *Subtask delete
	 *
	 * @since    1.0.0
	 * @access   public
	*/
    public function workreap_service_subtask_delete(){
        $json = array();
        if( function_exists('workreap_is_demo_site') ) {
            workreap_is_demo_site();
        }
        if (!wp_verify_nonce($_POST['security'], 'ajax_nonce')) {
            $json['type']           = 'error';
            $json['message']        = esc_html__('Restricted Access', 'workreap');
            $json['message_desc']   = esc_html__('You are not allowed to perform this action.', 'workreap');
            wp_send_json($json);
        }

        $data       = $_POST;
        $subtask_id = (!empty($data['subtask_id']))?intval($data['subtask_id']):'';

        if( function_exists('workreap_verify_post_author') ){
            workreap_verify_post_author($subtask_id);
        }

        if(empty($subtask_id)){
            $json['type']           = 'error';
            $json['message'] 		= esc_html__('Oops!', 'workreap');
            $json['message_desc'] 	= esc_html__('There is an error occur while deleting subtask.', 'workreap');
            wp_send_json($json);
        }

        do_action('workreap_subtask_delete_activity', $subtask_id);

        $delete = wp_delete_post($subtask_id);

        if(!empty($delete)){

            $json['type']           = 'success';
            $json['subtask_id']     = (int)$subtask_id;
            $json['message'] 		= esc_html__('Woohoo!', 'workreap');
            $json['message_desc'] 	= esc_html__('Sub task has been deleted!.', 'workreap');
            wp_send_json($json);
        } else {
            $json['type']           = 'error';
            $json['message'] 		= esc_html__('Oops!', 'workreap');
            $json['message_desc'] 	= esc_html__('There is an error occur while deleting subtask.', 'workreap');
            wp_send_json($json);
        }
    }

    /**
	 * Task plan save
	 *
	 * @since    1.0.0
	 * @access   public
	*/
    public function workreap_add_service_plans_save(){
        global $current_user,$workreap_settings;
        $custom_field_option    =  !empty($workreap_settings['custom_field_option']) ? $workreap_settings['custom_field_option'] : false;
        $maxnumber_fields       =  !empty($workreap_settings['maxnumber_fields']) ? $workreap_settings['maxnumber_fields'] : 5;
        if( function_exists('workreap_is_demo_site') ) {
            workreap_is_demo_site();
        }

        if( function_exists('workreap_verified_user') ) {
            workreap_verified_user();
        }

        $post_data  = !empty($_POST['data']) ?  $_POST['data'] : '';
		parse_str($post_data,$data);
        $json = array();

        if (!wp_verify_nonce($_POST['security'], 'ajax_nonce')) {
            $json['type']           = 'error';
            $json['message']        = esc_html__('Restricted Access', 'workreap');
            $json['message_desc'] 	= esc_html__('You are not allowed to perform this action.', 'workreap');
            wp_send_json($json);
        }

        $post_id = !empty( $data['post_id'] ) ? intval( $data['post_id'] ) : '';

        if(!empty($post_id)){

            if( function_exists('workreap_verify_post_author') ){
                workreap_verify_post_author($post_id);
            }

            $workreap_plans          = !empty($data['plans'] ) ? wp_unslash( $data['plans'] ) : array();
            $featured_package       = !empty($data['featured_package']) ? $data['featured_package'] :'';
            $custom_fields          = !empty($data['custom_fields']) ? $data['custom_fields'] : array();
            $subtasks_ids           = !empty($data['subtasks_selected_ids']) ? $data['subtasks_selected_ids'] : array();

            if( !empty($custom_field_option) ){
                $custom_field_array = array();
                if( !empty($custom_fields) ){
                    if( !empty($maxnumber_fields) && !empty($custom_fields) && is_array($custom_fields) && count($custom_fields) >= $maxnumber_fields ){
                        $json['type']           = 'error';
                        $json['message']        = esc_html__('Uh-Oh!', 'workreap');
                        $json['message_desc'] 	= sprintf(esc_html__('You are allowed to add only %s custom fields','workreap'),$maxnumber_fields);
                        wp_send_json($json);
                    }

                    foreach($custom_fields as $key => $custom_field){
                        $custom_field_array[]   = $custom_field;
                        if( empty($custom_field['title'])){
                            $json['type']           = 'error';
                            $json['message']        = esc_html__('Uh-Oh!', 'workreap');
                            $json['message_desc'] 	= esc_html__("Please don't leave empty custom fields. Either remove this or add the field title", 'workreap');
                            wp_send_json($json);
                        }
                    }
                }

                update_post_meta( $post_id, 'wr_custom_fields',$custom_field_array );
            }

            $this->task_allowed     = workreap_task_create_allowed($current_user->ID);
            $this->package_detail   = workreap_get_package($current_user->ID);

            $subtasks_ids   = !empty($subtasks_ids) ? explode(',', $subtasks_ids) : array();
            $subtasks_ids   = !empty($subtasks_ids) ? array_map( 'absint', $subtasks_ids ) : array();
            $workreap_plans  = workreap_recursive_sanitize_text_field($workreap_plans);

            $this->task_plans_allowed   = 'yes';
            $package_type               =  !empty($this->package_detail['type']) ? $this->package_detail['type'] : '';

            if($package_type == 'paid'){
                $this->task_plans_allowed       =  !empty($this->package_detail['package']['task_plans_allowed']) ? $this->package_detail['package']['task_plans_allowed'] : 'no';
                $this->number_tasks_allowed     =  !empty($this->package_detail['package']['number_tasks_allowed']) ? $this->package_detail['package']['number_tasks_allowed'] : 0;
            }

            $delivwery_defult_pkg   = 'basic';

            if($this->task_plans_allowed == 'no'){
                $task_plans = array();
                $counterPlan    = 0;
                foreach($workreap_plans as $key=>$plan_pkgs){
                    $counterPlan++;
                    if(!empty($counterPlan) && $counterPlan <= 1){
                        if(empty($plan_pkgs['price']) || $plan_pkgs['price'] <= 0){
                            $json['type']           = 'error';
                            $json['message']        = esc_html__('Uh-Oh!', 'workreap');
                            $json['message_desc'] 	= esc_html__("Price should be at-least 1 or greater than 1", 'workreap');
                            wp_send_json($json);
                        }
                    }


                    $task_plans[$key]   = $plan_pkgs;
                    $min_price          = $plan_pkgs['price'];
                    $max_price          = $plan_pkgs['price'];
                    break;
                }

                $workreap_plans = $task_plans;
            } else {
                $task_plans = array();
                $min_price  = 0;
                $max_price  = 0;
                $counterPlan    = 0;

                foreach($workreap_plans as $key=>$plan_pkgs){
                    $counterPlan++;
                    if(!empty($counterPlan) && $counterPlan <= 1){
                        if(empty($plan_pkgs['price']) || $plan_pkgs['price'] <= 0){
                            $json['type']           = 'error';
                            $json['message']        = esc_html__('Uh-Oh!', 'workreap');
                            $json['message_desc'] 	= esc_html__("Price should be at-least 1 or greater than 1", 'workreap');
                            wp_send_json($json);
                        }
                    }

                    if(!empty($plan_pkgs['title']) && !empty($plan_pkgs['price'])){
                        $task_plans[$key]   = $plan_pkgs;
                        if( !empty($featured_package) && $featured_package === $key){
                            $task_plans[$key]['featured_package'] = 'yes';
                            $delivwery_defult_pkg   = $key;
                        } else {
                            $task_plans[$key]['featured_package'] = 'no';
                        }

                        if(empty($min_price) || ($min_price > $plan_pkgs['price'])){
                            $min_price          = $plan_pkgs['price'];
                        }

                        if(empty($max_price) || ($max_price < $plan_pkgs['price'])){
                            $max_price          = $plan_pkgs['price'];
                        }
                    }
                }
                $workreap_plans = $task_plans;
            }

            if(isset($workreap_plans['basic']['price'])){
                update_post_meta( $post_id, '_regular_price', floatval($workreap_plans['basic']['price']) );
                update_post_meta( $post_id, '_price', floatval($workreap_plans['basic']['price']) );
            }


            update_post_meta( $post_id, '_min_price', floatval($min_price) );
            update_post_meta( $post_id, '_max_price', floatval($max_price) );

            if( !empty($featured_package) ){
                update_post_meta( $post_id, '_featured_package',$featured_package );
            }

            $duration = array_map(function ($ar) {
                return $ar['delivery_time'];
            }, $workreap_plans);

            wp_set_post_terms( $post_id, $duration, 'delivery_time' );

            if(isset($workreap_plans[$delivwery_defult_pkg]['delivery_time'])){
                update_post_meta( $post_id, '_delivery_time', intval($workreap_plans[$delivwery_defult_pkg]['delivery_time']) );
            }

            if(isset($data['plans'])){
                update_post_meta($post_id, 'workreap_product_plans', $workreap_plans);
            }

            if(isset($data['subtasks_selected_ids'])){
                update_post_meta($post_id, 'workreap_product_subtasks', $subtasks_ids);
            }

            do_action('workreap_add_service_plans_save_activity', $post_id, $data);
            workreapUpdateStatus($post_id);
            $json['type']               = 'success';
            $json['post_id']            = (int)$post_id;
            $json['step']               = 3;
            $json['message'] 		    = esc_html__('Woohoo!', 'workreap');
            $json['message_desc'] 		= esc_html__('Task has been updated', 'workreap');
            wp_send_json($json);
        } else {
            $json['type']               = 'error';
            $json['message'] 		    = esc_html__('Oops', 'workreap');
            $json['message_desc'] 		= esc_html__('There is an error occur, please try again later', 'workreap');
            wp_send_json($json);
        }
    }

    /**
	 * Add new subtask
	 *
	 * @since    1.0.0
	 * @access   public
	*/
    public function workreap_service_subtask_create(){
        $json = array();
        if( function_exists('workreap_is_demo_site') ) {
            workreap_is_demo_site();
        }
        if( function_exists('workreap_verified_user') ) {
            workreap_verified_user();
        }
        if (!wp_verify_nonce($_POST['security'], 'ajax_nonce')) {
            $json['type']           = 'error';
            $json['message']        = esc_html__('Restricted Access', 'workreap');
            $json['message_desc']   = esc_html__('You are not allowed to perform this action.', 'workreap');
            wp_send_json($json);
        }

        if ( !class_exists('WooCommerce') ) {
			$json['type'] = 'error';
			$json['message'] = esc_html__('WooCommerce plugin needs to be installed.', 'workreap');
			wp_send_json( $json );
		}

        $data   = $_POST;

        $post_id          = (!empty($data['service_id'])) ? intval($data['service_id']):'';
        $subtask_id       = (!empty($data['subtask_id']))?intval($data['subtask_id']):'';
        $subtask_title    = (!empty($data['title']))?sanitize_text_field($data['title']):'';
        $subtask_price    = (!empty($data['price']))?floatval($data['price']):'';
        $subtask_content  = (!empty($data['content']))?sanitize_textarea_field($data['content']):'';

        if(empty($post_id)){
            $json['type']           = 'error';
            $json['message']        = esc_html__('Oops', 'workreap');
            $json['message_desc']   = esc_html__('There is error while saving into database', 'workreap');
            wp_send_json($json);
        }


        if(!empty($post_id)){

            if( function_exists('workreap_verify_post_author') ){
                workreap_verify_post_author($post_id);
            }
        }

         // Update post
         $workreap_post_data = array(
            'post_title'    => wp_strip_all_tags($subtask_title),
            'post_content'  => $subtask_content,
            'post_status'   => 'publish',
            'post_type'     => 'product',
            'post_author'   => get_current_user_id(),
            'meta_input'   => array(
                '_regular_price'    => $subtask_price,
                '_price'            => $subtask_price,
            ),
        );

        if(!empty($subtask_id)){
            // Update the post into the database
            $workreap_post_data['ID'] = $subtask_id;
            wp_update_post( $workreap_post_data );
        } else {
            // insert the post into the database
            $subtask_id = wp_insert_post( $workreap_post_data );
        }

        if(!empty($subtask_id)){
            update_post_meta($subtask_id, '_regular_price', $subtask_price);
            update_post_meta( $subtask_id, '_price', $subtask_price );
            wp_set_object_terms( $subtask_id, 'subtasks', 'product_type', false );
            update_post_meta( $subtask_id, '_virtual', 'yes' );
            $workreap_post_data = array(
                'id' => (int)$subtask_id,
                'title' => wp_strip_all_tags($subtask_title),
                'price' => html_entity_decode(get_woocommerce_currency_symbol()).$subtask_price,
            );

            do_action('workreap_subtask_update_activity', $post_id, $subtask_id);

            $json['type']               = 'success';
            $json['subtask_id']         = (int)$subtask_id;
            $json['subtask_data']       = $workreap_post_data;
            $json['message'] 		    = esc_html__('Woohoo!', 'workreap');
            $json['message_desc'] 	    = esc_html__('Add-on added successfully!.', 'workreap');
            wp_send_json($json);
        } else {
            $json['type']           = 'error';
            $json['message']        = esc_html__('Oops', 'workreap');
            $json['message_desc']   = esc_html__('There is an error occur, please try again later', 'workreap');
            wp_send_json($json);
        }

    }

    /**
	 * Get subtask
	 *
	 * @since    1.0.0
	 * @access   public
	*/
    public function workreap_get_service_subtask(){
        global  $woocommerce;
        $json = array();
        if( function_exists('workreap_is_demo_site') ) {
            workreap_is_demo_site();
        }
        if (!wp_verify_nonce($_POST['security'], 'ajax_nonce')) {
            $json['type']           = 'error';
            $json['message']        = esc_html__('Restricted Access', 'workreap');
            $json['message_desc']   = esc_html__('You are not allowed to perform this action.', 'workreap');
            wp_send_json($json);
        }

        $data       = $_POST;
        $subtask_id = (isset($data['subtask_id']))?intval($data['subtask_id']):'';

        if(empty($subtask_id)){
            $json['type']           = 'error';
            $json['message']        = esc_html__('Oops', 'workreap');
            $json['message_desc']   = esc_html__('There is error while saving in database Post id.', 'workreap');
            wp_send_json($json);
        }

        if(!empty($subtask_id)){

            $subtask            = get_post( $subtask_id );
            $subtask_price      = get_post_meta($subtask_id, '_regular_price', true);
            $workreap_post_data  = array(
                'id'        => (int)$subtask_id,
                'title'     => get_the_title($subtask_id),
                'content'   => $subtask->post_content,
                'price'     => $subtask_price,
            );

            $workreap_post_data  = apply_filters('workreap_get_subtasks_data', $workreap_post_data);

            $json['type']           = 'success';
            $json['subtask_id']     = (int)$subtask_id;
            $json['subtask_data']   = $workreap_post_data;
            $json['message']        = esc_html__('Woohoo!', 'workreap');
            $json['message_desc']   = esc_html__('Added!.', 'workreap');
            wp_send_json($json);
        } else {
            $json['type']           = 'error';
            $json['message']        = esc_html__('Oops!', 'workreap');
            $json['message_desc']   = esc_html__('There is an error occur, please try again later', 'workreap');
            wp_send_json($json);
        }
    }

    /**
	 * add service media attachments
	 *
	 * @since    1.0.0
	 * @access   public
	*/
    public function workreap_add_service_media_attachments_save(){
        global $workreap_settings;
        $task_downloadable    =  !empty($workreap_settings['task_downloadable']) ? $workreap_settings['task_downloadable'] : '';
        $post_data  = !empty($_POST['data']) ?  $_POST['data'] : '';
        if( function_exists('workreap_is_demo_site') ) {
            workreap_is_demo_site();
        }
        if( function_exists('workreap_verified_user') ) {
            workreap_verified_user();
        }
		parse_str($post_data,$data);
        $json   = array();

        if (!wp_verify_nonce($_POST['security'], 'ajax_nonce')) {
            $json['type']           = 'error';
            $json['message']        = esc_html__('Restricted Access', 'workreap');
            $json['message_desc']   = esc_html__('You are not allowed to perform this action.', 'workreap');
            wp_send_json($json);
        }

        $post_id    = !empty($data['post_id']) ? intval($data['post_id']) : '';

        if(!empty($post_id)){

            if( function_exists('workreap_verify_post_author') ){
                workreap_verify_post_author($post_id);
            }
        }

        if(empty($post_id)){
            $json['type']           = 'error';
            $json['message']        = esc_html__('Oops!', 'workreap');
            $json['message_desc']   = esc_html__('There is an error occur, please try again later', 'workreap');
            wp_send_json($json);
        }

        $files                  = !empty($data['attachments']) ? $data['attachments'] : array();
        $video_url              = !empty($data['video_url']) ? sanitize_text_field($data['video_url']) : '';
        $custom_video_upload    = !empty($data['custom_video_upload']) ? sanitize_text_field($data['custom_video_upload']) : '';
        $downloadable           = !empty($data['downloadable']) ? sanitize_text_field($data['downloadable']) : 'no';
        $downloads              = !empty($data['downloads']) ? $data['downloads'] : array();

        $video_attachment_id = '';

        if (!empty($video_url) && !empty($custom_video_upload)) {
            $video_attachemt    = workreap_temp_upload_to_media($video_url, $post_id, false);

            if (!empty($video_attachemt['url'])) {
                $video_url = $video_attachemt['url'];
                $video_attachment_id    = $video_attachemt['attachment_id'];
            }
        }

        if(!empty($downloadable) && $downloadable === 'yes' && empty($downloads) && !empty($task_downloadable)){
            $json['type']           = 'error';
            $json['message']        = esc_html__('Oops!', 'workreap');
            $json['message_desc']   = esc_html__('You must need to add file for download', 'workreap');
            wp_send_json($json);
        }
        $attachments_files  = array();
        $attachment_ids     = array();

        if (!empty($files)) {
            foreach ($files as $key => $value) {

                if (!empty($value['attachment_id'])) {
                    $attachment_ids[]        = intval($value['attachment_id']);
                    $value['url']            = sanitize_text_field($value['url']);
                    $value['name']           = sanitize_text_field($value['name']);
                    $attachments_files[$key] = $value;
                } else {
                    $new_attachemt = workreap_temp_upload_to_media($value, $post_id);

                    $attachment_ids[]       = $new_attachemt['attachment_id'];
                    $new_attachemt['size']  = !empty($_POST['size'][$key]) ? sanitize_text_field($_POST['size'][$key]) : filesize(get_attached_file($value));
                    $attachments_files[]    = $new_attachemt;
                }

            }
        }
        $attachment_ids_string = '';
        $product_attachments   = get_post_meta($post_id, '_product_image_gallery',true);
        $product_attachments   = !empty($product_attachments) ? explode(',',$product_attachments): array();
//        if(!empty($product_attachments)){
//            foreach($product_attachments as $product_attachment){
//                if(!in_array($product_attachment,$attachments_files)){
//                    wp_delete_attachment($product_attachment,true);
//                }
//            }
//        }
        if(!empty($attachment_ids)){

            if(is_array($attachment_ids) && !empty($attachment_ids['0'])){
                set_post_thumbnail( $post_id, $attachment_ids['0']);
            }

            $attachment_ids_string  = implode(',', $attachment_ids);
        }

        if(!empty($attachment_ids_string)){
            update_post_meta($post_id, '_product_image_gallery', $attachment_ids_string);
            update_post_meta($post_id, '_product_attachments', $attachments_files);
        } else {
            delete_post_meta( $post_id, '_product_image_gallery');
            delete_post_meta( $post_id, '_product_attachments');
        }

        if(!empty($downloadable) && !empty($task_downloadable)){
            $download_data  = array();

            if($downloads){

                foreach ($downloads as $key => $value) {
                    $name   = !empty($value['title']) ? esc_html($value['title']) : '';

                    if (!empty($value['id'])) {
                        $uploaded_media = array();
                        $uploaded_media['id']           = intval($value['id']);
                        $uploaded_media['file']         = esc_url($value['file']);
                        $uploaded_media['name']         = $name;
                        $uploaded_media['download_id']  = esc_html($value['id']);
                        $download_data[]                = $uploaded_media;
                    } else {
                        $file_url       = !empty($value['file']) ? esc_url($value['file']) : '';
                        $new_attachemt  = workreap_temp_upload_to_media($file_url, $post_id);
                        $attachment_id  = !empty($new_attachemt['attachment_id']) ? $new_attachemt['attachment_id'] : '';
                        $file           = !empty($new_attachemt['url']) ? $new_attachemt['url'] : '';
                        $download_data[]    = array(
                            'id'            => $new_attachemt['attachment_id'],
                            'name'          => $name,
                            'file'          => $file,
                            'download_id'   => $attachment_id,
                        );
                    }
                }
            }
            update_post_meta($post_id, '_downloadable_files', $download_data);
        }

        update_post_meta($post_id, '_downloadable', $downloadable);
        update_post_meta($post_id, '_product_video', $video_url);
        update_post_meta($post_id, '_product_video_attachment_id', $video_attachment_id);
        workreapUpdateStatus($post_id);
        do_action('service_media_attachments_update', $post_id);

        $json['type']               = 'success';
        $json['post_id']            = (int)$post_id;
        $json['attachment_ids']     = $attachment_ids;
        $json['step']               = 4;
        $json['message'] 		    = esc_html__('Woohoo!', 'workreap');
        $json['message_desc'] 		= esc_html__('Task has been updated!', 'workreap');
        wp_send_json($json);
    }

    /**
	 * save service introduction
	 *
	 * @since    1.0.0
	 * @access   public
	*/
    public function workreap_add_service_inroduction_save(){
        global $workreap_settings;
        $error      = array();
        $response   = array();
        $json       = array();
        if( function_exists('workreap_is_demo_site') ) {
            workreap_is_demo_site();
        }
        if (!wp_verify_nonce($_POST['ajax_nonce'], 'ajax_nonce')) {
            $json['type']           = 'error';
            $json['message']        = esc_html__('Restricted Access', 'workreap');
            $json['message_desc']   = esc_html__('You are not allowed to perform this action.', 'workreap');
            wp_send_json($json);
        }

        if( function_exists('workreap_verified_user') ) {
            workreap_verified_user();
        }

        if( !empty($_POST['action']) && $_POST['action'] == 'workreap_add_service_inroduction_save' ) {
            $post_id        = !empty($_POST['post_id']) ? intval($_POST['post_id']) : '';

            if(!empty($post_id)){

                if( function_exists('workreap_verify_post_author') ){
                    workreap_verify_post_author($post_id);
                }
            }
            $default_attribs = array(
                'id' => array(),
                'class' => array(),
                'title' => array(),
                'style' => array(),
                'data' => array(),
            );

            $allowed_tags   = array(
                'a' => array_merge( $default_attribs, array(
                    'href' => array(),
                    'title' => array()
                )),
                'h1'        => array(),
                'h2'        => array(),
                'h3'        => array(),
                'h4'        => array(),
                'h5'        => array(),
                'h6'        => array(),
                'u'             =>  $default_attribs,
                'i'             =>  $default_attribs,
                'q'             =>  $default_attribs,
                'b'             =>  $default_attribs,
                'ul'            => $default_attribs,
                'ol'            => $default_attribs,
                'li'            => $default_attribs,
                'br'            => $default_attribs,
                'hr'            => $default_attribs,
                'strong'        => $default_attribs,
                'blockquote'    => $default_attribs,
                'del'           => $default_attribs,
                'strike'        => $default_attribs,
                'em'            => $default_attribs,
                'code'          => $default_attribs,
            );

            $post_data      = !empty($_POST['workreap_service']) ? $_POST['workreap_service'] : array();
            $post_title     = !empty($post_data['post_title']) ? sanitize_text_field($post_data['post_title']) : '';
            $post_content   = !empty($post_data['post_content']) ? wp_kses($post_data['post_content'], $allowed_tags) : '';
            $locations      = $categories = $post_tags = $languages = array();

            $taxonomy_category_data     = !empty($post_data['category']) ? intval($post_data['category']) : '';
            $category_level2            = !empty($post_data['category_level2']) ? intval($post_data['category_level2']) : '';
            $category_level3            = !empty($post_data['category_level3']) ? $post_data['category_level3'] : array();
            $taxonomy_product_tag_data  = !empty($post_data['product_tag']) ? $post_data['product_tag'] : '';
            $taxonomy_locations_data    = !empty($post_data['locations']) ? sanitize_text_field($post_data['locations']) : '';
       	    $zipcode 	                = !empty( $_POST['zipcode'] ) ? sanitize_text_field( $_POST['zipcode'] ) : '';
            $state 	                    = !empty( $post_data['state'] ) ? sanitize_text_field( $post_data['state'] ) : '';
            $enable_state			    = !empty($workreap_settings['enable_state']) ? $workreap_settings['enable_state'] : false;
            $category_level3            = is_array($category_level3) ? $category_level3 : array($category_level3);
            $validation_fields  = array(
                'post_title'    => esc_html__('Please enter task title.','workreap'),
                'category'      => esc_html__('Please select task category.','workreap'),
                'post_content'  => esc_html__('Please enter task content.','workreap'),
            );

            foreach($validation_fields as $key => $validation_field ){

                if( empty($post_data[$key]) ){
                    $json['type']           = 'error';
                    $json['message']        = esc_html__('Field required','workreap');
                    $json['message_desc']   = $validation_field;
                    wp_send_json($json);
                }
            }

            if(isset($post_data['post_content']) && $workreap_settings['task_description_length_option'] && isset($workreap_settings['task_description_length'])){
                $min_task_description_length = isset($workreap_settings['task_description_length'][1]) ? $workreap_settings['task_description_length'][1] : 50;
                $max_task_description_length = isset($workreap_settings['task_description_length'][2]) ? $workreap_settings['task_description_length'][2] : 500;
                $post_content_length = str_word_count($post_data['post_content']);
                if(isset($post_content_length) && ($post_content_length < $min_task_description_length || $post_content_length > $max_task_description_length) ){
                    $json['type']           = 'error';
	                $json['message']        = esc_html__('Description length','workreap');
                    $json['message_desc']   = sprintf(
                        __('The description should be between %s to %s words in length.', 'workreap'),
                        $min_task_description_length,
                        $max_task_description_length,
                    );
                    wp_send_json($json);
                }
            }

            $parent_category        = array();
            $subcategory            = array();
            $service_type           = array();
            $categories_term        = array();

            if($taxonomy_category_data){
                $categories_term[]                  = $taxonomy_category_data;
                $category                           = get_term_by('id', $taxonomy_category_data, 'product_cat');
                $categories[$category->slug]        = $category->name;
                $parent_category[$category->slug]   = $category->name;
            }

            if($category_level2){
                $category                           = get_term_by('id', $category_level2, 'product_cat');
                $categories[$category->slug]        = $category->name;
                $subcategory[$category->slug]       = $category->name;
                $categories_term[]                  = $category_level2;
            }

            if($category_level3){
                foreach($category_level3 as $term_id){

                    if($term_id){
                        $term_id    = intval($term_id);
                        $category   = get_term_by('id', $term_id, 'product_cat');
                        $categories[$category->slug]        = $category->name;
                        $service_type[$category->slug]      = $category->name;
                        $categories_term[]                  = $term_id;
                    }
                }

            }

            $wr_product_tags = array();
            $taxonomy_product_tag_data  = stripslashes($taxonomy_product_tag_data['0']);
            $taxonomy_product_tag_data  = json_decode($taxonomy_product_tag_data);

            if(!empty($taxonomy_product_tag_data)){
                foreach($taxonomy_product_tag_data as $product_tag){

                    if(!empty($product_tag->value)){
                        $wr_product_tags[]  = $product_tag->value;
                    }
                }
            }

            if($post_id){
                $product_data   = get_post_meta($post_id, 'wr_service_meta', true);
                $product_data   = !empty($product_data) ? $product_data : array();
                $old_zipcode    = get_post_meta( $post_id, 'zipcode', true );
                $old_country    = get_post_meta( $post_id, '_country', true );
                $old_location   = get_post_meta( $post_id, 'location',true );
            }

            if(empty($workreap_settings['enable_zipcode']) ){
                update_post_meta($post_id,'zipcode', 0 );
                update_post_meta($post_id,'longitude',0);
                update_post_meta($post_id,'latitude',0);
                $product_data['country']        = $taxonomy_locations_data;
            } else if(( !empty($old_zipcode) && $old_zipcode != $zipcode && $old_country != $taxonomy_locations_data) || empty($old_zipcode) ){
                $response   = array();
                $response   = workreap_process_geocode_info($zipcode,$taxonomy_locations_data);

                if( !empty($response) ) {
                    update_post_meta($post_id,'location', $response );
                    update_post_meta($post_id,'zipcode', $zipcode );
                    update_post_meta($post_id,'longitude',$response['lng']);
                    update_post_meta($post_id,'latitude',$response['lat']);
                    $product_data['country']        = $taxonomy_locations_data;
                    $product_data['latitude']       = $response['lat'];
                    $product_data['longitude']      = $response['lng'];
                    $product_data['zipcode']        = $zipcode;
                }
            }

            $product_data['categories']     = $categories;
            $product_data['category']       = $parent_category;
            $product_data['subcategory']    = $subcategory;
            $product_data['service_type']   = $service_type;
            $product_data['product_tag']    = $post_tags;
            if( !empty($enable_state) ){
                $product_data['state']        = $state;
            }

            // Update post
            $wr_post_data = array(
                'post_title' => wp_strip_all_tags($post_title),
                'post_content' => $post_content,
                'post_type'    => 'product',
                'post_author'  => get_current_user_id(),
                'meta_input'   => array(
                    'wr_service_meta' => $product_data,
                ),
            );


            if($post_id){
                // Update the post into the database
                $wr_post_data['ID']         = $post_id;
                $wr_post_data['post_name']  = sanitize_title($post_title);
                wp_update_post( $wr_post_data );
            } else {
                $wr_post_data['post_status'] = 'draft';
                // insert the post into the database
                $post_id = wp_insert_post( $wr_post_data );
                $package_options    = workreap_get_package(get_current_user_id());
                if(!empty($package_options['type']) && $package_options['type'] === 'paid' && !empty($package_options['order_id']) ){
                    update_post_meta( $post_id, '_package_order_id',$package_options['order_id'] );
                }
            }

            if($post_id){
                workreapUpdateStatus($post_id);
                update_post_meta($post_id,'wr_product_type','tasks');

                update_post_meta($post_id,'_country',$taxonomy_locations_data);
                wp_set_object_terms($post_id, $wr_product_tags, 'product_tag');
                wp_set_post_terms($post_id,$categories_term,'product_cat');
                wp_set_object_terms( $post_id, 'tasks', 'product_type', true );
                if( !empty($enable_state) ){
                    update_post_meta($post_id,'state', $state );
                }
                do_action('workreap_task_create_activity', $post_id, $post_data);

                $json['type']               = 'success';
                $json['post_id']            = (int)$post_id;
                $json['step']               = 2;
                $json['message'] 		    = esc_html__('Woohoo!', 'workreap');
                $json['message_desc'] 		= esc_html__('Task has been updated', 'workreap');
                wp_send_json($json);
            } else {
                $json['type']               = 'error';
                $json['message'] 		    = esc_html__('Oops', 'workreap');
                $json['message_desc'] 		= esc_html__('There is an error occur, please try again later', 'workreap');
                wp_send_json($json);
            }

        }
        exit;
    }

}

new Workreap_Dashboard_Shortcodes_Service();
