<?php
/**
 * Saudi Address Admin Class
 *
 * Handles admin settings and configuration
 *
 * @package Saudi_Address_WooCommerce
 * @since 1.0.0
 */

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Saudi Address Admin Class
 */
class Saudi_Address_Admin {
    
    /**
     * Constructor
     */
    public function __construct() {
        add_action( 'admin_menu', array( $this, 'add_admin_menu' ) );
        add_action( 'admin_init', array( $this, 'register_settings' ) );
        add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_scripts' ) );
    }
    
    /**
     * Add admin menu
     */
    public function add_admin_menu() {
        add_submenu_page(
            'woocommerce',
            __( 'Saudi Address Settings', 'saudi-address-woocommerce' ),
            __( 'Saudi Address', 'saudi-address-woocommerce' ),
            'manage_woocommerce',
            'saudi-address-settings',
            array( $this, 'admin_page' )
        );
        
        // Add debug page
        add_submenu_page(
            'woocommerce',
            __( 'Saudi Address Debug', 'saudi-address-woocommerce' ),
            __( 'Saudi Address Debug', 'saudi-address-woocommerce' ),
            'manage_woocommerce',
            'saudi-address-debug',
            array( $this, 'debug_page' )
        );
    }
    
    /**
     * Register settings
     */
    public function register_settings() {
        // API Settings
        register_setting( 'saudi_address_settings', 'saudi_address_api_url' );
        register_setting( 'saudi_address_settings', 'saudi_address_api_key' );
        
        // General Settings
        register_setting( 'saudi_address_settings', 'saudi_address_enabled' );
        register_setting( 'saudi_address_settings', 'saudi_address_required' );
        register_setting( 'saudi_address_settings', 'saudi_address_language' );
        register_setting( 'saudi_address_settings', 'saudi_address_verify_address' );
        
        // Field Settings
        register_setting( 'saudi_address_settings', 'saudi_address_show_region' );
        register_setting( 'saudi_address_settings', 'saudi_address_show_city' );
        register_setting( 'saudi_address_settings', 'saudi_address_show_district' );
        register_setting( 'saudi_address_settings', 'saudi_address_show_building_number' );
        register_setting( 'saudi_address_settings', 'saudi_address_show_postal_code' );
        register_setting( 'saudi_address_settings', 'saudi_address_show_additional_number' );
        register_setting( 'saudi_address_settings', 'saudi_address_show_street' );
        register_setting( 'saudi_address_settings', 'saudi_address_show_unit_number' );
    }
    
    /**
     * Enqueue admin scripts
     */
    public function enqueue_admin_scripts( $hook ) {
        if ( 'woocommerce_page_saudi-address-settings' !== $hook ) {
            return;
        }
        
        wp_enqueue_script( 'saudi-address-admin', SAUDI_ADDRESS_WC_PLUGIN_URL . 'assets/js/admin.js', array( 'jquery' ), SAUDI_ADDRESS_WC_VERSION, true );
        wp_enqueue_style( 'saudi-address-admin', SAUDI_ADDRESS_WC_PLUGIN_URL . 'assets/css/admin.css', array(), SAUDI_ADDRESS_WC_VERSION );
        
        // Localize script for admin
        wp_localize_script( 'saudi-address-admin', 'saudi_address_admin_ajax', array(
            'ajax_url' => admin_url( 'admin-ajax.php' ),
            'nonce'    => wp_create_nonce( 'saudi_address_admin_nonce' ),
        ) );
    }
    
