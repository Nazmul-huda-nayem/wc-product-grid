<?php
/**
 * Plugin Name: Custom WooCommerce Product Grid for Elementor
 * Description: Advanced WooCommerce product grid widget for Elementor with extensive filtering and styling options
 * Version: 1.0.0
 * Author: Your Name
 * Text Domain: custom-woo-grid
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

define('CUSTOM_WOO_GRID_PATH', plugin_dir_path(__FILE__));
define('CUSTOM_WOO_GRID_URL', plugin_dir_url(__FILE__));

class Custom_Woo_Product_Grid_Plugin {
    
    public function __construct() {
        add_action('plugins_loaded', array($this, 'init'));
        add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts'));
        
        // Include AJAX handlers
        require_once CUSTOM_WOO_GRID_PATH . 'includes/ajax-handlers.php';
    }
    
    public function init() {
        // Check if Elementor and WooCommerce are installed
        if (!did_action('elementor/loaded') || !class_exists('WooCommerce')) {
            add_action('admin_notices', array($this, 'admin_notice_missing_dependencies'));
            return;
        }
        
        // Add Elementor widget
        add_action('elementor/widgets/widgets_registered', array($this, 'register_widgets'));
        add_action('elementor/elements/categories_registered', array($this, 'add_elementor_widget_categories'));
    }
    
    public function admin_notice_missing_dependencies() {
        echo '<div class="notice notice-warning is-dismissible">';
        echo '<p>' . __('Custom WooCommerce Product Grid requires Elementor and WooCommerce to be installed and activated.', 'custom-woo-grid') . '</p>';
        echo '</div>';
    }
    
    public function enqueue_scripts() {
        wp_enqueue_style('custom-woo-grid-style', CUSTOM_WOO_GRID_URL . 'assets/css/style.css', array(), '1.0.0');
        wp_enqueue_script('custom-woo-grid-script', CUSTOM_WOO_GRID_URL . 'assets/js/script.js', array('jquery'), '1.0.0', true);
        
        wp_localize_script('custom-woo-grid-script', 'custom_woo_grid_ajax', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('custom_woo_grid_nonce')
        ));
    }
    
    public function add_elementor_widget_categories($elements_manager) {
        $elements_manager->add_category(
            'woocommerce-custom',
            array(
                'title' => __('WooCommerce Custom', 'custom-woo-grid'),
                'icon' => 'fa fa-shopping-cart',
            )
        );
    }
    
    public function register_widgets() {
        require_once CUSTOM_WOO_GRID_PATH . 'includes/class-product-grid-widget.php';
        \Elementor\Plugin::instance()->widgets_manager->register_widget_type(new \Custom_Woo_Product_Grid_Widget());
    }
}

new Custom_Woo_Product_Grid_Plugin();