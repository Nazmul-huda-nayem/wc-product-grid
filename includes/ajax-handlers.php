<?php
if (!defined('ABSPATH')) {
    exit;
}

// Add this to your main plugin file or include it
class Custom_Woo_Grid_Ajax_Handlers {
    
    public function __construct() {
        // AJAX actions for logged in and non-logged in users
        add_action('wp_ajax_get_quick_view_product', array($this, 'get_quick_view_product'));
        add_action('wp_ajax_nopriv_get_quick_view_product', array($this, 'get_quick_view_product'));
        
        add_action('wp_ajax_add_to_wishlist', array($this, 'add_to_wishlist'));
        add_action('wp_ajax_nopriv_add_to_wishlist', array($this, 'add_to_wishlist'));
        
        add_action('wp_ajax_remove_from_wishlist', array($this, 'remove_from_wishlist'));
        add_action('wp_ajax_nopriv_remove_from_wishlist', array($this, 'remove_from_wishlist'));
        
        add_action('wp_ajax_add_to_compare', array($this, 'add_to_compare'));
        add_action('wp_ajax_nopriv_add_to_compare', array($this, 'add_to_compare'));
        
        add_action('wp_ajax_remove_from_compare', array($this, 'remove_from_compare'));
        add_action('wp_ajax_nopriv_remove_from_compare', array($this, 'remove_from_compare'));
        
        add_action('wp_ajax_load_more_products', array($this, 'load_more_products'));
        add_action('wp_ajax_nopriv_load_more_products', array($this, 'load_more_products'));
        
        add_action('wp_ajax_filter_products', array($this, 'filter_products'));
        add_action('wp_ajax_nopriv_filter_products', array($this, 'filter_products'));
    }
    
