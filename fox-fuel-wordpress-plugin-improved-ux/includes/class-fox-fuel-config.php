<?php
/**
 * Fox Fuel Configuration Class
 * Centralized configuration management
 */

if (!defined('ABSPATH')) {
    exit;
}

class FoxFuelConfig {
    
    private static $instance = null;
    private $config = null;
    
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    private function __construct() {
        $this->loadConfig();
    }
    
    private function loadConfig() {
        $default_config = $this->getDefaultConfig();
        $saved_config = get_option('fox_fuel_config', array());
        $this->config = wp_parse_args($saved_config, $default_config);
    }
    
    public function get($key, $default = null) {
        return isset($this->config[$key]) ? $this->config[$key] : $default;
    }
    
    public function set($key, $value) {
        $this->config[$key] = $value;
        update_option('fox_fuel_config', $this->config);
    }
    
    public function getAll() {
        return $this->config;
    }
    
    public function updateAll($config) {
        $this->config = wp_parse_args($config, $this->getDefaultConfig());
        update_option('fox_fuel_config', $this->config);
    }
    
    private function getDefaultConfig() {
        return array(
            // Contact Information
            'contact_phone' => '(215) 659-1616',
            'contact_email' => 'info@foxfuel.com',
            'business_hours' => 'Monday-Friday, 8 AM - 5 PM',
            'service_tagline' => 'Serving southeastern Pennsylvania and South Jersey since 1981',
            
            // Logo and Branding
            'logo_url' => FOX_FUEL_PLUGIN_URL . 'assets/images/fox-fuel-logo.svg',
            'logo_fallback_url' => FOX_FUEL_PLUGIN_URL . 'assets/images/fox-fuel-logo.png',
            'company_name' => 'Fox Fuel',
            
            // Form Content
            'main_heading' => 'Find Your Perfect Heating Oil Plan',
            'main_subtitle' => 'Get personalized recommendations in under 2 minutes',
            
            // Step Content
            'step1_title' => 'First, let\'s check if we serve your area',
            'step1_subtitle' => 'We deliver to parts of southeastern Pennsylvania and South Jersey',
            'step2_title' => 'Tell us about your heating oil usage',
            'step2_subtitle' => 'This helps us recommend the right plan for your home.',
            'step3_title' => 'What\'s most important to you?',
            'step3_subtitle' => 'Select all that apply - this helps us prioritize your recommendations.',
            'step4_title' => 'Your Recommended Plans',
            'step4_subtitle' => 'Based on your needs, here are the best options for you:',
            
            // Usage Options
            'usage_options' => array(
                'less-than-200' => 'Less than 200 gallons',
                '200-400' => '200-400 gallons',
                '400-600' => '400-600 gallons', 
                '600-800' => '600-800 gallons',
                '800-1000' => '800-1000 gallons',
                '1000-1200' => '1000-1200 gallons',
                '1200+' => '1200+ gallons',
                'not-sure' => 'Not sure'
            ),
            
            // Priority Options
            'priority_options' => array(
                'predictable-payments' => array(
                    'title' => 'Predictable monthly payments',
                    'description' => 'Same amount each month, no surprises'
                ),
                'price-protection' => array(
                    'title' => 'Protection from price spikes',
                    'description' => 'Shield from sudden market increases'
                ),
                'lowest-price' => array(
                    'title' => 'Lowest possible price',
                    'description' => 'Best value, willing to accept some price fluctuation'
                ),
                'no-commitment' => array(
                    'title' => 'Flexibility with no long-term commitment',
                    'description' => 'Freedom to change plans or providers'
                )
            ),
            
            // Plan Data
            'plans' => array(
                'smartpay-cap' => array(
                    'name' => 'SmartPay Price Cap',
                    'type' => 'cap',
                    'badge' => 'Most Popular',
                    'description' => 'Price ceiling protection with benefits if prices drop',
                    'price' => '$3.89/gallon cap',
                    'monthly_payment' => '12 equal payments',
                    'features' => array(
                        'Price ceiling protection',
                        'Benefit from price drops',
                        'Automatic delivery included',
                        '$19.99/month cap fee',
                        '400 gallon minimum'
                    ),
                    'best_for' => array('predictable-payments', 'price-protection'),
                    'enrollment_fee' => 239.88,
                    'monthly_enroll_fee' => 19.99,
                    'active' => true
                ),
                'fixed-price-budget' => array(
                    'name' => 'Fixed Price Budget',
                    'type' => 'fixed',
                    'badge' => 'Price Certainty',
                    'description' => 'Guaranteed fixed price for the entire heating season',
                    'price' => '$3.59/gallon fixed',
                    'monthly_payment' => '10 equal payments',
                    'features' => array(
                        'Guaranteed fixed price',
                        'Complete price protection',
                        'Predictable monthly payments',
                        'No enrollment fees',
                        '400 gallon minimum'
                    ),
                    'best_for' => array('predictable-payments', 'price-protection'),
                    'enrollment_fee' => 0,
                    'monthly_enroll_fee' => 0,
                    'active' => true
                ),
                'fuelsaver-budget' => array(
                    'name' => 'FuelSaver Budget',
                    'type' => 'budget',
                    'badge' => 'Budget Friendly',
                    'description' => 'Equal monthly payments with no price protection',
                    'price' => 'Market pricing',
                    'monthly_payment' => '$125-200/month',
                    'features' => array(
                        'Predictable monthly payments',
                        'No minimum gallons',
                        'Market pricing',
                        'No enrollment fees',
                        'Final adjustment in May'
                    ),
                    'best_for' => array('predictable-payments'),
                    'enrollment_fee' => 0,
                    'monthly_enroll_fee' => 0,
                    'active' => true
                )
            ),
            
            // Messages and Legal
            'contact_intro_heading' => '🎉 Perfect! You now have personalized heating oil plan recommendations.',
            'contact_intro_text' => 'Want us to follow up or answer questions? Leave your info below (optional).',
            'contact_no_pressure' => 'Prefer not to? No problem—you already have everything you need above!',
            'contact_yes_button' => '👍 Yes, I\'d like Fox Fuel to contact me about these plans',
            'contact_no_button' => '🔍 No thanks, I\'ll review the options myself',
            
            'success_heading' => '✅ Thank you!',
            'success_message' => 'We\'ve received your information and will contact you within 24 hours to discuss your plan options and answer any questions.',
            
            'next_steps' => array(
                'A Fox Fuel representative will call you at your preferred time',
                'We\'ll review your recommended plans and pricing',
                'Answer any questions about delivery, billing, or our service',
                'Help you enroll in your chosen plan if you\'re ready'
            ),
            
            // Error Messages
            'zip_code_required' => 'Please enter your ZIP code.',
            'zip_code_invalid' => 'Please enter a valid 5-digit ZIP code.',
            'zip_code_out_of_area' => 'Sorry, we don\'t currently serve that area. We deliver to parts of southeastern Pennsylvania and South Jersey.',
            'usage_required' => 'Please select your annual usage amount.',
            'priorities_required' => 'Please select at least one priority to help us recommend the best plan for you.',
            'contact_name_required' => 'Please enter your name.',
            'contact_info_required' => 'Please provide either a phone number or email address.',
            'email_invalid' => 'Please enter a valid email address.',
            
            // UI Settings
            'show_progress_bar' => true,
            'enable_tooltips' => true,
            'theme' => 'light',
            'enable_analytics' => false
        );
    }
}
