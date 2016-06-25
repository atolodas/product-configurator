<?php
/*
Plugin Name: WordPress Product Configurator
Plugin URI: http://wp-theme.eu/de
Description: Create a Multistep Product Configurator with the attributes and variations of your products.
Version: 1.0.1
Author: WP-THEME.EU
Author URI:http://wp-theme.eu/de
*/

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
if (!defined('WPC_PLUGIN_DIR'))
    define( 'WPC_PLUGIN_DIR', dirname(__FILE__) );
if (!defined('WPC_PLUGIN_ROOT_PHP'))
    define( 'WPC_PLUGIN_ROOT_PHP', dirname(__FILE__).'/'.basename(__FILE__)  );
if(!defined('WPC_PLUGIN_ABSOLUTE_PATH'))
    define('WPC_PLUGIN_ABSOLUTE_PATH',plugin_dir_url(__FILE__));
if (!defined('WPC_PLUGIN_ADMIN_DIR'))
    define( 'WPC_PLUGIN_ADMIN_DIR', dirname(__FILE__) . '/admin' );
if( !class_exists('WPC_Product_Configurator') ) {
    class WPC_Product_Configurator {
        const CAPABILITY = "edit_wpc";
        public function __construct() {
            require_once(WPC_PLUGIN_DIR.'/inc/wpc-functions.php');
            require_once(WPC_PLUGIN_ADMIN_DIR.'/class-admin.php');
            require_once(WPC_PLUGIN_DIR.'/inc/class-scripts-styles.php');
            require_once(WPC_PLUGIN_DIR.'/inc/class-frontend-ajax.php');
            require_once(WPC_PLUGIN_DIR.'/inc/class-frontend-cart.php');
            add_action( 'init', array( &$this, 'init') );

        }
        public function init(){

            require_once(WPC_PLUGIN_DIR.'/inc/class-frontend-product.php');
            $this->custom_post_type();
            $this->register_custom_taxonomy();
            load_plugin_textdomain('wpc', FALSE, dirname(plugin_basename(__FILE__)).'/languages/');
        }
        public function install(){
//            $upload_dir = wp_upload_dir();
//            $wpc_upload_dir=$upload_dir["basedir"]."/product_configurator_images/";
//            if(!file_exists($wpc_upload_dir)){
//                mkdir($wpc_upload_dir, 0777, true);
//            }
            self::create_files();
        }
        private static function create_files() {
            // Install files and folders for uploading files and prevent hotlinking
            $upload_dir      = wp_upload_dir();
            $wpc_upload_dir=$upload_dir["basedir"]."/product_configurator_images";
            $wpc_upload_dir_final=$upload_dir["basedir"]."/product_configurator_images/final_design";
            $content=file_get_contents(plugin_dir_path(__FILE__).'/htaccess.txt');
            $files = array(
                array(
                    'base' 		=> $wpc_upload_dir,
                    'file' 		=> 'index.html',
                    'content' 	=> ''
                ),
                array(
                    'base' 		=> $wpc_upload_dir,
                    'file' 		=> '.htaccess',
                    'content' 	=> $content
                ),
                array(
                    'base' 		=> $wpc_upload_dir_final,
                    'file' 		=> 'index.html',
                    'content' 	=> ''
                ),
                array(
                    'base' 		=> $wpc_upload_dir_final,
                    'file' 		=> '.htaccess',
                    'content' 	=> $content
                )
            );


            foreach ( $files as $file ) {
                if ( wp_mkdir_p( $file['base'] ) && ! file_exists( trailingslashit( $file['base'] ) . $file['file'] ) ) {
                    if ( $file_handle = @fopen( trailingslashit( $file['base'] ) . $file['file'], 'w' ) ) {
                        fwrite( $file_handle, $file['content'] );
                        fclose( $file_handle );
                    }
                }
            }
        }
     public function custom_post_type() {
            $labels = array(
                'name'                => _x( 'Base Designs', 'Post Type General Name', 'wpc' ),
                'singular_name'       => _x( 'Design', 'Post Type Singular Name', 'wpc' ),
                'menu_name'           => __( 'Base Designs', 'wpc' ),
                'name_admin_bar'      => __( 'Base Design', 'wpc' ),
                'all_items'           => __( 'All Designs', 'wpc' ),
                'add_new_item'        => __( 'Add New Design', 'wpc' ),
                'add_new'             => __( 'Add New', 'wpc' ),
                'new_item'            => __( 'New Design', 'wpc' ),
                'edit_item'           => __( 'Edit Design', 'wpc' ),
                'update_item'         => __( 'Update Design', 'wpc' ),
                'view_item'           => __( 'View Design', 'wpc' ),
                'search_items'        => __( 'Search Design', 'wpc' ),
                'not_found'           => __( 'Not found', 'wpc' ),
                'not_found_in_trash'  => __( 'Not found in Trash', 'wpc' ),
            );
            $args = array(
                'label'               => __( 'Base Design', 'wpc' ),
                'labels'              => $labels,
                'supports'            => array( 'title'),
                'hierarchical'        => false,
                'public'              => false,
                'show_in_menu'        => true,
                'show_ui'             => true,

            );
            register_post_type( 'wpc_base_design', $args );

        }
        public function register_custom_taxonomy(){
            $labels = array(
                'name'              => __('Categories', 'wpc' ),
                'singular_name'     => __( 'Category', 'wpc' ),
                'search_items'      => __( 'Search Categories', 'wpc' ),
                'all_items'         => __( 'All Categories', 'wpc' ),
                'parent_item'       => __( 'Parent Category', 'wpc' ),
                'parent_item_colon' => __( 'Parent Category:', 'wpc'),
                'edit_item'         => __( 'Edit Category', 'wpc' ),
                'update_item'       => __( 'Update Category', 'wpc' ),
                'add_new_item'      => __( 'Add New Category', 'wpc' ),
                'new_item_name'     => __( 'New Category Name', 'wpc' ),
                'menu_name'         => __( 'Category' ),
            );
            $args = array(
                'hierarchical'      => true,
                'labels'            => $labels,
                'show_ui'           => true,
                'show_admin_column' => true,
                'query_var'         => true,
                'rewrite'           => array( 'slug' => 'category' ),
            );
            register_taxonomy( 'wpc_design_category', array( 'wpc_base_design' ), $args );
        }


    }
    new WPC_Product_Configurator();
    register_activation_hook( __FILE__, array( 'WPC_Product_Configurator', 'install' ) );
}