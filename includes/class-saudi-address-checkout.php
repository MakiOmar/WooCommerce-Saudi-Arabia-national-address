<?php
/**
 * Saudi Address Checkout Class
 *
 * Extends WooCommerce checkout to include Saudi address fields
 *
 * @package Saudi_Address_WooCommerce
 * @since 1.0.0
 */

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Saudi Address Checkout Class
 */
class Saudi_Address_Checkout {
    
    /**
     * Constructor
     */
    public function __construct() {
        add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
        add_action( 'woocommerce_after_checkout_billing_form', array( $this, 'add_saudi_address_fields' ) );
        add_action( 'woocommerce_checkout_process', array( $this, 'validate_saudi_address_fields' ) );
        add_action( 'woocommerce_checkout_update_order_meta', array( $this, 'save_saudi_address_fields' ) );
        add_action( 'woocommerce_admin_order_data_after_billing_address', array( $this, 'display_saudi_address_in_admin' ) );
        add_action( 'woocommerce_order_details_after_order_table', array( $this, 'display_saudi_address_in_order_details' ) );

        // Persist values for logged-in customers
        add_action( 'woocommerce_checkout_update_user_meta', array( $this, 'save_saudi_address_user_meta' ), 10, 2 );
        add_filter( 'woocommerce_checkout_get_value', array( $this, 'prefill_saudi_address_from_user_meta' ), 10, 2 );
    }
    
    /**
     * Enqueue scripts and styles
     */
    public function enqueue_scripts() {
        if ( is_checkout() ) {
            wp_enqueue_script( 'saudi-address-checkout', SAUDI_ADDRESS_WC_PLUGIN_URL . 'assets/js/checkout.js', array( 'jquery' ), SAUDI_ADDRESS_WC_VERSION, true );
            wp_enqueue_style( 'saudi-address-checkout', SAUDI_ADDRESS_WC_PLUGIN_URL . 'assets/css/checkout.css', array(), SAUDI_ADDRESS_WC_VERSION );
            
            // Localize script
            wp_localize_script( 'saudi-address-checkout', 'saudi_address_ajax', array(
                'ajax_url' => admin_url( 'admin-ajax.php' ),
                'nonce'    => wp_create_nonce( 'saudi_address_nonce' ),
                'language' => get_option( 'saudi_address_language', 'A' ),
                'strings'  => array(
                    'loading'        => __( 'Loading...', 'saudi-address-woocommerce' ),
                    'select_region'  => __( 'Select Region', 'saudi-address-woocommerce' ),
                    'select_city'    => __( 'Select City', 'saudi-address-woocommerce' ),
                    'select_district' => __( 'Select District', 'saudi-address-woocommerce' ),
                    'error'          => __( 'An error occurred. Please try again.', 'saudi-address-woocommerce' ),
                ),
            ) );
        }
    }
    
