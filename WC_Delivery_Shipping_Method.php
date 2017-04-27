<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}
if ( ! class_exists( 'WC_Delivery_Shipping_Method' ) ) {
    class WC_Delivery_Shipping_Method extends WC_Shipping_Method
    {
        /**
         * Constructor for your shipping class
         *
         * @access public
         * @return void
         */
        public function __construct() {
            $this->id                 = 'delivery_shipping_method';
            $this->title       = __( 'Delivery Shipping Method' );
            $this->method_description = __( 'Description of delivery shipping method' ); //
            
            $this->availability = 'including';
            $this->countries = array(
                'UA', // Ukraine
                'GB', // United Kingdom
                'CA', // Canada
                'RU',
                );
            $this->init();
            $this->enabled = isset( $this->settings['enabled'] ) ? $this->settings['enabled'] : 'yes';
            $this->title = isset( $this->settings['title'] ) ? $this->settings['title'] : __( 'Delivery Shipping Method' );
        }
        /**
         * Init your settings
         *
         * @access public
         * @return void
         */
        function init() {
            // Load the settings API
            $this->init_form_fields(); // This is part of the settings API. Override the method to add your own settings
            $this->init_settings(); // This is part of the settings API. Loads settings you previously init.
            // Save settings in admin if you have any defined
            add_action( 'woocommerce_update_options_shipping_' . $this->id, array( $this, 'process_admin_options' ) );
        }
        /**
         * Define settings field for this shipping
         * @return void 
         */
        function init_form_fields() { 
            // We will add our settings here
                $this->form_fields = array(
 
                     'enabled' => array(
                          'title' => __( 'Enable'),
                          'type' => 'checkbox',
                          'description' => __( 'Enable this shipping.'),
                          'default' => 'yes'
                          ),
             
                     'title' => array(
                        'title' => __( 'Title'),
                          'type' => 'text',
                          'description' => __( 'Title to be display on site'),
                          'default' => __( 'Delivery')
                          ),
                     'coast' => array(
                        'title' => __( 'Coast'),
                          'type' => 'text',
                          'description' => __( 'Coast'),
                          'default' => 15
                          ),
             
                     );
        }
        
        /**
         * calculate_shipping function.
         *
         * @access public
         * @param mixed $package
         * @return void
         */
        public function calculate_shipping( $package = array() ) {
            
            $country = $package["destination"]["country"];
            // We will add the cost, rate and logics in here
            $countryZones = array(
                'UA' => 0,
                'GB' => 1,
                'CA' => 2,
                'RU' => 3,

            );
            $zonePrices = array(
                0 => 15,
                1 => 100,
                2 => 150,
                3 => 50,
            );
            $zoneFromCountry = $countryZones[ $country ];
            $priceFromZone = $zonePrices[ $zoneFromCountry ];
             $rate = array(
                'id' => $this->id,
                'label' => $this->title,
                'cost' => $this->settings['coast']+$priceFromZone,
                'calc_tax' => 'per_item'
            );
            // Register the rate
            $this->add_rate( $rate );
        }
    }
}