    /**
     * Admin page
     */
    public function admin_page() {
        ?>
        <div class="wrap">
            <h1><?php esc_html_e( 'Saudi Address Settings', 'saudi-address-woocommerce' ); ?></h1>
            
            <form method="post" action="options.php">
                <?php
                settings_fields( 'saudi_address_settings' );
                do_settings_sections( 'saudi_address_settings' );
                ?>
                
                <table class="form-table">
                    <tr>
                        <th scope="row">
                            <label for="saudi_address_api_url"><?php esc_html_e( 'API URL', 'saudi-address-woocommerce' ); ?></label>
                        </th>
                        <td>
                            <input type="url" id="saudi_address_api_url" name="saudi_address_api_url" 
                                   value="<?php echo esc_attr( get_option( 'saudi_address_api_url', 'https://apina.address.gov.sa/NationalAddress/v3.1' ) ); ?>" 
                                   class="regular-text" />
                            <p class="description">
                                <?php esc_html_e( 'The Saudi National Address API URL. Default: https://apina.address.gov.sa/NationalAddress/v3.1', 'saudi-address-woocommerce' ); ?>
                            </p>
                        </td>
                    </tr>
                    
                    <tr>
                        <th scope="row">
                            <label for="saudi_address_api_key"><?php esc_html_e( 'API Key', 'saudi-address-woocommerce' ); ?></label>
                        </th>
                        <td>
                            <input type="text" id="saudi_address_api_key" name="saudi_address_api_key" 
                                   value="<?php echo esc_attr( get_option( 'saudi_address_api_key', '' ) ); ?>" 
                                   class="regular-text" />
                            <p class="description">
                                <?php 
                                printf( 
                                    __( 'Get your API key from %s', 'saudi-address-woocommerce' ), 
                                    '<a href="https://api.address.gov.sa/" target="_blank">https://api.address.gov.sa/</a>' 
                                ); 
                                ?>
                            </p>
                        </td>
                    </tr>
                    
                    <tr>
                        <th scope="row">
                            <label for="saudi_address_enabled"><?php esc_html_e( 'Enable Saudi Address', 'saudi-address-woocommerce' ); ?></label>
                        </th>
                        <td>
                            <input type="checkbox" id="saudi_address_enabled" name="saudi_address_enabled" 
                                   value="yes" <?php checked( get_option( 'saudi_address_enabled', 'yes' ), 'yes' ); ?> />
                            <label for="saudi_address_enabled"><?php esc_html_e( 'Enable Saudi address fields on checkout', 'saudi-address-woocommerce' ); ?></label>
                        </td>
                    </tr>
                    
                    <tr>
                        <th scope="row">
                            <label for="saudi_address_required"><?php esc_html_e( 'Required Fields', 'saudi-address-woocommerce' ); ?></label>
                        </th>
                        <td>
                            <input type="checkbox" id="saudi_address_required" name="saudi_address_required" 
                                   value="yes" <?php checked( get_option( 'saudi_address_required', 'yes' ), 'yes' ); ?> />
                            <label for="saudi_address_required"><?php esc_html_e( 'Make Saudi address fields required', 'saudi-address-woocommerce' ); ?></label>
                        </td>
                    </tr>
                    
                    <tr>
                        <th scope="row">
                            <label for="saudi_address_language"><?php esc_html_e( 'Language', 'saudi-address-woocommerce' ); ?></label>
                        </th>
                        <td>
                            <select id="saudi_address_language" name="saudi_address_language">
                                <option value="A" <?php selected( get_option( 'saudi_address_language', 'A' ), 'A' ); ?>><?php esc_html_e( 'Arabic', 'saudi-address-woocommerce' ); ?></option>
                                <option value="E" <?php selected( get_option( 'saudi_address_language', 'A' ), 'E' ); ?>><?php esc_html_e( 'English', 'saudi-address-woocommerce' ); ?></option>
                            </select>
                            <p class="description">
                                <?php esc_html_e( 'Language for API responses', 'saudi-address-woocommerce' ); ?>
                            </p>
                        </td>
                    </tr>
                    
                    <tr>
                        <th scope="row">
                            <label for="saudi_address_verify_address"><?php esc_html_e( 'Verify Address', 'saudi-address-woocommerce' ); ?></label>
                        </th>
                        <td>
                            <input type="checkbox" id="saudi_address_verify_address" name="saudi_address_verify_address" 
                                   value="yes" <?php checked( get_option( 'saudi_address_verify_address', 'no' ), 'yes' ); ?> />
                            <label for="saudi_address_verify_address"><?php esc_html_e( 'Verify address using API before checkout', 'saudi-address-woocommerce' ); ?></label>
                            <p class="description">
                                <?php esc_html_e( 'This will verify the address using the Saudi National Address API before allowing checkout completion.', 'saudi-address-woocommerce' ); ?>
                            </p>
                        </td>
                    </tr>
                </table>
                
                <h2><?php esc_html_e( 'Field Settings', 'saudi-address-woocommerce' ); ?></h2>
                <p><?php esc_html_e( 'Choose which fields to display on the checkout page.', 'saudi-address-woocommerce' ); ?></p>
                
                <table class="form-table">
                    <tr>
                        <th scope="row"><?php esc_html_e( 'Available Fields', 'saudi-address-woocommerce' ); ?></th>
                        <td>
                            <fieldset>
                                <label>
                                    <input type="checkbox" name="saudi_address_show_region" value="yes" 
                                           <?php checked( get_option( 'saudi_address_show_region', 'yes' ), 'yes' ); ?> />
                                    <?php esc_html_e( 'Region', 'saudi-address-woocommerce' ); ?>
                                </label><br/>
                                
                                <label>
                                    <input type="checkbox" name="saudi_address_show_city" value="yes" 
                                           <?php checked( get_option( 'saudi_address_show_city', 'yes' ), 'yes' ); ?> />
                                    <?php esc_html_e( 'City', 'saudi-address-woocommerce' ); ?>
                                </label><br/>
                                
                                <label>
                                    <input type="checkbox" name="saudi_address_show_district" value="yes" 
                                           <?php checked( get_option( 'saudi_address_show_district', 'yes' ), 'yes' ); ?> />
                                    <?php esc_html_e( 'District', 'saudi-address-woocommerce' ); ?>
                                </label><br/>
                                
                                <label>
                                    <input type="checkbox" name="saudi_address_show_building_number" value="yes" 
                                           <?php checked( get_option( 'saudi_address_show_building_number', 'yes' ), 'yes' ); ?> />
                                    <?php esc_html_e( 'Building Number', 'saudi-address-woocommerce' ); ?>
                                </label><br/>
                                
                                <label>
                                    <input type="checkbox" name="saudi_address_show_postal_code" value="yes" 
                                           <?php checked( get_option( 'saudi_address_show_postal_code', 'yes' ), 'yes' ); ?> />
                                    <?php esc_html_e( 'Postal Code', 'saudi-address-woocommerce' ); ?>
                                </label><br/>
                                
                                <label>
                                    <input type="checkbox" name="saudi_address_show_additional_number" value="yes" 
                                           <?php checked( get_option( 'saudi_address_show_additional_number', 'no' ), 'yes' ); ?> />
                                    <?php esc_html_e( 'Additional Number', 'saudi-address-woocommerce' ); ?>
                                </label><br/>
                                
                                <label>
                                    <input type="checkbox" name="saudi_address_show_street" value="yes" 
                                           <?php checked( get_option( 'saudi_address_show_street', 'no' ), 'yes' ); ?> />
                                    <?php esc_html_e( 'Street', 'saudi-address-woocommerce' ); ?>
                                </label><br/>
                                
                                <label>
                                    <input type="checkbox" name="saudi_address_show_unit_number" value="yes" 
                                           <?php checked( get_option( 'saudi_address_show_unit_number', 'no' ), 'yes' ); ?> />
                                    <?php esc_html_e( 'Unit Number', 'saudi-address-woocommerce' ); ?>
                                </label><br/>
                            </fieldset>
                        </td>
                    </tr>
                </table>
                
                <?php submit_button(); ?>
            </form>
            
            <div class="saudi-address-test-section">
                <h2><?php esc_html_e( 'API Test', 'saudi-address-woocommerce' ); ?></h2>
                <p><?php esc_html_e( 'Test your API configuration by clicking the button below.', 'saudi-address-woocommerce' ); ?></p>
                <button type="button" id="test-api-connection" class="button button-secondary">
                    <?php esc_html_e( 'Test API Connection', 'saudi-address-woocommerce' ); ?>
                </button>
                <div id="api-test-result"></div>
            </div>
        </div>
        <?php
    }
    
