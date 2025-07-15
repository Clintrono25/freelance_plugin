<?php

namespace Elementor;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Workreap_User_Menu' ) ) {
	/**
	 * Workreap Elements
	 *
	 * Elementor widget.
	 *
	 * @since 1.0.0
	 */
	class Workreap_User_Menu extends Widget_Base {

		/**
		 * Get widget name.
		 *
		 * Retrieve image widget name.
		 *
		 * @return string Widget name.
		 * @since 1.0.0
		 * @access public
		 *
		 */
		public function get_name() {
			return 'workreap-user-menu';
		}

		/**
		 * Get widget title.
		 *
		 * Retrieve image widget title.
		 *
		 * @return string Widget title.
		 * @since 1.0.0
		 * @access public
		 *
		 */
		public function get_title() {
			return __( 'User Menu', 'workreap' );
		}

		/**
		 * Get widget icon.
		 *
		 * Retrieve image widget icon.
		 *
		 * @return string Widget icon.
		 * @since 1.0.0
		 * @access public
		 *
		 */
		public function get_icon() {
			return 'eicon-post-list';
		}

		/**
		 * Get widget categories.
		 *
		 * Retrieve the list of categories the image widget belongs to.
		 *
		 * Used to determine where to display the widget in the editor.
		 *
		 * @return array Widget categories.
		 * @since 1.0.0
		 * @access public
		 *
		 */
		public function get_categories() {
			return array( 'workreap-elements' );
		}

		/**
		 * Get widget keywords.
		 *
		 * Retrieve the list of keywords the widget belongs to.
		 *
		 * @return array Widget keywords.
		 * @since 1.0.0
		 * @access public
		 *
		 */
		public function get_keywords() {
			return array( 'user', 'menu', 'login' );
		}

		/**
		 * Retrieve the list of style the widget depended on.
		 *
		 * Used to set style dependencies required to run the widget.
		 *
		 * @return array Widget style dependencies.
		 * @since 1.0.0
		 *
		 * @access public
		 *
		 */
		public function get_style_depends() {
			return array();
		}

		/**
		 * Retrieve the list of scripts the widget depended on.
		 *
		 * Used to set scripts dependencies required to run the widget.
		 *
		 * @return array Widget scripts dependencies.
		 * @since 1.0.0
		 *
		 * @access public
		 *
		 */
		public function get_script_depends() {
			return array();
		}

		/**
		 * Register widget controls.
		 *
		 * Adds different input fields to allow the user to change and customize the widget settings.
		 *
		 * @since 1.0.0
		 * @access protected
		 */
		protected function register_controls() {

			$this->start_controls_section(
				'section_general',
				array(
					'label' => esc_html__( 'General', 'workreap' ),
					'tab'   => Controls_Manager::TAB_CONTENT,
				)
			);

			$this->add_control(
				'login_btn_text',
				[
					'type'        => Controls_Manager::TEXT,
					'label'       => esc_html__( 'Login Button', 'workreap' ),
					'default'     => esc_html__( 'Sign in', 'workreap' ),
					'label_block' => true,
				]
			);

			$this->add_control(
				'register_btn_text',
				[
					'type'        => Controls_Manager::TEXT,
					'label'       => esc_html__( 'Register Button', 'workreap' ),
					'default'     => esc_html__( 'Post a task', 'workreap' ),
					'label_block' => true,
				]
			);

			$this->end_controls_section();

		}

		/**
		 * Render image widget output on the frontend.
		 *
		 * Written in PHP and used to generate the final HTML.
		 *
		 * @since 1.0.0
		 * @access protected
		 */
		protected function render() {
			$settings = $this->get_settings_for_display();?>
            <div class="wr-elementor-user-menu-wrapper">
            <?php
			if ( is_user_logged_in() ) { ?>
                <div class="wr-user-menu-wrapper"><?php echo workreap_login_user_menu_details(); ?></div>
			<?php } else {
				$view_type   = ! empty( $workreap_settings['registration_view_type'] ) ? $workreap_settings['registration_view_type'] : 'pages';
				$login       = workreap_get_page_uri( 'login' );
				$login_class = '';
				if ( ! empty( $view_type ) && $view_type === 'popup' ) {
					$login       = 'javascript:void(0);';
					$login_class = 'wr-login-popup';
				}?>
                <div class="wr-user-menu-wrapper">
                    <div class="wr-navbarbtn">
                        <a href="<?php echo do_shortcode($login); ?>" class="wr-btn wr-login <?php echo esc_attr($login_class); ?>"><?php echo esc_html($settings['login_btn_text']); ?></a>
                        <span data-type="post_task" id="wr_post_task" class="wr-btn-solid-lg"><?php echo esc_html($settings['register_btn_text']); ?></span>
                    </div>
                </div>
				<?php
			} ?>
            </div>
            <?php
		}
	}

	Plugin::instance()->widgets_manager->register( new Workreap_User_Menu );
}
