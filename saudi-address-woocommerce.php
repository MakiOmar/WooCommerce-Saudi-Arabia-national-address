<?php
/**
 * Plugin Name: Saudi Address for WooCommerce
 * Plugin URI: https://github.com/your-repo/saudi-address-woocommerce
 * Description: Extends WooCommerce checkout to allow customers to provide Saudi Arabia national address information.
 * Version: 1.0.0
 * Author: Your Name
 * Author URI: https://yourwebsite.com
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: saudi-address-woocommerce
 * Domain Path: /languages
 * Requires at least: 5.0
 * Tested up to: 6.4
 * Requires PHP: 7.4
 * WC requires at least: 5.0
 * WC tested up to: 8.0
 */

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Define plugin constants
define( 'SAUDI_ADDRESS_WC_VERSION', '1.0.0' );
define( 'SAUDI_ADDRESS_WC_PLUGIN_FILE', __FILE__ );
define( 'SAUDI_ADDRESS_WC_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'SAUDI_ADDRESS_WC_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

/**
 * Check if WooCommerce is active
 */
function saudi_address_wc_check_woocommerce() {
    if ( ! class_exists( 'WooCommerce' ) ) {
        add_action( 'admin_notices', 'saudi_address_wc_woocommerce_missing_notice' );
        return false;
    }
    return true;
}

/**
 * Display notice if WooCommerce is not active
 */
function saudi_address_wc_woocommerce_missing_notice() {
    ?>
    <div class="notice notice-error">
        <p><?php esc_html_e( 'Saudi Address for WooCommerce requires WooCommerce to be installed and active.', 'saudi-address-woocommerce' ); ?></p>
    </div>
    <?php
}

/**
 * Main plugin class
 */
class Saudi_Address_WooCommerce {
    
    /**
     * Single instance of the class
     */
    private static $instance = null;
    
    /**
     * Get single instance
     */
    public static function get_instance() {
        if ( null === self::$instance ) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    /**
     * Constructor
     */
    private function __construct() {
        add_action( 'plugins_loaded', array( $this, 'init' ) );
    }
    
    /**
     * Initialize plugin
     */
    public function init() {
        // Check if WooCommerce is active
        if ( ! saudi_address_wc_check_woocommerce() ) {
            return;
        }
        
        // Load text domain
        load_plugin_textdomain( 'saudi-address-woocommerce', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
        
        // Include required files
        $this->includes();
        
        // Initialize hooks
        $this->init_hooks();
    }
    
    /**
     * Include required files
     */
    private function includes() {
        require_once SAUDI_ADDRESS_WC_PLUGIN_DIR . 'includes/class-saudi-address-api.php';
        require_once SAUDI_ADDRESS_WC_PLUGIN_DIR . 'includes/class-saudi-address-checkout.php';
        require_once SAUDI_ADDRESS_WC_PLUGIN_DIR . 'includes/class-saudi-address-admin.php';
        require_once SAUDI_ADDRESS_WC_PLUGIN_DIR . 'includes/class-saudi-address-ajax.php';
    }
    
    /**
     * Initialize hooks
     */
    private function init_hooks() {
        // Initialize classes
        new Saudi_Address_Checkout();
        new Saudi_Address_Admin();
        new Saudi_Address_Ajax();
        
        // Activation and deactivation hooks
        register_activation_hook( __FILE__, array( $this, 'activate' ) );
        register_deactivation_hook( __FILE__, array( $this, 'deactivate' ) );
    }
    
    /**
     * Plugin activation
     */
    public function activate() {
        // Add any activation tasks here
    }
    
    /**
     * Plugin deactivation
     */
    public function deactivate() {
        // Add any deactivation tasks here
    }
}

// Initialize the plugin
Saudi_Address_WooCommerce::get_instance();
