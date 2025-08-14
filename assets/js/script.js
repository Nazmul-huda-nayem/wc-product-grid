jQuery(document).ready(function($) {
    'use strict';

    // Initialize the product grid
    initProductGrid();

    function initProductGrid() {
        // Set up responsive grid
        setupResponsiveGrid();
        
        // Initialize hover animations
        initHoverAnimations();
        
        // Setup AJAX functionality
        setupAjaxFunctionality();
        
        // Initialize quick view
        initQuickView();
        
        // Initialize wishlist functionality
        initWishlist();
        
        // Initialize compare functionality
        initCompare();
        
        // Initialize infinite scroll
        initInfiniteScroll();
        
        // Initialize load more
        initLoadMore();
        
        // Create quick view modal if it doesn't exist
        createQuickViewModal();
    }

    // Create Quick View Modal
    function createQuickViewModal() {
        if ($('.quick-view-modal').length === 0) {
            $('body').append(`
                <div class="quick-view-modal">
                    <div class="quick-view-content">
                        <span class="quick-view-close">&times;</span>
                        <div class="quick-view-product-details">
                            <div class="loading-spinner"></div>
                        </div>
                    </div>
                </div>
            `);
        }
    }

    // Responsive Grid Setup
    function setupResponsiveGrid() {
        $('.custom-woo-grid').each(function() {
            const $grid = $(this);
            const columns = $grid.data('columns') || 3;
            const tabletColumns = $grid.data('tablet-columns') || 2;
            const mobileColumns = $grid.data('mobile-columns') || 1;
            
            $grid.css('--columns', columns);
            $grid.css('--tablet-columns', tabletColumns);
            $grid.css('--mobile-columns', mobileColumns);
        });
    }

    // Hover Animations
    function initHoverAnimations() {
        $('.product-item').each(function() {
            const $item = $(this);
            const animation = $item.closest('[data-hover-animation]').data('hover-animation');
            
            if (animation && animation !== 'none') {
                $item.addClass('hover-' + animation);
            }
        });
    }

    // AJAX Functionality
    function setupAjaxFunctionality() {
        // Add to cart AJAX
        $(document).on('click', '.add_to_cart_button:not(.product_type_external)', function(e) {
            e.preventDefault();
            
            const $button = $(this);
            const productId = $button.data('product_id');
            
            if (!productId) return;
            
            $button.addClass('loading');
            $button.prop('disabled', true);
            
            // Add loading spinner
            const originalText = $button.text();
            $button.html('<span class="loading-spinner"></span>' + originalText);
            
            $.ajax({
                url: wc_add_to_cart_params.ajax_url,
                type: 'POST',
                data: {
                    action: 'woocommerce_add_to_cart',
                    product_id: productId,
                    quantity: $button.data('quantity') || 1,
                    variation_id: $button.data('variation_id') || 0,
                    security: wc_add_to_cart_params.add_to_cart_nonce
                },
                success: function(response) {
                    if (response.error) {
                        showNotification('Error: ' + response.error, 'error');
                    } else {
                        showNotification('Product added to cart!', 'success');
                        
                        // Update cart fragments
                        $(document.body).trigger('added_to_cart', [response.fragments, response.cart_hash, $button]);
                        
                        // Update button text
                        $button.text('Added to Cart');
                        $button.addClass('added');
                    }
                },
                error: function() {
                    showNotification('Something went wrong. Please try again.', 'error');
                },
                complete: function() {
                    $button.removeClass('loading');
                    $button.prop('disabled', false);
                    
                    setTimeout(function() {
                        $button.text(originalText);
                        $button.removeClass('added');
                    }, 3000);
                }
            });
        });
    }

    // Quick View Functionality
    function initQuickView() {
        // Quick view button click
        $(document).on('click', '.quick-view-btn', function(e) {
            e.preventDefault();
            
            const productId = $(this).data('product-id');
            if (!productId) {
                console.error('Product ID not found');
                return;
            }
            
            openQuickView(productId);
        });
        
        // Close quick view
        $(document).on('click', '.quick-view-close', function(e) {
            e.preventDefault();
            closeQuickView();
        });
        
        // Close on modal background click
        $(document).on('click', '.quick-view-modal', function(e) {
            if (e.target === this) {
                closeQuickView();
            }
        });
        
        // Close on escape key
        $(document).on('keyup', function(e) {
            if (e.keyCode === 27) { // Escape key
                closeQuickView();
            }
        });
    }

    function openQuickView(productId) {
        const $modal = $('.quick-view-modal');
        const $content = $('.quick-view-product-details');
        
        // Show modal and loading state
        $content.html('<div class="loading-spinner"></div><p>Loading product details...</p>');
        $modal.addClass('active');
        $('body').addClass('quick-view-open');
        
        // Check if AJAX object exists
        if (typeof custom_woo_grid_ajax === 'undefined') {
            console.error('AJAX object not found');
            $content.html('<p>Error: AJAX configuration missing. Please check plugin setup.</p>');
            return;
        }
        
        $.ajax({
            url: custom_woo_grid_ajax.ajax_url,
            type: 'POST',
            data: {
                action: 'get_quick_view_product',
                product_id: productId,
                nonce: custom_woo_grid_ajax.nonce
            },
            success: function(response) {
                console.log('Quick view response:', response);
                if (response.success && response.data && response.data.html) {
                    $content.html(response.data.html);
                } else {
                    const errorMsg = response.data && response.data.message ? response.data.message : 'Product not found.';
                    $content.html('<p>' + errorMsg + '</p>');
                }
            },
            error: function(xhr, status, error) {
                console.error('AJAX Error:', error);
                console.error('Status:', status);
                console.error('Response:', xhr.responseText);
                $content.html('<p>Error loading product details. Please try again.</p>');
            }
        });
    }

    function closeQuickView() {
        $('.quick-view-modal').removeClass('active');
        $('body').removeClass('quick-view-open');
    }

    // Wishlist Functionality
    function initWishlist() {
        $(document).on('click', '.wishlist-btn', function(e) {
            e.preventDefault();
            
            const $button = $(this);
            const productId = $button.data('product-id');
            
            if (!productId) {
                console.error('Product ID not found for wishlist');
                return;
            }
            
            toggleWishlist(productId, $button);
        });
    }

    function toggleWishlist(productId, $button) {
        const isAdded = $button.hasClass('added');
        const action = isAdded ? 'remove_from_wishlist' : 'add_to_wishlist';
        
        // Check if AJAX object exists
        if (typeof custom_woo_grid_ajax === 'undefined') {
            console.error('AJAX object not found');
            showNotification('Error: AJAX configuration missing.', 'error');
            return;
        }
        
        $button.prop('disabled', true);
        const originalText = $button.text();
        $button.html('<span class="loading-spinner"></span>');
        
        $.ajax({
            url: custom_woo_grid_ajax.ajax_url,
            type: 'POST',
            data: {
                action: action,
                product_id: productId,
                nonce: custom_woo_grid_ajax.nonce
            },
            success: function(response) {
                console.log('Wishlist response:', response);
                if (response.success) {
                    $button.toggleClass('added');
                    const newText = isAdded ? '♡ Wishlist' : '♥ Added';
                    $button.text(newText);
                    const message = isAdded ? 'Removed from wishlist' : 'Added to wishlist';
                    showNotification(message, 'success');
                } else {
                    const errorMsg = response.data && response.data.message ? response.data.message : 'Unknown error';
                    showNotification('Error: ' + errorMsg, 'error');
                    $button.text(originalText);
                }
            },
            error: function(xhr, status, error) {
                console.error('Wishlist AJAX Error:', error);
                showNotification('Something went wrong. Please try again.', 'error');
                $button.text(originalText);
            },
            complete: function() {
                $button.prop('disabled', false);
            }
        });
    }

    // Compare Functionality
    function initCompare() {
        $(document).on('click', '.compare-btn', function(e) {
            e.preventDefault();
            
            const $button = $(this);
            const productId = $button.data('product-id');
            
            if (!productId) {
                console.error('Product ID not found for compare');
                return;
            }
            
            toggleCompare(productId, $button);
        });
    }

    function toggleCompare(productId, $button) {
        const isAdded = $button.hasClass('added');
        const action = isAdded ? 'remove_from_compare' : 'add_to_compare';
        
        // Check if AJAX object exists
        if (typeof custom_woo_grid_ajax === 'undefined') {
            console.error('AJAX object not found');
            showNotification('Error: AJAX configuration missing.', 'error');
            return;
        }
        
        $button.prop('disabled', true);
        const originalText = $button.text();
        $button.html('<span class="loading-spinner"></span>');
        
        $.ajax({
            url: custom_woo_grid_ajax.ajax_url,
            type: 'POST',
            data: {
                action: action,
                product_id: productId,
                nonce: custom_woo_grid_ajax.nonce
            },
            success: function(response) {
                console.log('Compare response:', response);
                if (response.success) {
                    $button.toggleClass('added');
                    const newText = isAdded ? '⚖ Compare' : '⚖ Added';
                    $button.text(newText);
                    const message = isAdded ? 'Removed from compare' : 'Added to compare';
                    showNotification(message, 'success');
                } else {
                    const errorMsg = response.data && response.data.message ? response.data.message : 'Unknown error';
                    showNotification('Error: ' + errorMsg, 'error');
                    $button.text(originalText);
                }
            },
            error: function(xhr, status, error) {
                console.error('Compare AJAX Error:', error);
                showNotification('Something went wrong. Please try again.', 'error');
                $button.text(originalText);
            },
            complete: function() {
                $button.prop('disabled', false);
            }
        });
    }

    // Infinite Scroll
    function initInfiniteScroll() {
        const $trigger = $('.infinite-scroll-trigger');
        
        if ($trigger.length === 0) return;
        
        let loading = false;
        let page = parseInt($trigger.data('page')) || 1;
        const maxPages = parseInt($trigger.data('max-pages')) || 1;
        
        $(window).on('scroll', function() {
            if (loading || page >= maxPages) return;
            
            const triggerOffset = $trigger.offset().top;
            const scrollTop = $(window).scrollTop();
            const windowHeight = $(window).height();
            
            if (scrollTop + windowHeight >= triggerOffset - 100) {
                loading = true;
                page++;
                
                loadMoreProducts(page, function() {
                    loading = false;
                });
            }
        });
    }

    // Load More Button
    function initLoadMore() {
        $(document).on('click', '.load-more-btn', function(e) {
            e.preventDefault();
            
            const $button = $(this);
            let page = parseInt($button.data('page')) || 1;
            const maxPages = parseInt($button.data('max-pages')) || 1;
            
            if (page >= maxPages) {
                $button.hide();
                return;
            }
            
            page++;
            $button.data('page', page);
            
            const originalText = $button.text();
            $button.html('<span class="loading-spinner"></span>' + originalText);
            $button.prop('disabled', true);
            
            loadMoreProducts(page, function() {
                $button.text(originalText);
                $button.prop('disabled', false);
                
                if (page >= maxPages) {
                    $button.hide();
                }
            });
        });
    }

    // Load More Products Function
    function loadMoreProducts(page, callback) {
        const $grid = $('.custom-woo-grid');
        const $pagination = $('.custom-woo-pagination');
        
        // Get current widget settings
        const widgetId = $grid.closest('.elementor-widget').attr('data-id');
        
        $.ajax({
            url: custom_woo_grid_ajax.ajax_url,
            type: 'POST',
            data: {
                action: 'load_more_products',
                page: page,
                widget_id: widgetId,
                nonce: custom_woo_grid_ajax.nonce
            },
            success: function(response) {
                if (response.success && response.data.html) {
                    $grid.append(response.data.html);
                    
                    // Trigger animations for new items
                    const $newItems = $grid.find('.product-item').slice(-response.data.count);
                    animateNewItems($newItems);
                    
                    // Update pagination data
                    if (response.data.has_more === false) {
                        $pagination.find('.load-more-btn, .infinite-scroll-trigger').hide();
                    }
                } else {
                    showNotification('No more products to load.', 'info');
                }
            },
            error: function() {
                showNotification('Error loading products. Please try again.', 'error');
            },
            complete: function() {
                if (callback) callback();
            }
        });
    }

    // Animate New Items
    function animateNewItems($items) {
        $items.each(function(index) {
            const $item = $(this);
            $item.css('opacity', '0');
            
            setTimeout(function() {
                $item.animate({ opacity: 1 }, 300);
            }, index * 100);
        });
    }

    // Notification System
    function showNotification(message, type) {
        // Create notification container if it doesn't exist
        if ($('.woo-grid-notifications').length === 0) {
            $('body').append('<div class="woo-grid-notifications"></div>');
        }
        
        const $container = $('.woo-grid-notifications');
        const $notification = $(`
            <div class="notification notification-${type}">
                <span class="notification-message">${message}</span>
                <span class="notification-close">&times;</span>
            </div>
        `);
        
        $container.append($notification);
        
        // Auto remove after 5 seconds
        setTimeout(function() {
            $notification.fadeOut(300, function() {
                $(this).remove();
            });
        }, 5000);
        
        // Manual close
        $notification.find('.notification-close').on('click', function() {
            $notification.fadeOut(300, function() {
                $(this).remove();
            });
        });
    }

    // Product Filters (if implementing)
    function initProductFilters() {
        $(document).on('change', '.product-filters select', function() {
            filterProducts();
        });
        
        $(document).on('click', '.filter-reset', function(e) {
            e.preventDefault();
            $('.product-filters select').val('');
            filterProducts();
        });
    }

    function filterProducts() {
        const $grid = $('.custom-woo-grid');
        const $filters = $('.product-filters');
        
        if ($filters.length === 0) return;
        
        const filterData = {};
        $filters.find('select').each(function() {
            const name = $(this).attr('name');
            const value = $(this).val();
            if (value) {
                filterData[name] = value;
            }
        });
        
        $grid.addClass('loading');
        
        $.ajax({
            url: custom_woo_grid_ajax.ajax_url,
            type: 'POST',
            data: {
                action: 'filter_products',
                filters: filterData,
                nonce: custom_woo_grid_ajax.nonce
            },
            success: function(response) {
                if (response.success) {
                    $grid.html(response.data.html);
                } else {
                    showNotification('No products found with current filters.', 'info');
                }
            },
            error: function() {
                showNotification('Error filtering products. Please try again.', 'error');
            },
            complete: function() {
                $grid.removeClass('loading');
            }
        });
    }

    // Sale Percentage Calculator
    function calculateSalePercentage(regularPrice, salePrice) {
        const regular = parseFloat(regularPrice);
        const sale = parseFloat(salePrice);
        
        if (!regular || !sale || sale >= regular) return 0;
        
        const percentage = Math.round(((regular - sale) / regular) * 100);
        return percentage;
    }

    // Image Lazy Loading
    function initLazyLoading() {
        if ('IntersectionObserver' in window) {
            const imageObserver = new IntersectionObserver(function(entries, observer) {
                entries.forEach(function(entry) {
                    if (entry.isIntersecting) {
                        const img = entry.target;
                        img.src = img.dataset.src;
                        img.classList.remove('lazy');
                        imageObserver.unobserve(img);
                    }
                });
            });
            
            $('.product-image img.lazy').each(function() {
                imageObserver.observe(this);
            });
        } else {
            // Fallback for older browsers
            $('.product-image img.lazy').each(function() {
                const $img = $(this);
                $img.attr('src', $img.data('src'));
                $img.removeClass('lazy');
            });
        }
    }

    // Initialize lazy loading
    initLazyLoading();
    
    // Re-initialize on AJAX content load
    $(document).ajaxComplete(function() {
        initLazyLoading();
        setupResponsiveGrid();
        initHoverAnimations();
    });

    // Keyboard Navigation
    $(document).on('keydown', '.product-item', function(e) {
        if (e.keyCode === 13 || e.keyCode === 32) { // Enter or Space
            e.preventDefault();
            $(this).find('.product-title a')[0].click();
        }
    });

    // Make product items focusable for accessibility
    $('.product-item').attr('tabindex', '0');
    
    // Screen reader announcements
    function announceToScreenReader(message) {
        const $announcement = $('<div class="sr-only" aria-live="polite"></div>');
        $announcement.text(message);
        $('body').append($announcement);
        
        setTimeout(function() {
            $announcement.remove();
        }, 1000);
    }
});