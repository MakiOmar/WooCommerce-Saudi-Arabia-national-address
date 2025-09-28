<?php
/**
 * Saudi Address API Class
 *
 * Handles communication with the Saudi National Address API
 *
 * @package Saudi_Address_WooCommerce
 * @since 1.0.0
 */

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Saudi Address API Class
 */
class Saudi_Address_API {
    
    /**
     * API URL
     */
    private $api_url;
    
    /**
     * API Key
     */
    private $api_key;
    
    /**
     * Constructor
     */
    public function __construct() {
        $this->api_url = get_option( 'saudi_address_api_url', 'https://apina.address.gov.sa/NationalAddress/v3.1' );
        $this->api_key = get_option( 'saudi_address_api_key', '' );
    }
    
    /**
     * Get regions
     *
     * @param string $lang Language code (A for Arabic, E for English)
     * @return array|false
     */
    public function get_regions( $lang = 'A' ) {
        $url = $this->api_url . '/lookup/regions?language=' . $lang . '&format=JSON&api_key=' . $this->api_key;
        
        $response = $this->make_request( $url );
        
        if ( $response && isset( $response->Regions ) ) {
            return $response->Regions;
        }
        
        return false;
    }
    
    /**
     * Get cities
     *
     * @param int    $region_id Region ID (-1 for all cities)
     * @param string $lang      Language code
     * @return array|false
     */
    public function get_cities( $region_id = -1, $lang = 'A' ) {
        $url = $this->api_url . '/lookup/cities?regionid=' . $region_id . '&language=' . $lang . '&format=JSON&api_key=' . $this->api_key;
        
        $response = $this->make_request( $url );
        
        if ( $response && isset( $response->Cities ) ) {
            return $response->Cities;
        }
        
        return false;
    }
    
    /**
     * Get districts
     *
     * @param int    $city_id City ID
     * @param string $lang    Language code
     * @return array|false
     */
    public function get_districts( $city_id, $lang = 'A' ) {
        $url = $this->api_url . '/lookup/districts?cityid=' . $city_id . '&language=' . $lang . '&format=JSON&api_key=' . $this->api_key;
        
        $response = $this->make_request( $url );
        
        if ( $response && isset( $response->Districts ) ) {
            return $response->Districts;
        }
        
        return false;
    }
    
    /**
     * Geocode address by coordinates
     *
     * @param float  $lat Latitude
     * @param float  $lng Longitude
     * @param string $lang Language code
     * @return object|false
     */
    public function geocode( $lat, $lng, $lang = 'A' ) {
        $url = $this->api_url . '/Address/address-geocode?lat=' . $lat . '&long=' . $lng . '&language=' . $lang . '&format=JSON&api_key=' . $this->api_key;
        
        $response = $this->make_request( $url );
        
        if ( $response && isset( $response->Addresses ) && ! empty( $response->Addresses ) ) {
            return $response->Addresses[0];
        }
        
        return false;
    }
    
    /**
     * Verify address
     *
     * @param int    $building_number   Building number
     * @param int    $post_code         Postal code
     * @param int    $additional_number Additional number
     * @param string $lang              Language code
     * @return bool|false
     */
    public function verify_address( $building_number, $post_code, $additional_number, $lang = 'A' ) {
        $url = $this->api_url . '/Address/address-verify?buildingnumber=' . $building_number . '&zipcode=' . $post_code . '&additionalnumber=' . $additional_number . '&language=' . $lang . '&format=JSON&api_key=' . $this->api_key;
        
        $response = $this->make_request( $url );
        
        if ( $response && isset( $response->addressfound ) ) {
            return (bool) $response->addressfound;
        }
        
        return false;
    }
    
    /**
     * Make API request
     *
     * @param string $url API URL
     * @return object|false
     */
    private function make_request( $url ) {
        // Check if API key is set
        if ( empty( $this->api_key ) ) {
            return false;
        }
        
        // Make the request
        $response = wp_remote_get( $url, array(
            'timeout' => 30,
            'headers' => array(
                'User-Agent' => 'Saudi Address WooCommerce Plugin/' . SAUDI_ADDRESS_WC_VERSION,
            ),
        ) );
        
        // Check for errors
        if ( is_wp_error( $response ) ) {
            return false;
        }
        
        $body = wp_remote_retrieve_body( $response );
        
        // Convert encoding from windows-1256 to utf-8
        $body = iconv( 'windows-1256', 'utf-8', $body );
        
        // Decode JSON
        $data = json_decode( $body );
        
        return $data;
    }
    
    /**
     * Check if API is configured
     *
     * @return bool
     */
    public function is_configured() {
        return ! empty( $this->api_key );
    }
}