    /**
     * Add Saudi address fields to checkout
     *
     * @param WC_Checkout $checkout Checkout object
     */
    public function add_saudi_address_fields( $checkout ) {
        // Check if Saudi address is enabled
        if ( get_option( 'saudi_address_enabled', 'yes' ) !== 'yes' ) {
            return;
        }
        
        echo '<div id="saudi_address_fields">';
        echo '<h3>' . esc_html__( 'Saudi National Address', 'saudi-address-woocommerce' ) . '</h3>';
        
        // Region field
        if ( get_option( 'saudi_address_show_region', 'yes' ) === 'yes' ) {
            woocommerce_form_field( 'saudi_region', array(
                'type'        => 'select',
                'class'       => array( 'form-row-wide', 'saudi-address-field' ),
                'label'       => esc_html__( 'Region', 'saudi-address-woocommerce' ),
                'options'     => array( '' => esc_html__( 'Select Region', 'saudi-address-woocommerce' ) ),
                'required'    => get_option( 'saudi_address_required', 'yes' ) === 'yes',
            ), $checkout->get_value( 'saudi_region' ) );
        }
        
        // City field
        if ( get_option( 'saudi_address_show_city', 'yes' ) === 'yes' ) {
            woocommerce_form_field( 'saudi_city', array(
                'type'        => 'select',
                'class'       => array( 'form-row-wide', 'saudi-address-field' ),
                'label'       => esc_html__( 'City', 'saudi-address-woocommerce' ),
                'options'     => array( '' => esc_html__( 'Select City', 'saudi-address-woocommerce' ) ),
                'required'    => get_option( 'saudi_address_required', 'yes' ) === 'yes',
            ), $checkout->get_value( 'saudi_city' ) );
        }
        
        // District field
        if ( get_option( 'saudi_address_show_district', 'yes' ) === 'yes' ) {
            woocommerce_form_field( 'saudi_district', array(
                'type'        => 'select',
                'class'       => array( 'form-row-wide', 'saudi-address-field' ),
                'label'       => esc_html__( 'District', 'saudi-address-woocommerce' ),
                'options'     => array( '' => esc_html__( 'Select District', 'saudi-address-woocommerce' ) ),
                'required'    => get_option( 'saudi_address_required', 'yes' ) === 'yes',
            ), $checkout->get_value( 'saudi_district' ) );
        }
        
        // Building number field
        if ( get_option( 'saudi_address_show_building_number', 'yes' ) === 'yes' ) {
            woocommerce_form_field( 'saudi_building_number', array(
                'type'        => 'text',
                'class'       => array( 'form-row-first', 'saudi-address-field' ),
                'label'       => esc_html__( 'Building Number', 'saudi-address-woocommerce' ),
                'placeholder' => esc_attr__( 'Enter building number', 'saudi-address-woocommerce' ),
                'required'    => get_option( 'saudi_address_required', 'yes' ) === 'yes',
            ), $checkout->get_value( 'saudi_building_number' ) );
        }
        
        // Postal code field
        if ( get_option( 'saudi_address_show_postal_code', 'yes' ) === 'yes' ) {
            woocommerce_form_field( 'saudi_postal_code', array(
                'type'        => 'text',
                'class'       => array( 'form-row-last', 'saudi-address-field' ),
                'label'       => esc_html__( 'Postal Code', 'saudi-address-woocommerce' ),
                'placeholder' => esc_attr__( 'Enter postal code', 'saudi-address-woocommerce' ),
                'required'    => get_option( 'saudi_address_required', 'yes' ) === 'yes',
            ), $checkout->get_value( 'saudi_postal_code' ) );
        }
        
        // Additional number field
        if ( get_option( 'saudi_address_show_additional_number', 'no' ) === 'yes' ) {
            woocommerce_form_field( 'saudi_additional_number', array(
                'type'        => 'text',
                'class'       => array( 'form-row-first', 'saudi-address-field' ),
                'label'       => esc_html__( 'Additional Number', 'saudi-address-woocommerce' ),
                'placeholder' => esc_attr__( 'Enter additional number', 'saudi-address-woocommerce' ),
                'required'    => false,
            ), $checkout->get_value( 'saudi_additional_number' ) );
        }
        
        // Street field
        if ( get_option( 'saudi_address_show_street', 'no' ) === 'yes' ) {
            woocommerce_form_field( 'saudi_street', array(
                'type'        => 'text',
                'class'       => array( 'form-row-last', 'saudi-address-field' ),
                'label'       => esc_html__( 'Street', 'saudi-address-woocommerce' ),
                'placeholder' => esc_attr__( 'Enter street name', 'saudi-address-woocommerce' ),
                'required'    => false,
            ), $checkout->get_value( 'saudi_street' ) );
        }
        
        // Unit number field
        if ( get_option( 'saudi_address_show_unit_number', 'no' ) === 'yes' ) {
            woocommerce_form_field( 'saudi_unit_number', array(
                'type'        => 'text',
                'class'       => array( 'form-row-wide', 'saudi-address-field' ),
                'label'       => esc_html__( 'Unit Number', 'saudi-address-woocommerce' ),
                'placeholder' => esc_attr__( 'Enter unit number (optional)', 'saudi-address-woocommerce' ),
                'required'    => false,
            ), $checkout->get_value( 'saudi_unit_number' ) );
        }
        
        echo '</div>';
    }
    