    public function get_quick_view_product() {
        // Check if it's a valid AJAX request
        if (!wp_doing_ajax()) {
            wp_die('Direct access denied');
        }
        
        // Verify nonce
        if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'custom_woo_grid_nonce')) {
            wp_send_json_error(array('message' => 'Security check failed'));
        }
        
        $product_id = isset($_POST['product_id']) ? intval($_POST['product_id']) : 0;
        
        if (!$product_id) {
            wp_send_json_error(array('message' => 'Invalid product ID'));
        }
        
        $product = wc_get_product($product_id);
        
        if (!$product || !$product->exists()) {
            wp_send_json_error(array('message' => 'Product not found'));
        }
        
        // Check if product is published
        if ($product->get_status() !== 'publish') {
            wp_send_json_error(array('message' => 'Product not available'));
        }
        
        ob_start();
        ?>
        <div class="quick-view-product">
            <div class="quick-view-images">
                <?php 
                $image_id = $product->get_image_id();
                if ($image_id) {
                    echo wp_get_attachment_image($image_id, 'medium', false, array('class' => 'quick-view-product-image'));
                } else {
                    echo wc_placeholder_img('medium');
                }
                ?>
            </div>
            <div class="quick-view-summary">
                <h2 class="product-title"><?php echo esc_html($product->get_name()); ?></h2>
                
                <?php if ($product->get_average_rating()): ?>
                <div class="product-rating">
                    <?php echo wc_get_rating_html($product->get_average_rating()); ?>
                    <span class="rating-count">(<?php echo $product->get_review_count(); ?> reviews)</span>
                </div>
                <?php endif; ?>
                
                <div class="product-price">
                    <?php echo $product->get_price_html(); ?>
                </div>
                
                <?php if ($product->get_short_description()): ?>
                <div class="product-description">
                    <?php echo wp_kses_post($product->get_short_description()); ?>
                </div>
                <?php endif; ?>
                
                <div class="product-meta">
                    <?php if ($product->get_sku()): ?>
                    <span class="sku">SKU: <strong><?php echo esc_html($product->get_sku()); ?></strong></span>
                    <?php endif; ?>
                    
                    <?php
                    $categories = get_the_terms($product_id, 'product_cat');
                    if ($categories && !is_wp_error($categories)):
                    ?>
                    <span class="categories">
                        Categories: 
                        <?php 
                        $cat_names = array();
                        foreach ($categories as $category) {
                            $cat_names[] = esc_html($category->name);
                        }
                        echo implode(', ', $cat_names);
                        ?>
                    </span>
                    <?php endif; ?>
                </div>
                
                <div class="product-actions">
                    <?php if ($product->is_purchasable() && $product->is_in_stock()): ?>
                        <form class="cart" method="post" enctype='multipart/form-data'>
                            <?php if (!$product->is_sold_individually()): ?>
                            <div class="quantity">
                                <label for="quantity"><?php _e('Quantity:', 'custom-woo-grid'); ?></label>
                                <input type="number" id="quantity" name="quantity" value="1" min="1" max="<?php echo $product->get_max_purchase_quantity(); ?>" step="1">
                            </div>
                            <?php endif; ?>
                            
                            <button type="submit" name="add-to-cart" value="<?php echo esc_attr($product->get_id()); ?>" class="single_add_to_cart_button button">
                                <?php echo esc_html($product->single_add_to_cart_text()); ?>
                            </button>
                        </form>
                    <?php elseif (!$product->is_in_stock()): ?>
                        <p class="out-of-stock"><?php _e('This product is currently out of stock.', 'custom-woo-grid'); ?></p>
                    <?php endif; ?>
                    
                    <a href="<?php echo esc_url(get_permalink($product_id)); ?>" class="view-product-btn">
                        <?php _e('View Full Details', 'custom-woo-grid'); ?>
                    </a>
                </div>
            </div>
        </div>
        <?php
        $html = ob_get_clean();
        
        wp_send_json_success(array('html' => $html));
    }
    
    
        
    public function add_to_wishlist() {
        // Verify nonce
        if (!wp_verify_nonce($_POST['nonce'], 'custom_woo_grid_nonce')) {
            wp_die('Security check failed');
        }
        
        $product_id = intval($_POST['product_id']);
        
        if (!$product_id) {
            wp_send_json_error(array('message' => 'Invalid product ID'));
        }
        
        $user_id = get_current_user_id();
        
        if ($user_id) {
            // For logged in users, store in user meta
            $wishlist = get_user_meta($user_id, '_custom_woo_wishlist', true);
            if (!is_array($wishlist)) {
                $wishlist = array();
            }
            
            if (!in_array($product_id, $wishlist)) {
                $wishlist[] = $product_id;
                update_user_meta($user_id, '_custom_woo_wishlist', $wishlist);
                wp_send_json_success(array('message' => 'Product added to wishlist'));
            } else {
                wp_send_json_error(array('message' => 'Product already in wishlist'));
            }
        } else {
            // For guest users, use cookies/session
            if (!session_id()) {
                session_start();
            }
            
            $wishlist = isset($_SESSION['custom_woo_wishlist']) ? $_SESSION['custom_woo_wishlist'] : array();
            
            if (!in_array($product_id, $wishlist)) {
                $wishlist[] = $product_id;
                $_SESSION['custom_woo_wishlist'] = $wishlist;
                wp_send_json_success(array('message' => 'Product added to wishlist'));
            } else {
                wp_send_json_error(array('message' => 'Product already in wishlist'));
            }
        }
    }
    
    public function remove_from_wishlist() {
        // Verify nonce
        if (!wp_verify_nonce($_POST['nonce'], 'custom_woo_grid_nonce')) {
            wp_die('Security check failed');
        }
        
        $product_id = intval($_POST['product_id']);
        
        if (!$product_id) {
            wp_send_json_error(array('message' => 'Invalid product ID'));
        }
        
        $user_id = get_current_user_id();
        
        if ($user_id) {
            $wishlist = get_user_meta($user_id, '_custom_woo_wishlist', true);
            if (is_array($wishlist)) {
                $key = array_search($product_id, $wishlist);
                if ($key !== false) {
                    unset($wishlist[$key]);
                    update_user_meta($user_id, '_custom_woo_wishlist', array_values($wishlist));
                    wp_send_json_success(array('message' => 'Product removed from wishlist'));
                }
            }
        } else {
            if (!session_id()) {
                session_start();
            }
            
            if (isset($_SESSION['custom_woo_wishlist'])) {
                $wishlist = $_SESSION['custom_woo_wishlist'];
                $key = array_search($product_id, $wishlist);
                if ($key !== false) {
                    unset($wishlist[$key]);
                    $_SESSION['custom_woo_wishlist'] = array_values($wishlist);
                    wp_send_json_success(array('message' => 'Product removed from wishlist'));
                }
            }
        }
        
        wp_send_json_error(array('message' => 'Product not found in wishlist'));
    }
    
    public function add_to_compare() {
        // Verify nonce
        if (!wp_verify_nonce($_POST['nonce'], 'custom_woo_grid_nonce')) {
            wp_die('Security check failed');
        }
        
        $product_id = intval($_POST['product_id']);
        
        if (!$product_id) {
            wp_send_json_error(array('message' => 'Invalid product ID'));
        }
        
        if (!session_id()) {
            session_start();
        }
        
        $compare_list = isset($_SESSION['custom_woo_compare']) ? $_SESSION['custom_woo_compare'] : array();
        
        // Limit compare list to 4 products
        if (count($compare_list) >= 4) {
            wp_send_json_error(array('message' => 'You can compare maximum 4 products'));
        }
        
        if (!in_array($product_id, $compare_list)) {
            $compare_list[] = $product_id;
            $_SESSION['custom_woo_compare'] = $compare_list;
            wp_send_json_success(array('message' => 'Product added to compare'));
        } else {
            wp_send_json_error(array('message' => 'Product already in compare list'));
        }
    }
    
    public function remove_from_compare() {
        // Verify nonce
        if (!wp_verify_nonce($_POST['nonce'], 'custom_woo_grid_nonce')) {
            wp_die('Security check failed');
        }
        
        $product_id = intval($_POST['product_id']);
        
        if (!$product_id) {
            wp_send_json_error(array('message' => 'Invalid product ID'));
        }
        
        if (!session_id()) {
            session_start();
        }
        
        if (isset($_SESSION['custom_woo_compare'])) {
            $compare_list = $_SESSION['custom_woo_compare'];
            $key = array_search($product_id, $compare_list);
            if ($key !== false) {
                unset($compare_list[$key]);
                $_SESSION['custom_woo_compare'] = array_values($compare_list);
                wp_send_json_success(array('message' => 'Product removed from compare'));
            }
        }
        
        wp_send_json_error(array('message' => 'Product not found in compare list'));
    }
    
    public function load_more_products() {
        // Verify nonce
        if (!wp_verify_nonce($_POST['nonce'], 'custom_woo_grid_nonce')) {
            wp_die('Security check failed');
        }
        
        $page = intval($_POST['page']);
        $widget_id = sanitize_text_field($_POST['widget_id']);
        
        if (!$page || !$widget_id) {
            wp_send_json_error(array('message' => 'Invalid parameters'));
        }
        
        // Get widget settings from database or cache
        // This would require storing the widget settings when first rendered
        $widget_settings = $this->get_widget_settings($widget_id);
        
        if (!$widget_settings) {
            wp_send_json_error(array('message' => 'Widget settings not found'));
        }
        
        // Build query args
        $args = $this->build_query_args_for_ajax($widget_settings, $page);
        
        $products = new WP_Query($args);
        
        if (!$products->have_posts()) {
            wp_send_json_error(array('message' => 'No more products found'));
        }
        
        ob_start();
        
        while ($products->have_posts()) {
            $products->the_post();
            $this->render_product_item_ajax($widget_settings);
        }
        
        $html = ob_get_clean();
        wp_reset_postdata();
        
        wp_send_json_success(array(
            'html' => $html,
            'count' => $products->post_count,
            'has_more' => $page < $products->max_num_pages
        ));
    }
    
    public function filter_products() {
        // Verify nonce
        if (!wp_verify_nonce($_POST['nonce'], 'custom_woo_grid_nonce')) {
            wp_die('Security check failed');
        }
        
        $filters = $_POST['filters'];
        
        if (!is_array($filters)) {
            wp_send_json_error(array('message' => 'Invalid filter data'));
        }
        
        // Build query args based on filters
        $args = array(
            'post_type' => 'product',
            'post_status' => 'publish',
            'posts_per_page' => 12,
            'meta_query' => array(),
            'tax_query' => array(),
        );
        
        // Apply filters
        foreach ($filters as $key => $value) {
            switch ($key) {
                case 'category':
                    if ($value) {
                        $args['tax_query'][] = array(
                            'taxonomy' => 'product_cat',
                            'field' => 'term_id',
                            'terms' => intval($value),
                        );
                    }
                    break;
                    
                case 'price_min':
                case 'price_max':
                    $price_min = isset($filters['price_min']) ? floatval($filters['price_min']) : 0;
                    $price_max = isset($filters['price_max']) ? floatval($filters['price_max']) : 999999;
                    
                    $args['meta_query'][] = array(
                        'key' => '_price',
                        'value' => array($price_min, $price_max),
                        'compare' => 'BETWEEN',
                        'type' => 'NUMERIC',
                    );
                    break;
                    
                case 'rating':
                    if ($value) {
                        $args['meta_query'][] = array(
                            'key' => '_wc_average_rating',
                            'value' => intval($value),
                            'compare' => '>=',
                            'type' => 'NUMERIC',
                        );
                    }
                    break;
                    
                case 'availability':
                    if ($value === 'in_stock') {
                        $args['meta_query'][] = array(
                            'key' => '_stock_status',
                            'value' => 'instock',
                        );
                    } elseif ($value === 'out_of_stock') {
                        $args['meta_query'][] = array(
                            'key' => '_stock_status',
                            'value' => 'outofstock',
                        );
                    }
                    break;
            }
        }
        
        $products = new WP_Query($args);
        
        if (!$products->have_posts()) {
            wp_send_json_success(array('html' => '<p>No products found matching your criteria.</p>'));
        }
        
        ob_start();
        
        while ($products->have_posts()) {
            $products->the_post();
            $this->render_product_item_ajax();
        }
        
        $html = ob_get_clean();
        wp_reset_postdata();
        
        wp_send_json_success(array('html' => $html));
    }
    
    private function get_widget_settings($widget_id) {
        // This is a simplified version - you'd need to implement proper settings storage
        $cached_settings = get_transient('custom_woo_grid_settings_' . $widget_id);
        return $cached_settings;
    }
    
    private function build_query_args_for_ajax($settings, $page) {
        $args = array(
            'post_type' => 'product',
            'post_status' => 'publish',
            'posts_per_page' => $settings['products_per_page'] ?? 12,
            'paged' => $page,
            'orderby' => $settings['order_by'] ?? 'date',
            'order' => $settings['order'] ?? 'desc',
            'meta_query' => array(),
            'tax_query' => array(),
        );
        
        // Apply the same filters as in the main widget
        // This would use the same logic as in the main widget class
        
        return $args;
    }
    
    private function render_product_item_ajax($settings = array()) {
        global $product;
        
        if (!$product) {
            return;
        }
        
        // Use default settings if not provided
        $default_settings = array(
            'show_image' => 'yes',
            'show_title' => 'yes',
            'show_price' => 'yes',
            'show_rating' => 'yes',
            'show_add_to_cart' => 'yes',
            'show_sale_badge' => 'yes',
            'show_stock_badge' => 'yes',
            'show_featured_badge' => 'yes',
        );
        
        $settings = wp_parse_args($settings, $default_settings);
        
        echo '<div class="product-item">';
        
        // Product badges
        if ($settings['show_sale_badge'] === 'yes' || $settings['show_stock_badge'] === 'yes' || $settings['show_featured_badge'] === 'yes') {
            echo '<div class="product-badges">';
            
            if ($settings['show_sale_badge'] === 'yes' && $product->is_on_sale()) {
                echo '<span class="product-badge sale-badge">' . __('Sale', 'custom-woo-grid') . '</span>';
            }
            
            if ($settings['show_featured_badge'] === 'yes' && $product->is_featured()) {
                echo '<span class="product-badge featured-badge">' . __('Featured', 'custom-woo-grid') . '</span>';
            }
            
            if ($settings['show_stock_badge'] === 'yes' && !$product->is_in_stock()) {
                echo '<span class="product-badge stock-badge">' . __('Out of Stock', 'custom-woo-grid') . '</span>';
            }
            
            echo '</div>';
        }
        
        // Product image
        if ($settings['show_image'] === 'yes') {
            echo '<div class="product-image">';
            echo '<a href="' . get_permalink() . '">';
            echo woocommerce_get_product_thumbnail();
            echo '</a>';
            echo '</div>';
        }
        
        echo '<div class="product-content">';
        
        // Product title
        if ($settings['show_title'] === 'yes') {
            echo '<h3 class="product-title">';
            echo '<a href="' . get_permalink() . '">' . get_the_title() . '</a>';
            echo '</h3>';
        }
        
        // Product rating
        if ($settings['show_rating'] === 'yes') {
            $rating = $product->get_average_rating();
            if ($rating > 0) {
                echo '<div class="product-rating">';
                echo wc_get_rating_html($rating);
                echo '</div>';
            }
        }
        
        // Product price
        if ($settings['show_price'] === 'yes') {
            echo '<div class="product-price">';
            echo $product->get_price_html();
            echo '</div>';
        }
        
        // Product actions
        echo '<div class="product-actions">';
        
        // Add to cart button
        if ($settings['show_add_to_cart'] === 'yes') {
            woocommerce_template_loop_add_to_cart();
        }
        
        echo '</div>'; // .product-actions
        echo '</div>'; // .product-content
        echo '</div>'; // .product-item
    }
}

// Initialize the AJAX handlers
new Custom_Woo_Grid_Ajax_Handlers();