    /**
     * Debug page
     */
    public function debug_page() {
        // Handle quick fix action
        if ( isset( $_POST['saudi_address_debug_action'] ) && check_admin_referer( 'saudi_address_debug_action' ) ) {
            if ( $_POST['saudi_address_debug_action'] === 'set_defaults' ) {
                update_option( 'saudi_address_enabled', 'yes' );
                update_option( 'saudi_address_required', 'yes' );
                update_option( 'saudi_address_language', 'A' );
                update_option( 'saudi_address_verify_address', 'no' );
                update_option( 'saudi_address_show_region', 'yes' );
                update_option( 'saudi_address_show_city', 'yes' );
                update_option( 'saudi_address_show_district', 'yes' );
                update_option( 'saudi_address_show_building_number', 'yes' );
                update_option( 'saudi_address_show_postal_code', 'yes' );
                update_option( 'saudi_address_show_additional_number', 'no' );
                update_option( 'saudi_address_show_street', 'no' );
                update_option( 'saudi_address_show_unit_number', 'no' );
                update_option( 'saudi_address_api_url', 'https://apina.address.gov.sa/NationalAddress/v3.1' );
                
                echo '<div class="notice notice-success"><p><strong>Success!</strong> Default options have been set.</p></div>';
            }
        }
        ?>
        <div class="wrap">
            <h1><?php esc_html_e( 'Saudi Address Debug', 'saudi-address-woocommerce' ); ?></h1>
            
            <div style="background: #fff; padding: 20px; margin-top: 20px; border: 1px solid #ccc;">
                <h2>1. Plugin Status</h2>
                <table class="widefat">
                    <thead>
                        <tr>
                            <th>Check</th>
                            <th>Status</th>
                            <th>Details</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><strong>WordPress Version</strong></td>
                            <td><span style="color: green;">✓</span></td>
                            <td><?php echo esc_html( get_bloginfo( 'version' ) ); ?></td>
                        </tr>
                        <tr>
                            <td><strong>WooCommerce Active</strong></td>
                            <td>
                                <?php if ( class_exists( 'WooCommerce' ) ) : ?>
                                    <span style="color: green;">✓</span>
                                <?php else : ?>
                                    <span style="color: red;">✗</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php 
                                if ( class_exists( 'WooCommerce' ) ) {
                                    echo 'Version: ' . esc_html( WC()->version );
                                } else {
                                    echo '<span style="color: red;">WooCommerce is NOT active!</span>';
                                }
                                ?>
                            </td>
                        </tr>
                        <tr>
                            <td><strong>Saudi Address Plugin</strong></td>
                            <td>
                                <?php if ( class_exists( 'Saudi_Address_WooCommerce' ) ) : ?>
                                    <span style="color: green;">✓</span>
                                <?php else : ?>
                                    <span style="color: red;">✗</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php 
                                if ( defined( 'SAUDI_ADDRESS_WC_VERSION' ) ) {
                                    echo 'Version: ' . esc_html( SAUDI_ADDRESS_WC_VERSION );
                                } else {
                                    echo '<span style="color: red;">Plugin class not found!</span>';
                                }
                                ?>
                            </td>
                        </tr>
                        <tr>
                            <td><strong>Checkout Class</strong></td>
                            <td>
                                <?php if ( class_exists( 'Saudi_Address_Checkout' ) ) : ?>
                                    <span style="color: green;">✓</span>
                                <?php else : ?>
                                    <span style="color: red;">✗</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php 
                                echo class_exists( 'Saudi_Address_Checkout' ) ? 'Class loaded' : '<span style="color: red;">Class NOT found!</span>';
                                ?>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            
            <div style="background: #fff; padding: 20px; margin-top: 20px; border: 1px solid #ccc;">
                <h2>2. Plugin Settings</h2>
                <table class="widefat">
                    <thead>
                        <tr>
                            <th>Option Name</th>
                            <th>Current Value</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $options = array(
                            'saudi_address_enabled'                => 'Enable Saudi Address',
                            'saudi_address_required'               => 'Required Fields',
                            'saudi_address_language'               => 'Language',
                            'saudi_address_show_region'            => 'Show Region',
                            'saudi_address_show_city'              => 'Show City',
                            'saudi_address_show_district'          => 'Show District',
                            'saudi_address_show_building_number'   => 'Show Building Number',
                            'saudi_address_show_postal_code'       => 'Show Postal Code',
                            'saudi_address_api_key'                => 'API Key',
                        );
                        
                        foreach ( $options as $option => $label ) {
                            $value = get_option( $option );
                            $is_set = ( $value !== false && $value !== '' );
                            
                            if ( $option === 'saudi_address_api_key' ) {
                                $display_value = $is_set ? '****** (SET)' : '<span style="color: red;">(NOT SET)</span>';
                            } else {
                                $display_value = $is_set ? esc_html( $value ) : '<span style="color: red;">(NOT SET)</span>';
                            }
                            
                            $status_color = $is_set ? 'green' : 'orange';
                            $status_text = $is_set ? '✓' : '⚠';
                            
                            // Special check for enabled
                            if ( $option === 'saudi_address_enabled' && $value !== 'yes' ) {
                                $status_color = 'red';
                                $status_text = '✗ DISABLED';
                            }
                            
                            echo '<tr>';
                            echo '<td><strong>' . esc_html( $label ) . '</strong><br/><small>' . esc_html( $option ) . '</small></td>';
                            echo '<td>' . $display_value . '</td>';
                            echo '<td><span style="color: ' . $status_color . ';">' . $status_text . '</span></td>';
                            echo '</tr>';
                        }
                        ?>
                    </tbody>
                </table>
            </div>
            
            <div style="background: #fff; padding: 20px; margin-top: 20px; border: 1px solid #ccc;">
                <h2>3. Test Field Generation</h2>
                <p>Attempting to generate fields manually:</p>
                <div style="background: #f5f5f5; padding: 15px; border: 1px solid #ddd;">
                    <?php
                    if ( class_exists( 'Saudi_Address_Checkout' ) && class_exists( 'WC_Checkout' ) ) {
                        $checkout_obj = new Saudi_Address_Checkout();
                        $wc_checkout = WC()->checkout();
                        
                        ob_start();
                        $checkout_obj->add_saudi_address_fields( $wc_checkout );
                        $output = ob_get_clean();
                        
                        if ( ! empty( $output ) ) {
                            echo '<p style="color: green;"><strong>✓ SUCCESS!</strong> Fields are being generated. Here\'s a preview:</p>';
                            echo '<div style="max-height: 300px; overflow: auto; background: white; padding: 10px; border: 1px solid #ccc;">';
                            echo $output;
                            echo '</div>';
                        } else {
                            echo '<p style="color: red;"><strong>✗ ERROR!</strong> No fields were generated.</p>';
                            echo '<p>This usually means:</p>';
                            echo '<ul>';
                            echo '<li>The plugin is disabled (saudi_address_enabled is not "yes")</li>';
                            echo '<li>There is a PHP error preventing execution</li>';
                            echo '</ul>';
                        }
                    } else {
                        echo '<p style="color: red;"><strong>✗ ERROR!</strong> Required classes not found.</p>';
                    }
                    ?>
                </div>
            </div>
            
            <div style="background: #fff; padding: 20px; margin-top: 20px; border: 1px solid #ccc;">
                <h2>4. Quick Actions</h2>
                <form method="post">
                    <?php wp_nonce_field( 'saudi_address_debug_action' ); ?>
                    <input type="hidden" name="saudi_address_debug_action" value="set_defaults" />
                    <p>
                        <button type="submit" class="button button-primary button-large">
                            Set Default Options Now
                        </button>
                    </p>
                    <p class="description">
                        This will set all plugin options to their default values (enabled, required, Arabic language, etc.)
                    </p>
                </form>
            </div>
            
            <div style="background: #fffbcc; padding: 20px; margin-top: 20px; border: 1px solid #e6db55;">
                <h2>5. Next Steps</h2>
                <ol>
                    <li><strong>If fields are NOT showing in the test above:</strong> Click "Set Default Options Now"</li>
                    <li><strong>Clear your site cache</strong> (if using any caching plugin)</li>
                    <li><strong>Go to the checkout page</strong> and check if fields appear</li>
                    <li><strong>If still not working:</strong> Check for JavaScript errors in browser console (F12)</li>
                </ol>
            </div>
        </div>
        <?php
    }
}
