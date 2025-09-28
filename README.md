# Saudi Address for WooCommerce

A WordPress plugin that extends WooCommerce checkout to allow customers to provide Saudi Arabia national address information using the official Saudi National Address API.

## Features

- **Dynamic Address Fields**: Automatically loads regions, cities, and districts from the Saudi National Address API
- **Address Verification**: Optional address verification using building number, postal code, and additional number
- **Flexible Configuration**: Choose which fields to display and make them required or optional
- **Multi-language Support**: Support for both Arabic and English API responses
- **Admin Settings**: Easy configuration through WordPress admin panel
- **API Testing**: Built-in API connection testing
- **Responsive Design**: Mobile-friendly checkout experience

## Requirements

- WordPress 5.0 or higher
- WooCommerce 5.0 or higher
- PHP 7.4 or higher
- Saudi National Address API key (obtain from https://api.address.gov.sa/)

## Installation

1. Upload the plugin files to `/wp-content/plugins/saudi-address-woocommerce/` directory
2. Activate the plugin through the 'Plugins' screen in WordPress
3. Go to WooCommerce > Saudi Address to configure the plugin
4. Enter your Saudi National Address API key
5. Configure the fields you want to display on checkout

## Configuration

### API Settings

1. **API URL**: The Saudi National Address API endpoint (default: https://apina.address.gov.sa/NationalAddress/v3.1)
2. **API Key**: Your API key from https://api.address.gov.sa/

### General Settings

- **Enable Saudi Address**: Toggle to enable/disable the feature
- **Required Fields**: Make Saudi address fields mandatory for checkout
- **Language**: Choose between Arabic and English for API responses
- **Verify Address**: Enable address verification before checkout completion

### Field Settings

Choose which fields to display on the checkout page:

- Region
- City
- District
- Building Number
- Postal Code
- Additional Number
- Street
- Unit Number

## Usage

Once configured, customers will see Saudi address fields on the checkout page. The fields will dynamically load based on selections:

1. Customer selects a region
2. Cities for that region are automatically loaded
3. Customer selects a city
4. Districts for that city are automatically loaded
5. Customer fills in building number, postal code, and other details
6. Optional address verification can be enabled

## API Integration

The plugin integrates with the Saudi National Address API to provide:

- **Regions**: Get list of Saudi regions
- **Cities**: Get cities within a region
- **Districts**: Get districts within a city
- **Geocode**: Get address details by coordinates
- **Verify**: Verify address by building number, postal code, and additional number

## Hooks and Filters

### Actions

- `saudi_address_before_checkout_fields`: Before Saudi address fields are displayed
- `saudi_address_after_checkout_fields`: After Saudi address fields are displayed

### Filters

- `saudi_address_field_classes`: Modify CSS classes for address fields
- `saudi_address_required_fields`: Modify which fields are required
- `saudi_address_api_timeout`: Modify API request timeout (default: 30 seconds)

## Development

### File Structure

```
saudi-address-woocommerce/
├── saudi-address-woocommerce.php    # Main plugin file
├── includes/
│   ├── class-saudi-address-api.php      # API communication
│   ├── class-saudi-address-checkout.php # Checkout integration
│   ├── class-saudi-address-admin.php    # Admin settings
│   └── class-saudi-address-ajax.php    # AJAX handlers
├── assets/
│   ├── css/
│   │   ├── checkout.css                 # Checkout styles
│   │   └── admin.css                    # Admin styles
│   └── js/
│       ├── checkout.js                  # Checkout JavaScript
│       └── admin.js                     # Admin JavaScript
└── languages/                           # Translation files
```

### Adding Custom Fields

To add custom fields, use the `saudi_address_before_checkout_fields` action:

```php
add_action( 'saudi_address_before_checkout_fields', 'add_custom_field' );

function add_custom_field() {
    woocommerce_form_field( 'custom_field', array(
        'type'        => 'text',
        'class'       => array( 'form-row-wide' ),
        'label'       => __( 'Custom Field', 'saudi-address-woocommerce' ),
        'required'    => false,
    ) );
}
```

## Troubleshooting

### Common Issues

1. **API Connection Failed**
   - Verify your API key is correct
   - Check if the API URL is accessible
   - Ensure your server can make outbound HTTPS requests

2. **Fields Not Loading**
   - Check browser console for JavaScript errors
   - Verify AJAX requests are working
   - Check WordPress AJAX URL is correct

3. **Styling Issues**
   - Check if theme CSS is conflicting
   - Verify plugin CSS files are loading
   - Test with default WordPress theme

### Debug Mode

Enable WordPress debug mode to see detailed error messages:

```php
define( 'WP_DEBUG', true );
define( 'WP_DEBUG_LOG', true );
```

## Support

For support and feature requests, please contact the plugin developer or create an issue in the plugin repository.

## License

This plugin is licensed under the GPL v2 or later.

## Changelog

### 1.0.0
- Initial release
- Basic Saudi address integration
- Admin settings page
- Address verification
- Multi-language support
