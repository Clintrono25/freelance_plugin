<?php
/**
 * Template loader
 *
 * @link       https://codecanyon.net/user/amentotech/portfolio
 * @since      1.0.0
 *
 * @package    Workreap
 * @subpackage Workreap_/public
 */
if (!class_exists('Workreap_PageTemplaterLoader')) {
    class Workreap_PageTemplaterLoader {

        private static $instance;
        protected $templates;

        //get class instance
        public static function get_instance() {

            if ( null == self::$instance ) {
            self::$instance = new Workreap_PageTemplaterLoader();
            }

            return self::$instance;
        }

        //Constructor
        private function __construct() {
            $this->templates = array();

            if ( version_compare( floatval( get_bloginfo( 'version' ) ), '4.7', '<' ) ) {
                add_filter('page_attributes_dropdown_pages_args',array( $this, 'register_custom_templates' ));
            } else {
                add_filter('theme_page_templates', array( $this, 'add_new_template' ));
            }

            add_filter('wp_insert_post_data', array( $this, 'register_custom_templates' ) );
            add_filter('template_include', array( $this, 'view_custom_templates'), 99 );
            $this->templates = array(
                'templates/dashboard.php' 		        => esc_html__('User Dashboard','workreap'),
                'templates/admin-dashboard.php' 		=> esc_html__('Admin Dashboard','workreap'),
                'templates/search-task.php' 	        => esc_html__('Search Task','workreap'),
                'templates/add-task.php' 	            => esc_html__('Add Task','workreap'),
                'templates/add-project.php' 	        => esc_html__('Add Project','workreap'),
                'templates/submit-proposal.php' 	    => esc_html__('Submit Proposal','workreap'),
                'templates/search-freelancer.php' 	        => esc_html__('Search Freelancers','workreap'),
                'templates/search-projects.php' 	    => esc_html__('Search Projects','workreap'),
                'templates/pricing-plans.php' 	        => esc_html__('Pricing Plans','workreap')
            );
        }

        //Add new templates
        public function add_new_template( $posts_templates ) {
            $posts_templates = array_merge( $posts_templates, $this->templates );
            return $posts_templates;
        }

        //Register Templates
        public function register_custom_templates( $atts ) {
            $cache_key = 'page_templates-' . md5( get_theme_root() . '/' . get_stylesheet() );

            $templates = wp_get_theme()->get_page_templates();
            if ( empty( $templates ) ) {
                $templates = array();
            }

            wp_cache_delete( $cache_key , 'themes');
            $templates = array_merge( $templates, $this->templates );
            wp_cache_add( $cache_key, $templates, 'themes', 1800 );

            return $atts;

        }

        //Embed into dropdown
        public function view_custom_templates( $template ) {
            global $post,$woocommerce,$product;
            if ( ! $post ) {
                return $template;
            }

            if (is_singular() && $post->post_type == 'product') {
                $product            = wc_get_product( $post->ID );
                $product_data       = get_post_meta($post->ID, 'wr_service_meta', true);
                $wr_product_type    = get_post_meta($post->ID, 'wr_product_type', true);
                if( $product->is_type( 'tasks' ) || !empty($product_data) || ($wr_product_type == 'tasks') ){
                    $template = workreap_load_template( 'templates/single-task');
                } else if( $product->is_type( 'projects' ) || $wr_product_type == 'projects' ){
                    $template = workreap_load_template( 'templates/single-project');
                }

                if ( '' != $template ) {
                    return $template ;
                }
            }

            if ( ! isset( $this->templates[get_post_meta( $post->ID, '_wp_page_template', true )] ) ) {
                return $template;
            }

            $file = WORKREAP_DIRECTORY . get_post_meta($post->ID, '_wp_page_template', true);

            if ( file_exists( $file ) ) {
                return $file;
            } else {
                return $file;
            }
            return $template;
        }
    }
    add_action( 'plugins_loaded', array( 'Workreap_PageTemplaterLoader', 'get_instance' ) );
}