    /**
     * Validate Saudi address fields
     */
    public function validate_saudi_address_fields() {
        // Check if Saudi address is enabled and required
        if ( get_option( 'saudi_address_enabled', 'yes' ) !== 'yes' || get_option( 'saudi_address_required', 'yes' ) !== 'yes' ) {
            return;
        }
        
        // Sanitize and validate input
        $saudi_region = isset( $_POST['saudi_region'] ) ? sanitize_text_field( $_POST['saudi_region'] ) : '';
        $saudi_city = isset( $_POST['saudi_city'] ) ? sanitize_text_field( $_POST['saudi_city'] ) : '';
        $saudi_district = isset( $_POST['saudi_district'] ) ? sanitize_text_field( $_POST['saudi_district'] ) : '';
        $saudi_building_number = isset( $_POST['saudi_building_number'] ) ? sanitize_text_field( $_POST['saudi_building_number'] ) : '';
        $saudi_postal_code = isset( $_POST['saudi_postal_code'] ) ? sanitize_text_field( $_POST['saudi_postal_code'] ) : '';
        $saudi_additional_number = isset( $_POST['saudi_additional_number'] ) ? sanitize_text_field( $_POST['saudi_additional_number'] ) : '';
        
        $required_fields = array();
        
        // Only validate fields that are enabled and shown
        if ( get_option( 'saudi_address_show_region', 'yes' ) === 'yes' ) {
            $required_fields['saudi_region'] = esc_html__( 'Region', 'saudi-address-woocommerce' );
        }
        if ( get_option( 'saudi_address_show_city', 'yes' ) === 'yes' ) {
            $required_fields['saudi_city'] = esc_html__( 'City', 'saudi-address-woocommerce' );
        }
        if ( get_option( 'saudi_address_show_district', 'yes' ) === 'yes' ) {
            $required_fields['saudi_district'] = esc_html__( 'District', 'saudi-address-woocommerce' );
        }
        if ( get_option( 'saudi_address_show_building_number', 'yes' ) === 'yes' ) {
            $required_fields['saudi_building_number'] = esc_html__( 'Building Number', 'saudi-address-woocommerce' );
        }
        if ( get_option( 'saudi_address_show_postal_code', 'yes' ) === 'yes' ) {
            $required_fields['saudi_postal_code'] = esc_html__( 'Postal Code', 'saudi-address-woocommerce' );
        }
        
        foreach ( $required_fields as $field => $label ) {
            $value = isset( $_POST[ $field ] ) ? sanitize_text_field( $_POST[ $field ] ) : '';
            if ( empty( $value ) ) {
                wc_add_notice( sprintf( esc_html__( '%s is a required field.', 'saudi-address-woocommerce' ), esc_html( $label ) ), 'error' );
            }
        }
        
        // Validate postal code format (5 digits)
        if ( ! empty( $saudi_postal_code ) && ! preg_match( '/^\d{5}$/', $saudi_postal_code ) ) {
            wc_add_notice( esc_html__( 'Postal code must be 5 digits.', 'saudi-address-woocommerce' ), 'error' );
        }
        
        // Validate building number (numeric)
        if ( ! empty( $saudi_building_number ) && ! is_numeric( $saudi_building_number ) ) {
            wc_add_notice( esc_html__( 'Building number must be numeric.', 'saudi-address-woocommerce' ), 'error' );
        }
        
        // Validate additional number (numeric if provided)
        if ( ! empty( $saudi_additional_number ) && ! is_numeric( $saudi_additional_number ) ) {
            wc_add_notice( esc_html__( 'Additional number must be numeric.', 'saudi-address-woocommerce' ), 'error' );
        }
    }
    
    /**
     * Save Saudi address fields
     *
     * @param int $order_id Order ID
     */
    public function save_saudi_address_fields( $order_id ) {
        $fields = array(
            'saudi_region',
            'saudi_city',
            'saudi_district',
            'saudi_building_number',
            'saudi_postal_code',
            'saudi_additional_number',
            'saudi_street',
            'saudi_unit_number',
        );
        
        foreach ( $fields as $field ) {
            if ( isset( $_POST[ $field ] ) && ! empty( $_POST[ $field ] ) ) {
                $value = sanitize_text_field( $_POST[ $field ] );
                update_post_meta( $order_id, '_' . $field, $value );
            }
        }
    }

    /**
     * Save Saudi address fields to user meta for logged-in customers.
     *
     * @param int   $customer_id User ID
     * @param array $posted      Posted checkout data
     */
    public function save_saudi_address_user_meta( $customer_id, $posted ) {
        if ( empty( $customer_id ) ) {
            return;
        }

        $fields = array(
            'saudi_region',
            'saudi_city',
            'saudi_district',
            'saudi_building_number',
            'saudi_postal_code',
            'saudi_additional_number',
            'saudi_street',
            'saudi_unit_number',
        );

        foreach ( $fields as $field ) {
            if ( isset( $_POST[ $field ] ) && '' !== $_POST[ $field ] ) {
                update_user_meta( $customer_id, $field, sanitize_text_field( wp_unslash( $_POST[ $field ] ) ) );
            }
        }
    }

