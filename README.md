# Custom WooCommerce Product Grid for Elementor

A comprehensive Elementor widget that provides advanced WooCommerce product grid functionality with extensive filtering, styling options, and modern features.

## Features

### Layout Options
- Responsive grid layout (1-6 columns)
- Customizable rows
- Mobile and tablet responsive settings

### Product Query Controls
- **Product Filters:**
  - Latest Products
  - Featured Products
  - On Sale Products
  - In Stock / Out of Stock Products
  - Top Rated Products
  - Best Selling Products
  - Popular Products
  - Recommended Products
  - Cheapest Products
  - Low Stock Products
  - Most Expensive Products
  - Manual Selection

### Advanced Filtering
- **Order By:** Date, Title, Price, Popularity, Rating, Menu Order, Random
- **Order:** ASC/DESC
- Products per page control
- Product status selection
- Category and tag filtering
- Product exclusion
- Date range filtering

### Product Display Settings
- Show/hide product image with height control
- Show/hide product title
- Show/hide product price
- Show/hide product rating
- Show/hide product excerpt with length control
- Show/hide add to cart button

### Badges & Labels
- Sale badge
- Stock status badge
- Featured product badge
- Custom styling for all badges

### Product Actions
- Quick view functionality
- Wishlist integration
- Product comparison
- Social sharing buttons

### Pagination Options
- None
- Numbers pagination
- Previous/Next pagination
- Load more button
- Infinite scroll

### Styling Controls
- **Container Styles:** Gap, padding, background
- **Product Item Styles:** Background, border, shadow, hover effects
- **Image Styles:** Height, border radius, hover animations
- **Typography Controls:** Title, price, buttons
- **Color Controls:** All text and background colors
- **Hover Animations:** Zoom, move, scale effects

## Installation

1. Upload the `custom-woo-product-grid` folder to `/wp-content/plugins/`
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Make sure Elementor and WooCommerce are installed and activated

## Requirements

- WordPress 5.0+
- Elementor 3.0+
- WooCommerce 5.0+
- PHP 7.4+

## File Structure

```
custom-woo-product-grid/
├── custom-woo-product-grid.php (Main plugin file)
├── assets/
│   ├── css/
│   │   └── style.css (Frontend styles)
│   └── js/
│       └── script.js (Frontend JavaScript)
├── includes/
│   ├── class-product-grid-widget.php (Main widget class)
│   └── ajax-handlers.php (AJAX functionality)
└── README.md
```

## Usage

1. Edit any page with Elementor
2. Search for "WooCommerce Product Grid" in the widgets panel
3. Drag and drop the widget to your desired location
4. Configure the settings in the widget panel:
   - **Layout:** Set columns and rows
   - **Product Query:** Choose filters and sorting
   - **Product Settings:** Toggle display elements
   - **Style:** Customize appearance

## Widget Controls Explained

### Layout Section
- **Columns:** Number of columns in the grid (responsive)
- **Rows:** Number of rows to display

### Product Query Section
- **Products Filter:** Choose which products to display
- **Order By:** Sort products by various criteria
- **Order:** Ascending or descending order
- **Products Per Page:** Limit number of products
- **Product Status:** Filter by post status
- **Select Categories:** Filter by product categories
- **Select Tags:** Filter by product tags
- **Exclude Products:** Comma-separated product IDs to exclude
- **Date Filter:** Filter products by publication date

### Product Settings Section
- Toggle visibility of product elements
- Control excerpt length
- Customize button text and behavior

### Sale/Stock Badge Section
- Enable/disable various product badges
- Customize badge appearance

### Product Actions Section
- Enable quick view modal
- Add wishlist functionality
- Include product comparison
- Social sharing options

### Pagination Section
- Choose pagination type
- Configure load more/infinite scroll

## Styling Options

### Container Styles
- Grid gap between products
- Container padding and margins
- Background colors and images

### Product Item Styles
- Individual product card styling
- Border and shadow effects
- Hover animations and transitions
- Background colors and gradients

### Typography
- Font family, size, weight for all text elements
- Text colors and hover states
- Line height and spacing

### Colors
- Primary and secondary color schemes
- Sale price highlighting
- Button colors and hover states
- Badge colors

### Responsive Design
- Tablet and mobile column settings
- Responsive typography
- Mobile-optimized layouts

## Advanced Features

### AJAX Functionality
- Add to cart without page reload
- Dynamic product loading
- Wishlist and compare actions
- Quick view modal

### Performance Optimization
- Lazy loading for images
- Efficient database queries
- Caching for repeated requests
- Optimized CSS and JavaScript

### Accessibility
- Keyboard navigation support
- Screen reader compatibility
- ARIA labels and roles
- High contrast support

### SEO Friendly
- Structured data markup
- Proper heading hierarchy
- Image alt attributes
- Clean HTML output

## Customization

### Hooks and Filters
The plugin provides various hooks for customization:

```php
// Filter product query arguments
add_filter('custom_woo_grid_query_args', 'my_custom_query_args');

// Modify product item HTML
add_filter('custom_woo_grid_item_html', 'my_custom_item_html');

// Add custom product actions
add_action('custom_woo_grid_after_product_actions', 'my_custom_actions');
```

### CSS Customization
Add custom CSS to override default styles:

```css
/* Custom product grid styles */
.custom-woo-grid .product-item {
    /* Your custom styles */
}
```

### JavaScript Customization
Extend functionality with custom JavaScript:

```javascript
// Custom product grid functionality
jQuery(document).ready(function($) {
    // Your custom code
});
```

## Troubleshooting

### Common Issues

1. **Products not showing:** Check WooCommerce settings and product visibility
2. **Styling issues:** Clear cache and check for theme conflicts
3. **AJAX not working:** Verify nonce and permissions
4. **Performance issues:** Optimize database and enable caching

### Debug Mode
Enable WordPress debug mode to troubleshoot issues:

```php
define('WP_DEBUG', true);
define('WP_DEBUG_LOG', true);
```

## Support

For support and feature requests, please visit our support forum or contact us directly.

## Changelog

### Version 1.0.0
- Initial release
- Complete product grid functionality
- All styling options implemented
- AJAX features working
- Mobile responsive design

## License

This plugin is licensed under GPL v2 or later.

## Credits

Developed with ❤️ for the WordPress and WooCommerce community.# wc-product-grid
