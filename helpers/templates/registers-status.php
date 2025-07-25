<?php
/**
 *
 * Class 'WorkreapRegistrationStatuses' defines user registration
 *
 * @package     Workreap
 * @subpackage  Workreap/helpers/templates
 * @author      Amentotech <info@amentotech.com>
 * @link        https://codecanyon.net/user/amentotech/portfolio
 * @version     1.0
 * @since       1.0
*/

if (!class_exists('WorkreapRegistrationStatuses')) {

  class WorkreapRegistrationStatuses extends Workreap_Email_helper{
    public function __construct()
    {
      //do something
    }

    /* Email to admin on Faq Questions */
    public function user_faq_questions($params = ''){
      global $workreap_settings;
      extract($params);
      $email_to             = !empty( $workreap_settings['email_sender_email'] ) ? $workreap_settings['email_sender_email'] : get_option('admin_email', 'info@example.com'); ; //admin email
      $sender_name          = !empty($sender_name) ? $sender_name : '';
      $sender_phone         = !empty($sender_phone) ? $sender_phone : '';
      $sender_email         = !empty($sender_email) ? $sender_email : '';
      $question_title       = !empty($question_title) ? $question_title : '';
      $sitename             = !empty($sitename) ? $sitename : '';
      $question_description = !empty($question_description) ? $question_description : '';
      $subject_default 	    = esc_html__('A new faq queston - {{sitename}}', 'workreap'); //default email subject
      $contact_default 	    = wp_kses(__('A new faq question has been posted by the {{sender_name}},<br/> {{question_title}} <br/> {{question_description}} <br/>Email: {{sender_email}} <br/> Phone:  {{sender_phone}}.', 'workreap'),
        array(
          'a'       => array(
            'href'  => array(),
            'title' => array()
          ),
          'br'      => array(),
          'em'      => array(),
          'strong'  => array(),
        )
      );
      $subject		            = !empty( $workreap_settings['faq_question_admin_subject'] ) ? $workreap_settings['faq_question_admin_subject'] : $subject_default; //getting subject
      $subject		            = str_replace("{{sitename}}", $sitename, $subject); //getting subject
      $email_content          = !empty( $workreap_settings['faq_question_mail_content'] ) ? $workreap_settings['faq_question_mail_content'] : $contact_default; //getting content

      $email_content = str_replace("{{sender_name}}", $sender_name, $email_content);
      $email_content = str_replace("{{sender_email}}", $sender_email, $email_content);
      $email_content = str_replace("{{sitename}}", $sitename, $email_content);
      $email_content = str_replace("{{sender_phone}}", $sender_phone, $email_content);
      $email_content = str_replace("{{question_title}}", $question_title, $email_content);
      $email_content = str_replace("{{question_description}}", $question_description, $email_content);

      /* data for greeting */
      $greeting['greet_keyword']      = '';
      $greeting['greet_value']        = '';
      $greeting['greet_option_key']   = '';

      $body = $this->workreap_email_body($email_content, $greeting);
      $body  = apply_filters('workreap_user_registration_email_content', $body);
      wp_mail($email_to, $subject, $body); //send Email
    }

    /* Email to User on Reset Password */
    public function user_reset_password($params = ''){
      global $workreap_settings;
      extract($params);
      $email_to 			      = !empty($email) ? $email : '';
      $name 			          = !empty($name) ? $name : '';
      $sitename 			      = !empty($sitename) ? $sitename : '';
      $reset_link 			    = !empty($reset_link) ? $reset_link : '';
      $subject_default 	    = esc_html__('Reset password - {{sitename}}', 'workreap'); //default email subject
      $content_default 	    = wp_kses(__('Someone requested to reset the password of following account: <br/> Email Address: {{account_email}} <br/>If this was a mistake, just ignore this email and nothing will happen.<br/>To reset your password, click reset link below:<br/>{{reset_link}}', 'workreap'),
        array(
          'a'       => array(
            'href'  => array(),
            'title' => array()
          ),
          'br'      => array(),
          'em'      => array(),
          'strong'  => array(),
        )
      );
      $subject		    = !empty( $workreap_settings['user_password_reset_subject'] ) ? $workreap_settings['user_password_reset_subject'] : $subject_default; //getting subject
      $subject		    = str_replace("{{sitename}}", $sitename, $subject); //getting subject
      $email_content  = !empty( $workreap_settings['user_reset_password_content'] ) ? $workreap_settings['user_reset_password_content'] : $content_default; //getting content
      $reset_link_    = $this->process_email_links($reset_link, esc_html__('Reset link','workreap')); //Reset link

      $email_content = str_replace("{{name}}", $name, $email_content);
      $email_content = str_replace("{{sitename}}", $sitename, $email_content);
      $email_content = str_replace("{{reset_link}}", $reset_link_, $email_content);
      $email_content = str_replace("{{account_email}} ", $email, $email_content);

      /* data for greeting */
      $greeting['greet_keyword'] = 'name';
      $greeting['greet_value'] = $name;
      $greeting['greet_option_key'] = 'user_reset_password_greeting';

      $body = $this->workreap_email_body($email_content, $greeting);
      $body  = apply_filters('workreap_user_registration_email_content', $body);
      wp_mail($email_to, $subject, $body); //send Email

    }

    /* Email to Freelancer on Registration */
    public function registration_user_email($params = ''){
      global $workreap_settings;
      extract($params);

      $email_to 			          = !empty($email) ? $email : '';
      $user_name                = !empty($name) ? $name : '';
      $user_password            = !empty($password) ? $password : '';
      $user_email               = $email_to;
      $user_verification_link   = !empty($verification_link) ? $verification_link : '';
      $site_name                = !empty($site) ? $site : '';

      $subject_default 	        = esc_html__('Thank you for registration at {{sitename}}', 'workreap'); //default email subject
      $contact_default 	        = wp_kses(__('Thank you for the registration at "{{sitename}}". Please click below to verify your account<br/> {{verification_link}}', 'workreap'),
        array(
          'a'       => array(
            'href'  => array(),
            'title' => array()
          ),
          'br'      => array(),
          'em'      => array(),
          'strong'  => array(),
        )
      );

      $subject		            = !empty( $workreap_settings['user_registration_subject'] ) ? $workreap_settings['user_registration_subject'] : $subject_default; //getting subject
      $subject		            = str_replace("{{sitename}}", $site_name, $subject); //getting subject
      $email_content          = !empty( $workreap_settings['user_registration_content'] ) ? $workreap_settings['user_registration_content'] : $contact_default; //getting content
      $verification_link_     = $this->process_email_links($user_verification_link, esc_html__('Verification link','workreap')); //verification link

      $email_content = str_replace("{{name}}", $user_name, $email_content);
      $email_content = str_replace("{{email}}", $user_email, $email_content);
      $email_content = str_replace("{{password}}", $user_password, $email_content);
      $email_content = str_replace("{{sitename}}", $site_name, $email_content);
      $email_content = str_replace("{{verification_link}}", $verification_link_, $email_content);

      /* data for greeting */
      $greeting['greet_keyword'] = 'name';
      $greeting['greet_value'] = $user_name;
      $greeting['greet_option_key'] = 'email_user_registration_greeting';

      $body = $this->workreap_email_body($email_content, $greeting);
      $body  = apply_filters('workreap_user_registration_email_content', $body);
      wp_mail($email_to, $subject, $body); //send Email

    }

    /* Email to Admin on User Registration */
    public function registration_admin_email($params = ''){
      global $workreap_settings;
      extract($params);
      $email_to 			          = !empty( $workreap_settings['admin_email_user_registration'] ) ? $workreap_settings['admin_email_user_registration'] : get_option('admin_email', 'info@example.com'); ; //admin email
      $user_name                = !empty($name) ? $name : '';
      $user_email               = !empty($email) ? $email : '';
      $site_name                = !empty($site) ? $site : '';
      $subject_default 	        = esc_html__('New registration at {{sitename}}', 'workreap'); //default email subject
      $contact_default 	        = wp_kses(__('A new user has been registered on the site with the name "{{name}}" and email address {{email}}', 'workreap'), //default email content
        array(
          'a' => array(
            'href' => array(),
            'title' => array()
          ),
          'br' => array(),
          'em' => array(),
          'strong' => array(),
        )
      );

      $subject		    = !empty( $workreap_settings['admin_registration_subject'] ) ? $workreap_settings['admin_registration_subject'] : $subject_default; //getting subject
      $subject		    = str_replace("{{sitename}}", $site_name, $subject); //getting subject
      $email_content  = !empty( $workreap_settings['admin_registration_content'] ) ? $workreap_settings['admin_registration_content'] : $contact_default; //getting content

      $email_content = str_replace("{{name}}", $user_name, $email_content);
      $email_content = str_replace("{{email}}", $user_email, $email_content);
      $email_content = str_replace("{{sitename}}", $site_name, $email_content);

      /* data for greeting */
      $greeting['greet_keyword']      = '';
      $greeting['greet_value']        = '';
      $greeting['greet_option_key']   = '';

      $body   = $this->workreap_email_body($email_content, $greeting);

      $body   = apply_filters('workreap_admin_registration_email_content', $body);
      wp_mail($email_to, $subject, $body); //send Email
    }

    /* Email to Admin on User Registration if verify by admin */
    public function registration_verify_by_admin_email($params = ''){
      global $workreap_settings;
      extract($params);
      $email_to 			          = !empty( $workreap_settings['admin_email_user_registration_verify_request'] ) ? $workreap_settings['admin_email_user_registration_verify_request'] : get_option('admin_email', 'info@example.com'); ; //admin email
      $user_name                = !empty($name) ? $name : '';
      $user_email               = !empty($email) ? $email : '';
      $site_name                = !empty($site) ? $site : '';
      $login_url                = !empty($login_url) ? $login_url : '';
      $subject_default 	        = esc_html__('New registration approval request at {{sitename}}', 'workreap'); //default email subject
      $contact_default 	        = wp_kses(__('A new user has been registered on the site with the name {{name}} and email address {{email}}. <br /> The registration is pending for approval, you can login  {{login_url}} to the admin to approve the account.', 'workreap'), //default email content
        array(
          'a' => array(
            'href' => array(),
            'title' => array()
          ),
          'br' => array(),
          'em' => array(),
          'strong' => array(),
        )
      );

      $subject		    = !empty( $workreap_settings['admin_verify_register_user_subject'] ) ? $workreap_settings['admin_verify_register_user_subject'] : $subject_default; //getting subject
      $subject		    = str_replace("{{sitename}}", $site_name, $subject); //getting subject
      $email_content  = !empty( $workreap_settings['admin_verify_user_registration_content'] ) ? $workreap_settings['admin_verify_user_registration_content'] : $contact_default; //getting content
      $login_link_     = $this->process_email_links($login_url, esc_html__('Login', 'workreap')); //task/post link

      $email_content = str_replace("{{name}}", $user_name, $email_content);
      $email_content = str_replace("{{email}}", $user_email, $email_content);
      $email_content = str_replace("{{sitename}}", $site_name, $email_content);
      $email_content = str_replace("{{login_url}}", $login_link_, $email_content);

      /* data for greeting */
      $greeting['greet_keyword']      = '';
      $greeting['greet_value']        = '';
      $greeting['greet_option_key']   = '';

      $body   = $this->workreap_email_body($email_content, $greeting);

      $body   = apply_filters('workreap_admin_verify_user_registration_email_content', $body);
      wp_mail($email_to, $subject, $body); //send Email
    }

    /* Account approval request */
    public function registration_account_approval_request($params = '')
    {
      global $workreap_settings;
      extract($params);
      $email_to 			          = !empty($email) ? $email : '';
      $user_name                = !empty($name) ? $name : '';
      $user_password            = !empty($password) ? $password : '';
      $user_email               = $email_to;
      $site_name                = !empty($site) ? $site : '';
      $subject_default 	        = esc_html__('Thank you for registration at {{sitename}}', 'workreap'); //default email subject
      $contact_default 	        = wp_kses(__('Thank you for the registration at "{{sitename}}". Your account will be approved after the verification.', 'workreap'), //default email content
        array(
          'a' => array(
            'href' => array(),
            'title' => array()
          ),
          'br' => array(),
          'em' => array(),
          'strong' => array(),
        )
      );

      $subject		      = !empty( $workreap_settings['user_account_approval_subject'] ) ? $workreap_settings['user_account_approval_subject'] : $subject_default; //getting subject
      $subject		      = str_replace("{{sitename}}", $site_name, $subject); //getting subject
      $email_content    = !empty( $workreap_settings['user_account_approval_content'] ) ? $workreap_settings['user_account_approval_content'] : $contact_default; //getting content

      $email_content = str_replace("{{name}}", $user_name, $email_content);
      $email_content = str_replace("{{email}}", $user_email, $email_content);
      $email_content = str_replace("{{password}}", $user_password, $email_content);
      $email_content = str_replace("{{sitename}}", $site_name, $email_content);

      /* data for greeting */
      $greeting['greet_keyword']      = 'name';
      $greeting['greet_value']        = $user_name;
      $greeting['greet_option_key']   = 'user_account_approval_request_greeting';


      $body = $this->workreap_email_body($email_content, $greeting);
      $body  = apply_filters('workreap_user_account_approval_email_content', $body);

      wp_mail($email_to, $subject, $body); //send Email

    }

    /* Account approved request */
    public function registration_account_approved_request($params = '')
    {
      global $workreap_settings;
      extract($params);
      $email_to 			          = !empty($email) ? $email : '';
      $user_name                = !empty($name) ? $name : '';
      $user_email               = $email_to;
      $site_name                = !empty($site) ? $site : '';

      $subject_default 	        = esc_html__('Account approved', 'workreap'); //default email subject
      $contact_default 	        = wp_kses(__('Congratulations! <br/> Your account has been approved by the admin.', 'workreap'), //default email content
        array(
          'a' => array(
            'href' => array(),
            'title' => array()
          ),
          'br' => array(),
          'em' => array(),
          'strong' => array(),
        )
      );

      $subject		      = !empty( $workreap_settings['user_approved_account_subject'] ) ? $workreap_settings['user_approved_account_subject'] : $subject_default; //getting subject
      $email_content    = !empty( $workreap_settings['approved_user_account_content'] ) ? $workreap_settings['approved_user_account_content'] : $contact_default; //getting content

      $email_content = str_replace("{{name}}", $user_name, $email_content);
      $email_content = str_replace("{{email}}", $user_email, $email_content);
      $email_content = str_replace("{{sitename}}", $site_name, $email_content);

      /* data for greeting */
      $greeting['greet_keyword']      = 'name';
      $greeting['greet_value']        = $user_name;
      $greeting['greet_option_key']   = 'user_email_request_approved_account_greeting';

      $body = $this->workreap_email_body($email_content, $greeting);
      $body  = apply_filters('workreap_user_account_approved_email_content', $body);

      wp_mail($email_to, $subject, $body); //send Email

    }

    /* Email to user on Registration by google */
    public function social_registration_user_email($params = ''){
      global $workreap_settings;
      extract($params);

      $email_to 			          = !empty($email) ? $email : '';
      $user_name                = !empty($name) ? $name : '';
      $user_email               = $email_to;
      $site_name                = !empty($site) ? $site : '';

      $subject_default 	        = esc_html__('Registration at {{sitename}} via google account', 'workreap'); //default email subject
      $contact_default 	        = wp_kses(__('Thank you for the registration at "{{sitename}}" Your account has been created. ', 'workreap'),
        array(
          'a'       => array(
            'href'  => array(),
            'title' => array()
          ),
          'br'      => array(),
          'em'      => array(),
          'strong'  => array(),
        )
      );

      $subject		            = !empty( $workreap_settings['subject_social_registration_user_email'] ) ? $workreap_settings['subject_social_registration_user_email'] : $subject_default; //getting subject
      $subject		            = str_replace("{{sitename}}", $site_name, $subject); //getting subject
      $email_content          = !empty( $workreap_settings['content_social_registration_user_email'] ) ? $workreap_settings['content_social_registration_user_email'] : $contact_default; //getting content

      $email_content = str_replace("{{name}}", $user_name, $email_content);
      $email_content = str_replace("{{email}}", $user_email, $email_content);
      $email_content = str_replace("{{sitename}}", $site_name, $email_content);

      /* data for greeting */
      $greeting['greet_keyword'] = 'name';
      $greeting['greet_value'] = $user_name;
      $greeting['greet_option_key'] = 'greeting_social_registration_user_email';

      $body = $this->workreap_email_body($email_content, $greeting);
      $body  = apply_filters('workreap_user_social_registration_email_content', $body);
      wp_mail($email_to, $subject, $body); //send Email

    }

    /* Account approval request on Registration by google */
    public function social_registration_account_approval_request($params = '')
    {
      global $workreap_settings;
      extract($params);
      $email_to 			          = !empty($email) ? $email : '';
      $user_name                = !empty($name) ? $name : '';
      $user_email               = $email_to;
      $site_name                = !empty($site) ? $site : '';
      $subject_default 	        = esc_html__('Registration at {{sitename}} via google account', 'workreap'); //default email subject
      $contact_default 	        = wp_kses(__('Thank you for the registration at "{{sitename}}". Your account will be approved after the verification.', 'workreap'), //default email content
        array(
          'a' => array(
            'href' => array(),
            'title' => array()
          ),
          'br' => array(),
          'em' => array(),
          'strong' => array(),
        )
      );

      $subject		      = !empty( $workreap_settings['social_user_account_approval_subject'] ) ? $workreap_settings['social_user_account_approval_subject'] : $subject_default; //getting subject
      $subject		      = str_replace("{{sitename}}", $site_name, $subject); //getting subject
      $email_content    = !empty( $workreap_settings['user_social_account_approval_content'] ) ? $workreap_settings['user_social_account_approval_content'] : $contact_default; //getting content

      $email_content = str_replace("{{name}}", $user_name, $email_content);
      $email_content = str_replace("{{email}}", $user_email, $email_content);
      $email_content = str_replace("{{sitename}}", $site_name, $email_content);

      /* data for greeting */
      $greeting['greet_keyword']      = 'name';
      $greeting['greet_value']        = $user_name;
      $greeting['greet_option_key']   = 'user_social_account_approval_request_greeting';


      $body = $this->workreap_email_body($email_content, $greeting);
      $body  = apply_filters('workreap_social_user_account_approval_email_content', $body);

      wp_mail($email_to, $subject, $body); //send Email

    }
  }
  new WorkreapRegistrationStatuses();
}
