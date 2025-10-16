<?php
/**
 * Saudi Address AJAX Class
 *
 * Handles AJAX requests for dynamic field loading
 *
 * @package Saudi_Address_WooCommerce
 * @since 1.0.0
 */

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Saudi Address AJAX Class
 */
class Saudi_Address_Ajax {
    
    /**
     * Constructor
     */
    public function __construct() {
        add_action( 'wp_ajax_saudi_address_get_cities', array( $this, 'get_cities' ) );
        add_action( 'wp_ajax_nopriv_saudi_address_get_cities', array( $this, 'get_cities' ) );
        
        add_action( 'wp_ajax_saudi_address_get_districts', array( $this, 'get_districts' ) );
        add_action( 'wp_ajax_nopriv_saudi_address_get_districts', array( $this, 'get_districts' ) );
        
        add_action( 'wp_ajax_saudi_address_verify_address', array( $this, 'verify_address' ) );
        add_action( 'wp_ajax_nopriv_saudi_address_verify_address', array( $this, 'verify_address' ) );
        
        add_action( 'wp_ajax_saudi_address_test_api', array( $this, 'test_api' ) );
    }
    
    /**
     * Get cities for a region
     */
    public function get_cities() {
        // Verify nonce
        if ( ! wp_verify_nonce( $_POST['nonce'], 'saudi_address_nonce' ) ) {
            wp_die( esc_html__( 'Security check failed', 'saudi-address-woocommerce' ) );
        }
        
        $region_id = isset( $_POST['region_id'] ) ? intval( $_POST['region_id'] ) : 0;
        $language = isset( $_POST['language'] ) ? sanitize_text_field( $_POST['language'] ) : 'A';
        
        // Validate language parameter
        if ( ! in_array( $language, array( 'A', 'E' ), true ) ) {
            $language = 'A';
        }
        
        $api = new Saudi_Address_API();
        $cities = $api->get_cities( $region_id, $language );
        
        if ( $cities ) {
            $options = array();
            foreach ( $cities as $city ) {
                $options[] = array(
                    'id'   => sanitize_text_field( $city->Id ),
                    'name' => sanitize_text_field( $city->Name ),
                );
            }
            wp_send_json_success( $options );
        } else {
            wp_send_json_error( esc_html__( 'Failed to load cities', 'saudi-address-woocommerce' ) );
        }
    }
    
    /**
     * Get districts for a city
     */
    public function get_districts() {
        // Verify nonce
        if ( ! wp_verify_nonce( $_POST['nonce'], 'saudi_address_nonce' ) ) {
            wp_die( esc_html__( 'Security check failed', 'saudi-address-woocommerce' ) );
        }
        
        $city_id = isset( $_POST['city_id'] ) ? intval( $_POST['city_id'] ) : 0;
        $language = isset( $_POST['language'] ) ? sanitize_text_field( $_POST['language'] ) : 'A';
        
        // Validate language parameter
        if ( ! in_array( $language, array( 'A', 'E' ), true ) ) {
            $language = 'A';
        }
        
        $api = new Saudi_Address_API();
        $districts = $api->get_districts( $city_id, $language );
        
        if ( $districts ) {
            $options = array();
            foreach ( $districts as $district ) {
                $options[] = array(
                    'id'   => sanitize_text_field( $district->Id ),
                    'name' => sanitize_text_field( $district->Name ),
                );
            }
            wp_send_json_success( $options );
        } else {
            wp_send_json_error( esc_html__( 'Failed to load districts', 'saudi-address-woocommerce' ) );
        }
    }
    
    /**
     * Verify address
     */
    public function verify_address() {
        // Verify nonce
        if ( ! wp_verify_nonce( $_POST['nonce'], 'saudi_address_nonce' ) ) {
            wp_die( esc_html__( 'Security check failed', 'saudi-address-woocommerce' ) );
        }
        
        $building_number = isset( $_POST['building_number'] ) ? intval( $_POST['building_number'] ) : 0;
        $postal_code = isset( $_POST['postal_code'] ) ? intval( $_POST['postal_code'] ) : 0;
        $additional_number = isset( $_POST['additional_number'] ) ? intval( $_POST['additional_number'] ) : 0;
        $language = isset( $_POST['language'] ) ? sanitize_text_field( $_POST['language'] ) : 'A';
        
        // Validate language parameter
        if ( ! in_array( $language, array( 'A', 'E' ), true ) ) {
            $language = 'A';
        }
        
        // Validate required fields
        if ( $building_number <= 0 || $postal_code <= 0 ) {
            wp_send_json_error( __( 'Building number and postal code are required for verification', 'saudi-address-woocommerce' ) );
        }
        
        $api = new Saudi_Address_API();
        $verified = $api->verify_address( $building_number, $postal_code, $additional_number, $language );
        
        if ( $verified ) {
            wp_send_json_success( __( 'Address verified successfully', 'saudi-address-woocommerce' ) );
        } else {
            wp_send_json_error( __( 'Address verification failed', 'saudi-address-woocommerce' ) );
        }
    }
    
    /**
     * Test API connection
     */
    public function test_api() {
        // Check user capabilities
        if ( ! current_user_can( 'manage_woocommerce' ) ) {
            wp_die( __( 'You do not have sufficient permissions', 'saudi-address-woocommerce' ) );
        }
        
        // Verify nonce for admin AJAX
        if ( ! wp_verify_nonce( $_POST['nonce'], 'saudi_address_admin_nonce' ) ) {
            wp_die( esc_html__( 'Security check failed', 'saudi-address-woocommerce' ) );
        }
        
        $api = new Saudi_Address_API();
        
        if ( ! $api->is_configured() ) {
            wp_send_json_error( __( 'API is not configured. Please set your API key.', 'saudi-address-woocommerce' ) );
        }
        
        // Test by getting regions
        $regions = $api->get_regions();
        
        if ( $regions ) {
            wp_send_json_success( array(
                'message' => __( 'API connection successful!', 'saudi-address-woocommerce' ),
                'regions_count' => count( $regions ),
            ) );
        } else {
            wp_send_json_error( __( 'API connection failed. Please check your API key and URL.', 'saudi-address-woocommerce' ) );
        }
    }
}
