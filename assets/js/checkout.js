/**
 * Saudi Address Checkout JavaScript
 *
 * Handles dynamic loading of cities and districts
 *
 * @package Saudi_Address_WooCommerce
 * @since 1.0.0
 */

(function($) {
    'use strict';

    var SaudiAddressCheckout = {
        
        /**
         * Initialize
         */
        init: function() {
            this.bindEvents();
            this.loadRegions();
        },
        
        /**
         * Bind events
         */
        bindEvents: function() {
            $(document).on('change', '#saudi_region', this.onRegionChange);
            $(document).on('change', '#saudi_city', this.onCityChange);
            $(document).on('blur', '#saudi_building_number, #saudi_postal_code, #saudi_additional_number', this.verifyAddress);
        },
        
        /**
         * Load regions on page load
         */
        loadRegions: function() {
            var $regionSelect = $('#saudi_region');
            var $citySelect = $('#saudi_city');
            var $districtSelect = $('#saudi_district');
            
            // Clear existing options
            $citySelect.html('<option value="">' + saudi_address_ajax.strings.select_city + '</option>');
            $districtSelect.html('<option value="">' + saudi_address_ajax.strings.select_district + '</option>');
            
            // Disable dependent fields
            $citySelect.prop('disabled', true);
            $districtSelect.prop('disabled', true);
        },
        
        /**
         * Handle region change
         */
        onRegionChange: function() {
            var regionId = $(this).val();
            var $citySelect = $('#saudi_city');
            var $districtSelect = $('#saudi_district');
            
            // Clear city and district options
            $citySelect.html('<option value="">' + saudi_address_ajax.strings.select_city + '</option>');
            $districtSelect.html('<option value="">' + saudi_address_ajax.strings.select_district + '</option>');
            
            // Disable district field
            $districtSelect.prop('disabled', true);
            
            if (regionId) {
                SaudiAddressCheckout.loadCities(regionId);
            } else {
                $citySelect.prop('disabled', true);
            }
        },
        
        /**
         * Handle city change
         */
        onCityChange: function() {
            var cityId = $(this).val();
            var $districtSelect = $('#saudi_district');
            
            // Clear district options
            $districtSelect.html('<option value="">' + saudi_address_ajax.strings.select_district + '</option>');
            
            if (cityId) {
                SaudiAddressCheckout.loadDistricts(cityId);
            } else {
                $districtSelect.prop('disabled', true);
            }
        },
        
        /**
         * Load cities for a region
         */
        loadCities: function(regionId) {
            var $citySelect = $('#saudi_city');
            var $loading = $('<span class="loading">' + saudi_address_ajax.strings.loading + '</span>');
            
            $citySelect.after($loading);
            $citySelect.prop('disabled', true);
            
            $.ajax({
                url: saudi_address_ajax.ajax_url,
                type: 'POST',
                data: {
                    action: 'saudi_address_get_cities',
                    region_id: regionId,
                    language: saudi_address_ajax.language || 'A',
                    nonce: saudi_address_ajax.nonce
                },
                success: function(response) {
                    $loading.remove();
                    
                    if (response.success) {
                        var options = '<option value="">' + saudi_address_ajax.strings.select_city + '</option>';
                        
                        $.each(response.data, function(index, city) {
                            options += '<option value="' + city.id + '">' + city.name + '</option>';
                        });
                        
                        $citySelect.html(options);
                        $citySelect.prop('disabled', false);
                    } else {
                        alert(saudi_address_ajax.strings.error);
                        $citySelect.prop('disabled', false);
                    }
                },
                error: function() {
                    $loading.remove();
                    alert(saudi_address_ajax.strings.error);
                    $citySelect.prop('disabled', false);
                }
            });
        },
        
        /**
         * Load districts for a city
         */
        loadDistricts: function(cityId) {
            var $districtSelect = $('#saudi_district');
            var $loading = $('<span class="loading">' + saudi_address_ajax.strings.loading + '</span>');
            
            $districtSelect.after($loading);
            $districtSelect.prop('disabled', true);
            
            $.ajax({
                url: saudi_address_ajax.ajax_url,
                type: 'POST',
                data: {
                    action: 'saudi_address_get_districts',
                    city_id: cityId,
                    language: saudi_address_ajax.language || 'A',
                    nonce: saudi_address_ajax.nonce
                },
                success: function(response) {
                    $loading.remove();
                    
                    if (response.success) {
                        var options = '<option value="">' + saudi_address_ajax.strings.select_district + '</option>';
                        
                        $.each(response.data, function(index, district) {
                            options += '<option value="' + district.id + '">' + district.name + '</option>';
                        });
                        
                        $districtSelect.html(options);
                        $districtSelect.prop('disabled', false);
                    } else {
                        alert(saudi_address_ajax.strings.error);
                        $districtSelect.prop('disabled', false);
                    }
                },
                error: function() {
                    $loading.remove();
                    alert(saudi_address_ajax.strings.error);
                    $districtSelect.prop('disabled', false);
                }
            });
        },
        
        /**
         * Verify address
         */
        verifyAddress: function() {
            var buildingNumber = $('#saudi_building_number').val();
            var postalCode = $('#saudi_postal_code').val();
            var additionalNumber = $('#saudi_additional_number').val();
            
            // Only verify if all required fields are filled
            if (!buildingNumber || !postalCode) {
                return;
            }
            
            // Add loading indicator
            var $this = $(this);
            var $loading = $('<span class="address-verification-loading">' + saudi_address_ajax.strings.loading + '</span>');
            $this.after($loading);
            
            $.ajax({
                url: saudi_address_ajax.ajax_url,
                type: 'POST',
                data: {
                    action: 'saudi_address_verify_address',
                    building_number: buildingNumber,
                    postal_code: postalCode,
                    additional_number: additionalNumber || 0,
                    language: saudi_address_ajax.language || 'A',
                    nonce: saudi_address_ajax.nonce
                },
                success: function(response) {
                    $loading.remove();
                    
                    if (response.success) {
                        $this.addClass('address-verified');
                        $this.removeClass('address-error');
                    } else {
                        $this.addClass('address-error');
                        $this.removeClass('address-verified');
                    }
                },
                error: function() {
                    $loading.remove();
                    $this.addClass('address-error');
                    $this.removeClass('address-verified');
                }
            });
        }
    };
    
    // Initialize when document is ready
    $(document).ready(function() {
        SaudiAddressCheckout.init();
    });
    
})(jQuery);