    /**
     * Prefill Saudi address fields from user meta for logged-in customers.
     *
     * @param mixed  $value  Current value
     * @param string $input  Field key
     * @return mixed
     */
    public function prefill_saudi_address_from_user_meta( $value, $input ) {
        if ( ! is_user_logged_in() ) {
            return $value;
        }

        // Only target our custom fields
        $saudi_fields = array(
            'saudi_region',
            'saudi_city',
            'saudi_district',
            'saudi_building_number',
            'saudi_postal_code',
            'saudi_additional_number',
            'saudi_street',
            'saudi_unit_number',
        );

        if ( in_array( $input, $saudi_fields, true ) && ( '' === $value || null === $value ) ) {
            $user_value = get_user_meta( get_current_user_id(), $input, true );
            if ( '' !== $user_value && null !== $user_value ) {
                return $user_value;
            }
        }

        return $value;
    }
    
    /**
     * Display Saudi address in admin order details
     *
     * @param WC_Order $order Order object
     */
    public function display_saudi_address_in_admin( $order ) {
        $saudi_fields = array(
            'saudi_region'         => esc_html__( 'Region', 'saudi-address-woocommerce' ),
            'saudi_city'           => esc_html__( 'City', 'saudi-address-woocommerce' ),
            'saudi_district'       => esc_html__( 'District', 'saudi-address-woocommerce' ),
            'saudi_building_number' => esc_html__( 'Building Number', 'saudi-address-woocommerce' ),
            'saudi_postal_code'    => esc_html__( 'Postal Code', 'saudi-address-woocommerce' ),
            'saudi_additional_number' => esc_html__( 'Additional Number', 'saudi-address-woocommerce' ),
            'saudi_street'         => esc_html__( 'Street', 'saudi-address-woocommerce' ),
            'saudi_unit_number'    => esc_html__( 'Unit Number', 'saudi-address-woocommerce' ),
        );
        
        $has_saudi_address = false;
        foreach ( $saudi_fields as $field => $label ) {
            $value = get_post_meta( $order->get_id(), '_' . $field, true );
            if ( ! empty( $value ) ) {
                $has_saudi_address = true;
                break;
            }
        }
        
        if ( $has_saudi_address ) {
            echo '<h3>' . esc_html__( 'Saudi National Address', 'saudi-address-woocommerce' ) . '</h3>';
            echo '<p>';
            
            foreach ( $saudi_fields as $field => $label ) {
                $value = get_post_meta( $order->get_id(), '_' . $field, true );
                if ( ! empty( $value ) ) {
                    echo '<strong>' . esc_html( $label ) . ':</strong> ' . esc_html( $value ) . '<br/>';
                }
            }
            
            echo '</p>';
        }
    }
    
    /**
     * Display Saudi address in order details for customer
     *
     * @param WC_Order $order Order object
     */
    public function display_saudi_address_in_order_details( $order ) {
        $saudi_fields = array(
            'saudi_region'         => esc_html__( 'Region', 'saudi-address-woocommerce' ),
            'saudi_city'           => esc_html__( 'City', 'saudi-address-woocommerce' ),
            'saudi_district'       => esc_html__( 'District', 'saudi-address-woocommerce' ),
            'saudi_building_number' => esc_html__( 'Building Number', 'saudi-address-woocommerce' ),
            'saudi_postal_code'    => esc_html__( 'Postal Code', 'saudi-address-woocommerce' ),
            'saudi_additional_number' => esc_html__( 'Additional Number', 'saudi-address-woocommerce' ),
            'saudi_street'         => esc_html__( 'Street', 'saudi-address-woocommerce' ),
            'saudi_unit_number'    => esc_html__( 'Unit Number', 'saudi-address-woocommerce' ),
        );
        
        $has_saudi_address = false;
        foreach ( $saudi_fields as $field => $label ) {
            $value = get_post_meta( $order->get_id(), '_' . $field, true );
            if ( ! empty( $value ) ) {
                $has_saudi_address = true;
                break;
            }
        }
        
        if ( $has_saudi_address ) {
            echo '<h3>' . esc_html__( 'Saudi National Address', 'saudi-address-woocommerce' ) . '</h3>';
            echo '<p>';
            
            foreach ( $saudi_fields as $field => $label ) {
                $value = get_post_meta( $order->get_id(), '_' . $field, true );
                if ( ! empty( $value ) ) {
                    echo '<strong>' . esc_html( $label ) . ':</strong> ' . esc_html( $value ) . '<br/>';
                }
            }
            
            echo '</p>';
        }
    }
}
