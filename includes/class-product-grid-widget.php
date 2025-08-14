<?php
if (!defined('ABSPATH')) {
    exit;
}

class Custom_Woo_Product_Grid_Widget extends \Elementor\Widget_Base {

    public function get_name() {
        return 'custom_woo_product_grid';
    }

    public function get_title() {
        return __('WooCommerce Product Grid', 'custom-woo-grid');
    }

    public function get_icon() {
        return 'eicon-products';
    }

    public function get_categories() {
        return ['woocommerce-custom'];
    }

    protected function _register_controls() {
        // Layout Section
        $this->start_controls_section(
            'layout_section',
            [
                'label' => __('Layout', 'custom-woo-grid'),
                'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_responsive_control(
            'columns',
            [
                'label' => __('Columns', 'custom-woo-grid'),
                'type' => \Elementor\Controls_Manager::NUMBER,
                'default' => 3,
                'min' => 1,
                'max' => 6,
                'tablet_default' => 2,
                'mobile_default' => 1,
            ]
        );

        $this->add_control(
            'rows',
            [
                'label' => __('Rows', 'custom-woo-grid'),
                'type' => \Elementor\Controls_Manager::NUMBER,
                'default' => 3,
                'min' => 1,
                'max' => 10,
            ]
        );

        $this->end_controls_section();

        // Product Query Section
        $this->start_controls_section(
            'query_section',
            [
                'label' => __('Product Query', 'custom-woo-grid'),
                'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
            'products_filter',
            [
                'label' => __('Products Filter', 'custom-woo-grid'),
                'type' => \Elementor\Controls_Manager::SELECT,
                'default' => 'latest',
                'options' => [
                    'latest' => __('Latest Products', 'custom-woo-grid'),
                    'featured' => __('Featured Products', 'custom-woo-grid'),
                    'on_sale' => __('On Sale Products', 'custom-woo-grid'),
                    'in_stock' => __('In Stock Products', 'custom-woo-grid'),
                    'out_of_stock' => __('Out of Stock Products', 'custom-woo-grid'),
                    'top_rated' => __('Top Rated Products', 'custom-woo-grid'),
                    'best_selling' => __('Best Selling Products', 'custom-woo-grid'),
                    'popular' => __('Popular Products', 'custom-woo-grid'),
                    'recommended' => __('Recommended Products', 'custom-woo-grid'),
                    'cheapest' => __('Cheapest Products', 'custom-woo-grid'),
                    'low_stock' => __('Low Stock Products', 'custom-woo-grid'),
                    'expensive' => __('Most Expensive Products', 'custom-woo-grid'),
                    'manual' => __('Manual Selection', 'custom-woo-grid'),
                ],
            ]
        );

        $this->add_control(
            'order_by',
            [
                'label' => __('Order By', 'custom-woo-grid'),
                'type' => \Elementor\Controls_Manager::SELECT,
                'default' => 'date',
                'options' => [
                    'date' => __('Date', 'custom-woo-grid'),
                    'title' => __('Title', 'custom-woo-grid'),
                    'price' => __('Price', 'custom-woo-grid'),
                    'popularity' => __('Popularity', 'custom-woo-grid'),
                    'rating' => __('Rating', 'custom-woo-grid'),
                    'menu_order' => __('Menu Order', 'custom-woo-grid'),
                    'rand' => __('Random', 'custom-woo-grid'),
                ],
            ]
        );

        $this->add_control(
            'order',
            [
                'label' => __('Order', 'custom-woo-grid'),
                'type' => \Elementor\Controls_Manager::SELECT,
                'default' => 'desc',
                'options' => [
                    'desc' => __('DESC', 'custom-woo-grid'),
                    'asc' => __('ASC', 'custom-woo-grid'),
                ],
            ]
        );

        $this->add_control(
            'products_per_page',
            [
                'label' => __('Products Per Page', 'custom-woo-grid'),
                'type' => \Elementor\Controls_Manager::NUMBER,
                'default' => 12,
                'min' => 1,
                'max' => 100,
            ]
        );

        $this->add_control(
            'product_status',
            [
                'label' => __('Product Status', 'custom-woo-grid'),
                'type' => \Elementor\Controls_Manager::SELECT,
                'default' => 'publish',
                'options' => [
                    'publish' => __('Published', 'custom-woo-grid'),
                    'draft' => __('Draft', 'custom-woo-grid'),
                    'private' => __('Private', 'custom-woo-grid'),
                ],
            ]
        );

        // Category Selection
        $categories = get_terms(array(
            'taxonomy' => 'product_cat',
            'hide_empty' => false,
        ));
        
        $category_options = ['' => __('All Categories', 'custom-woo-grid')];
        if (!empty($categories) && !is_wp_error($categories)) {
            foreach ($categories as $category) {
                $category_options[$category->term_id] = $category->name;
            }
        }

        $this->add_control(
            'select_categories',
            [
                'label' => __('Select Categories', 'custom-woo-grid'),
                'type' => \Elementor\Controls_Manager::SELECT2,
                'multiple' => true,
                'options' => $category_options,
            ]
        );

        // Tags Selection
        $tags = get_terms(array(
            'taxonomy' => 'product_tag',
            'hide_empty' => false,
        ));
        
        $tag_options = ['' => __('All Tags', 'custom-woo-grid')];
        if (!empty($tags) && !is_wp_error($tags)) {
            foreach ($tags as $tag) {
                $tag_options[$tag->term_id] = $tag->name;
            }
        }

        $this->add_control(
            'select_tags',
            [
                'label' => __('Select Tags', 'custom-woo-grid'),
                'type' => \Elementor\Controls_Manager::SELECT2,
                'multiple' => true,
                'options' => $tag_options,
            ]
        );

        $this->add_control(
            'exclude_products',
            [
                'label' => __('Exclude Products', 'custom-woo-grid'),
                'type' => \Elementor\Controls_Manager::TEXT,
                'placeholder' => __('Product IDs (comma separated)', 'custom-woo-grid'),
                'description' => __('Enter product IDs to exclude, separated by commas', 'custom-woo-grid'),
            ]
        );

        $this->add_control(
            'date_filter',
            [
                'label' => __('Date Filter', 'custom-woo-grid'),
                'type' => \Elementor\Controls_Manager::SELECT,
                'default' => 'all',
                'options' => [
                    'all' => __('All Time', 'custom-woo-grid'),
                    'today' => __('Today', 'custom-woo-grid'),
                    'week' => __('This Week', 'custom-woo-grid'),
                    'month' => __('This Month', 'custom-woo-grid'),
                    'year' => __('This Year', 'custom-woo-grid'),
                ],
            ]
        );

        $this->end_controls_section();

        // Product Settings Section
        $this->start_controls_section(
            'product_settings_section',
            [
                'label' => __('Product Settings', 'custom-woo-grid'),
                'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
            'show_image',
            [
                'label' => __('Show Product Image', 'custom-woo-grid'),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'label_on' => __('Yes', 'custom-woo-grid'),
                'label_off' => __('No', 'custom-woo-grid'),
                'return_value' => 'yes',
                'default' => 'yes',
            ]
        );

        $this->add_control(
            'show_title',
            [
                'label' => __('Show Product Title', 'custom-woo-grid'),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'label_on' => __('Yes', 'custom-woo-grid'),
                'label_off' => __('No', 'custom-woo-grid'),
                'return_value' => 'yes',
                'default' => 'yes',
            ]
        );

        $this->add_control(
            'show_price',
            [
                'label' => __('Show Product Price', 'custom-woo-grid'),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'label_on' => __('Yes', 'custom-woo-grid'),
                'label_off' => __('No', 'custom-woo-grid'),
                'return_value' => 'yes',
                'default' => 'yes',
            ]
        );

        $this->add_control(
            'show_rating',
            [
                'label' => __('Show Product Rating', 'custom-woo-grid'),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'label_on' => __('Yes', 'custom-woo-grid'),
                'label_off' => __('No', 'custom-woo-grid'),
                'return_value' => 'yes',
                'default' => 'yes',
            ]
        );

        $this->add_control(
            'show_excerpt',
            [
                'label' => __('Show Product Excerpt', 'custom-woo-grid'),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'label_on' => __('Yes', 'custom-woo-grid'),
                'label_off' => __('No', 'custom-woo-grid'),
                'return_value' => 'yes',
                'default' => 'no',
            ]
        );

        $this->add_control(
            'excerpt_length',
            [
                'label' => __('Excerpt Length', 'custom-woo-grid'),
                'type' => \Elementor\Controls_Manager::NUMBER,
                'default' => 20,
                'min' => 5,
                'max' => 100,
                'condition' => [
                    'show_excerpt' => 'yes',
                ],
            ]
        );

        $this->add_control(
            'show_add_to_cart',
            [
                'label' => __('Show Add to Cart Button', 'custom-woo-grid'),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'label_on' => __('Yes', 'custom-woo-grid'),
                'label_off' => __('No', 'custom-woo-grid'),
                'return_value' => 'yes',
                'default' => 'yes',
            ]
        );

        $this->end_controls_section();

        // Sale/Stock Badge Section
        $this->start_controls_section(
            'badge_section',
            [
                'label' => __('Sale / Stock Badge', 'custom-woo-grid'),
                'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
            'show_sale_badge',
            [
                'label' => __('Show Sale Badge', 'custom-woo-grid'),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'label_on' => __('Yes', 'custom-woo-grid'),
                'label_off' => __('No', 'custom-woo-grid'),
                'return_value' => 'yes',
                'default' => 'yes',
            ]
        );

        $this->add_control(
            'show_stock_badge',
            [
                'label' => __('Show Stock Badge', 'custom-woo-grid'),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'label_on' => __('Yes', 'custom-woo-grid'),
                'label_off' => __('No', 'custom-woo-grid'),
                'return_value' => 'yes',
                'default' => 'yes',
            ]
        );

        $this->add_control(
            'show_featured_badge',
            [
                'label' => __('Show Featured Badge', 'custom-woo-grid'),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'label_on' => __('Yes', 'custom-woo-grid'),
                'label_off' => __('No', 'custom-woo-grid'),
                'return_value' => 'yes',
                'default' => 'yes',
            ]
        );

        $this->end_controls_section();

        // Product Actions Section
        $this->start_controls_section(
            'actions_section',
            [
                'label' => __('Product Actions', 'custom-woo-grid'),
                'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
            'show_quick_view',
            [
                'label' => __('Show Quick View', 'custom-woo-grid'),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'label_on' => __('Yes', 'custom-woo-grid'),
                'label_off' => __('No', 'custom-woo-grid'),
                'return_value' => 'yes',
                'default' => 'no',
            ]
        );

        $this->add_control(
            'show_wishlist',
            [
                'label' => __('Show Wishlist', 'custom-woo-grid'),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'label_on' => __('Yes', 'custom-woo-grid'),
                'label_off' => __('No', 'custom-woo-grid'),
                'return_value' => 'yes',
                'default' => 'no',
            ]
        );

        $this->add_control(
            'show_compare',
            [
                'label' => __('Show Compare', 'custom-woo-grid'),
                'type' => \Elementor\Controls_Manager::SWITCHER,
                'label_on' => __('Yes', 'custom-woo-grid'),
                'label_off' => __('No', 'custom-woo-grid'),
                'return_value' => 'yes',
                'default' => 'no',
            ]
        );

        $this->end_controls_section();

        // Pagination Section
        $this->start_controls_section(
            'pagination_section',
            [
                'label' => __('Pagination', 'custom-woo-grid'),
                'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
            ]
        );

        $this->add_control(
            'pagination_type',
            [
                'label' => __('Pagination Type', 'custom-woo-grid'),
                'type' => \Elementor\Controls_Manager::SELECT,
                'default' => 'none',
                'options' => [
                    'none' => __('None', 'custom-woo-grid'),
                    'numbers' => __('Numbers', 'custom-woo-grid'),
                    'prev_next' => __('Previous/Next', 'custom-woo-grid'),
                    'load_more' => __('Load More Button', 'custom-woo-grid'),
                    'infinite' => __('Infinite Scroll', 'custom-woo-grid'),
                ],
            ]
        );

        $this->end_controls_section();

        $this->register_style_controls();
    }

    protected function register_style_controls() {
        // Container Styles
        $this->start_controls_section(
            'container_style_section',
            [
                'label' => __('Container', 'custom-woo-grid'),
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_responsive_control(
            'container_gap',
            [
                'label' => __('Gap', 'custom-woo-grid'),
                'type' => \Elementor\Controls_Manager::SLIDER,
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 100,
                    ],
                ],
                'default' => [
                    'size' => 30,
                ],
                'selectors' => [
                    '{{WRAPPER}} .custom-woo-grid' => 'gap: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'container_padding',
            [
                'label' => __('Padding', 'custom-woo-grid'),
                'type' => \Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors' => [
                    '{{WRAPPER}} .custom-woo-grid' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_section();

        // Product Item Styles
        $this->start_controls_section(
            'item_style_section',
            [
                'label' => __('Product Item', 'custom-woo-grid'),
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Background::get_type(),
            [
                'name' => 'item_background',
                'label' => __('Background', 'custom-woo-grid'),
                'types' => ['classic', 'gradient'],
                'selector' => '{{WRAPPER}} .product-item',
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Border::get_type(),
            [
                'name' => 'item_border',
                'label' => __('Border', 'custom-woo-grid'),
                'selector' => '{{WRAPPER}} .product-item',
            ]
        );

        $this->add_control(
            'item_border_radius',
            [
                'label' => __('Border Radius', 'custom-woo-grid'),
                'type' => \Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%'],
                'selectors' => [
                    '{{WRAPPER}} .product-item' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'item_box_shadow',
                'label' => __('Box Shadow', 'custom-woo-grid'),
                'selector' => '{{WRAPPER}} .product-item',
            ]
        );

        $this->add_responsive_control(
            'item_padding',
            [
                'label' => __('Padding', 'custom-woo-grid'),
                'type' => \Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors' => [
                    '{{WRAPPER}} .product-item' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        // Hover Effects
        $this->add_control(
            'item_hover_effects',
            [
                'label' => __('Hover Effects', 'custom-woo-grid'),
                'type' => \Elementor\Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );

        $this->add_control(
            'item_hover_animation',
            [
                'label' => __('Hover Animation', 'custom-woo-grid'),
                'type' => \Elementor\Controls_Manager::SELECT,
                'default' => 'none',
                'options' => [
                    'none' => __('None', 'custom-woo-grid'),
                    'zoom' => __('Zoom In', 'custom-woo-grid'),
                    'zoom-out' => __('Zoom Out', 'custom-woo-grid'),
                    'move-up' => __('Move Up', 'custom-woo-grid'),
                    'move-down' => __('Move Down', 'custom-woo-grid'),
                ],
            ]
        );

        $this->end_controls_section();

        // Image Styles
        $this->start_controls_section(
            'image_style_section',
            [
                'label' => __('Product Image', 'custom-woo-grid'),
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
                'condition' => [
                    'show_image' => 'yes',
                ],
            ]
        );

        $this->add_responsive_control(
            'image_height',
            [
                'label' => __('Image Height', 'custom-woo-grid'),
                'type' => \Elementor\Controls_Manager::SLIDER,
                'range' => [
                    'px' => [
                        'min' => 100,
                        'max' => 500,
                    ],
                ],
                'default' => [
                    'size' => 250,
                ],
                'selectors' => [
                    '{{WRAPPER}} .product-image img' => 'height: {{SIZE}}{{UNIT}}; object-fit: cover;',
                ],
            ]
        );

        $this->add_control(
            'image_border_radius',
            [
                'label' => __('Border Radius', 'custom-woo-grid'),
                'type' => \Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%'],
                'selectors' => [
                    '{{WRAPPER}} .product-image img' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_section();

        // Title Styles
        $this->start_controls_section(
            'title_style_section',
            [
                'label' => __('Product Title', 'custom-woo-grid'),
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
                'condition' => [
                    'show_title' => 'yes',
                ],
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'title_typography',
                'label' => __('Typography', 'custom-woo-grid'),
                'selector' => '{{WRAPPER}} .product-title',
            ]
        );

        $this->add_control(
            'title_color',
            [
                'label' => __('Color', 'custom-woo-grid'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .product-title' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'title_hover_color',
            [
                'label' => __('Hover Color', 'custom-woo-grid'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .product-title:hover' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'title_margin',
            [
                'label' => __('Margin', 'custom-woo-grid'),
                'type' => \Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em'],
                'selectors' => [
                    '{{WRAPPER}} .product-title' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_section();

        // Price Styles
        $this->start_controls_section(
            'price_style_section',
            [
                'label' => __('Product Price', 'custom-woo-grid'),
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
                'condition' => [
                    'show_price' => 'yes',
                ],
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'price_typography',
                'label' => __('Typography', 'custom-woo-grid'),
                'selector' => '{{WRAPPER}} .product-price',
            ]
        );

        $this->add_control(
            'price_color',
            [
                'label' => __('Color', 'custom-woo-grid'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .product-price' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'sale_price_color',
            [
                'label' => __('Sale Price Color', 'custom-woo-grid'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .product-price .sale-price' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'price_margin',
            [
                'label' => __('Margin', 'custom-woo-grid'),
                'type' => \Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em'],
                'selectors' => [
                    '{{WRAPPER}} .product-price' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_section();

        // Button Styles
        $this->start_controls_section(
            'button_style_section',
            [
                'label' => __('Add to Cart Button', 'custom-woo-grid'),
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
                'condition' => [
                    'show_add_to_cart' => 'yes',
                ],
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'button_typography',
                'label' => __('Typography', 'custom-woo-grid'),
                'selector' => '{{WRAPPER}} .add-to-cart-btn',
            ]
        );

        $this->start_controls_tabs('button_tabs');

        $this->start_controls_tab(
            'button_normal',
            [
                'label' => __('Normal', 'custom-woo-grid'),
            ]
        );

        $this->add_control(
            'button_text_color',
            [
                'label' => __('Text Color', 'custom-woo-grid'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .add-to-cart-btn' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Background::get_type(),
            [
                'name' => 'button_background',
                'label' => __('Background', 'custom-woo-grid'),
                'types' => ['classic', 'gradient'],
                'selector' => '{{WRAPPER}} .add-to-cart-btn',
            ]
        );

        $this->end_controls_tab();

        $this->start_controls_tab(
            'button_hover',
            [
                'label' => __('Hover', 'custom-woo-grid'),
            ]
        );

        $this->add_control(
            'button_hover_color',
            [
                'label' => __('Text Color', 'custom-woo-grid'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .add-to-cart-btn:hover' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Background::get_type(),
            [
                'name' => 'button_background_hover',
                'label' => __('Background', 'custom-woo-grid'),
                'types' => ['classic', 'gradient'],
                'selector' => '{{WRAPPER}} .add-to-cart-btn:hover',
            ]
        );

        $this->end_controls_tab();

        $this->end_controls_tabs();

        $this->add_group_control(
            \Elementor\Group_Control_Border::get_type(),
            [
                'name' => 'button_border',
                'label' => __('Border', 'custom-woo-grid'),
                'selector' => '{{WRAPPER}} .add-to-cart-btn',
            ]
        );

        $this->add_control(
            'button_border_radius',
            [
                'label' => __('Border Radius', 'custom-woo-grid'),
                'type' => \Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%'],
                'selectors' => [
                    '{{WRAPPER}} .add-to-cart-btn' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'button_padding',
            [
                'label' => __('Padding', 'custom-woo-grid'),
                'type' => \Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em'],
                'selectors' => [
                    '{{WRAPPER}} .add-to-cart-btn' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'button_margin',
            [
                'label' => __('Margin', 'custom-woo-grid'),
                'type' => \Elementor\Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em'],
                'selectors' => [
                    '{{WRAPPER}} .add-to-cart-btn' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_section();

        // Badge Styles
        $this->start_controls_section(
            'badge_style_section',
            [
                'label' => __('Badges', 'custom-woo-grid'),
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'badge_typography',
                'label' => __('Typography', 'custom-woo-grid'),
                'selector' => '{{WRAPPER}} .product-badge',
            ]
        );

        $this->add_control(
            'sale_badge_color',
            [
                'label' => __('Sale Badge Color', 'custom-woo-grid'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .sale-badge' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'stock_badge_color',
            [
                'label' => __('Stock Badge Color', 'custom-woo-grid'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .stock-badge' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'featured_badge_color',
            [
                'label' => __('Featured Badge Color', 'custom-woo-grid'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .featured-badge' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->end_controls_section();

        // Pagination Styles
        $this->start_controls_section(
            'pagination_style_section',
            [
                'label' => __('Pagination', 'custom-woo-grid'),
                'tab' => \Elementor\Controls_Manager::TAB_STYLE,
                'condition' => [
                    'pagination_type!' => 'none',
                ],
            ]
        );

        $this->add_group_control(
            \Elementor\Group_Control_Typography::get_type(),
            [
                'name' => 'pagination_typography',
                'label' => __('Typography', 'custom-woo-grid'),
                'selector' => '{{WRAPPER}} .pagination a, {{WRAPPER}} .pagination span',
            ]
        );

        $this->add_control(
            'pagination_color',
            [
                'label' => __('Text Color', 'custom-woo-grid'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .pagination a' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'pagination_active_color',
            [
                'label' => __('Active Color', 'custom-woo-grid'),
                'type' => \Elementor\Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .pagination .current' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->end_controls_section();
    }

    protected function render() {
        $settings = $this->get_settings_for_display();
        
        $args = $this->build_query_args($settings);
        $products = new WP_Query($args);
        
        if (!$products->have_posts()) {
            echo '<p>' . __('No products found.', 'custom-woo-grid') . '</p>';
            return;
        }

        $columns = $settings['columns'] ?? 3;
        $tablet_columns = $settings['columns_tablet'] ?? 2;
        $mobile_columns = $settings['columns_mobile'] ?? 1;
        
        echo '<div class="custom-woo-grid" data-columns="' . $columns . '" data-tablet-columns="' . $tablet_columns . '" data-mobile-columns="' . $mobile_columns . '">';
        
        while ($products->have_posts()) {
            $products->the_post();
            $this->render_product_item($settings);
        }
        
        echo '</div>';
        
        if ($settings['pagination_type'] !== 'none') {
            $this->render_pagination($products, $settings);
        }
        
        wp_reset_postdata();
    }

    protected function build_query_args($settings) {
        $args = [
            'post_type' => 'product',
            'post_status' => $settings['product_status'],
            'posts_per_page' => $settings['products_per_page'],
            'orderby' => $settings['order_by'],
            'order' => $settings['order'],
            'meta_query' => [],
            'tax_query' => [],
        ];

        // Handle different product filters
        switch ($settings['products_filter']) {
            case 'featured':
                $args['meta_query'][] = [
                    'key' => '_featured',
                    'value' => 'yes',
                ];
                break;
            case 'on_sale':
                $args['meta_query'][] = [
                    'key' => '_sale_price',
                    'value' => '',
                    'compare' => '!=',
                ];
                break;
            case 'in_stock':
                $args['meta_query'][] = [
                    'key' => '_stock_status',
                    'value' => 'instock',
                ];
                break;
            case 'out_of_stock':
                $args['meta_query'][] = [
                    'key' => '_stock_status',
                    'value' => 'outofstock',
                ];
                break;
            case 'best_selling':
                $args['meta_key'] = 'total_sales';
                $args['orderby'] = 'meta_value_num';
                break;
            case 'top_rated':
                $args['meta_key'] = '_wc_average_rating';
                $args['orderby'] = 'meta_value_num';
                break;
            case 'cheapest':
                $args['meta_key'] = '_price';
                $args['orderby'] = 'meta_value_num';
                $args['order'] = 'ASC';
                break;
            case 'expensive':
                $args['meta_key'] = '_price';
                $args['orderby'] = 'meta_value_num';
                $args['order'] = 'DESC';
                break;
        }

        // Categories
        if (!empty($settings['select_categories'])) {
            $args['tax_query'][] = [
                'taxonomy' => 'product_cat',
                'field' => 'term_id',
                'terms' => $settings['select_categories'],
            ];
        }

        // Tags
        if (!empty($settings['select_tags'])) {
            $args['tax_query'][] = [
                'taxonomy' => 'product_tag',
                'field' => 'term_id',
                'terms' => $settings['select_tags'],
            ];
        }

        // Exclude products
        if (!empty($settings['exclude_products'])) {
            $exclude_ids = array_map('trim', explode(',', $settings['exclude_products']));
            $args['post__not_in'] = $exclude_ids;
        }

        // Date filter
        if ($settings['date_filter'] !== 'all') {
            $args['date_query'] = $this->get_date_query($settings['date_filter']);
        }

        return $args;
    }

    protected function get_date_query($filter) {
        switch ($filter) {
            case 'today':
                return [['after' => 'today']];
            case 'week':
                return [['after' => '1 week ago']];
            case 'month':
                return [['after' => '1 month ago']];
            case 'year':
                return [['after' => '1 year ago']];
            default:
                return [];
        }
    }

    protected function render_product_item($settings) {
        global $product;
        
        if (!$product) {
            return;
        }
        
        echo '<div class="product-item">';
        
        // Product badges
        if ($settings['show_sale_badge'] === 'yes' || $settings['show_stock_badge'] === 'yes' || $settings['show_featured_badge'] === 'yes') {
            echo '<div class="product-badges">';
            
            if ($settings['show_sale_badge'] === 'yes' && $product->is_on_sale()) {
                echo '<span class="product-badge sale-badge">' . __('SALE', 'custom-woo-grid') . '</span>';
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
        
        // Product excerpt
        if ($settings['show_excerpt'] === 'yes') {
            $excerpt_length = $settings['excerpt_length'] ?? 20;
            $excerpt = wp_trim_words(get_the_excerpt(), $excerpt_length);
            echo '<div class="product-excerpt">' . $excerpt . '</div>';
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
            echo '<div class="product-actions-row">';
            woocommerce_template_loop_add_to_cart();
            echo '</div>';
        }
        
        // Action buttons row
        if ($settings['show_quick_view'] === 'yes' || $settings['show_wishlist'] === 'yes' || $settings['show_compare'] === 'yes') {
            echo '<div class="action-buttons-row">';
            
            if ($settings['show_quick_view'] === 'yes') {
                echo '<button class="quick-view-btn" data-product-id="' . get_the_ID() . '">' . __('Quick View', 'custom-woo-grid') . '</button>';
            }
            
            if ($settings['show_wishlist'] === 'yes') {
                echo '<button class="wishlist-btn" data-product-id="' . get_the_ID() . '">' . __('♡ Wishlist', 'custom-woo-grid') . '</button>';
            }
            
            if ($settings['show_compare'] === 'yes') {
                echo '<button class="compare-btn" data-product-id="' . get_the_ID() . '">' . __('⚖ Compare', 'custom-woo-grid') . '</button>';
            }
            
            echo '</div>';
        }
        
        echo '</div>'; // .product-actions
        echo '</div>'; // .product-content
        echo '</div>'; // .product-item
    }

    protected function render_pagination($query, $settings) {
        if ($settings['pagination_type'] === 'none') {
            return;
        }
        
        echo '<div class="custom-woo-pagination">';
        
        switch ($settings['pagination_type']) {
            case 'numbers':
                echo paginate_links([
                    'total' => $query->max_num_pages,
                    'current' => max(1, get_query_var('paged')),
                    'format' => '?paged=%#%',
                    'show_all' => false,
                    'end_size' => 1,
                    'mid_size' => 2,
                    'prev_next' => true,
                    'prev_text' => __('« Previous', 'custom-woo-grid'),
                    'next_text' => __('Next »', 'custom-woo-grid'),
                ]);
                break;
                
            case 'prev_next':
                previous_posts_link(__('« Previous', 'custom-woo-grid'));
                next_posts_link(__('Next »', 'custom-woo-grid'), $query->max_num_pages);
                break;
                
            case 'load_more':
                echo '<button class="load-more-btn" data-page="1" data-max-pages="' . $query->max_num_pages . '">' . __('Load More', 'custom-woo-grid') . '</button>';
                break;
                
            case 'infinite':
                echo '<div class="infinite-scroll-trigger" data-page="1" data-max-pages="' . $query->max_num_pages . '"></div>';
                break;
        }
        
        echo '</div>';
    }
}