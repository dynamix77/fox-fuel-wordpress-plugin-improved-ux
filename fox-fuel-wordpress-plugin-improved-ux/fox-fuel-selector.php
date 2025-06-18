<?php
/**
 * Plugin Name: Fox Fuel Price Protection Plan Selector
 * Plugin URI: https://foxfuel.com
 * Description: A comprehensive heating oil price protection plan selector with improved value-first UX flow and optional contact information.
 * Version: 2.0.0
 * Author: Fox Fuel
 * Author URI: https://foxfuel.com
 * License: GPL v2 or later
 * Text Domain: fox-fuel-selector
 * Domain Path: /languages
 * Requires at least: 5.0
 * Tested up to: 6.4
 * Requires PHP: 7.4
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Define plugin constants
define('FOX_FUEL_PLUGIN_VERSION', '2.0.0');
define('FOX_FUEL_PLUGIN_URL', plugin_dir_url(__FILE__));
define('FOX_FUEL_PLUGIN_PATH', plugin_dir_path(__FILE__));
define('FOX_FUEL_PLUGIN_BASENAME', plugin_basename(__FILE__));

// Include required files
require_once FOX_FUEL_PLUGIN_PATH . 'includes/class-fox-fuel-config.php';
require_once FOX_FUEL_PLUGIN_PATH . 'includes/class-fox-fuel-admin.php';
require_once FOX_FUEL_PLUGIN_PATH . 'includes/class-fox-fuel-sheets-integration.php';
require_once FOX_FUEL_PLUGIN_PATH . 'includes/service-area-zips.php';

/**
 * Main Fox Fuel Selector Plugin Class
 */
class FoxFuelSelectorPlugin {
    
    /**
     * Constructor
     */
    public function __construct() {
        add_action('init', array($this, 'init'));
        add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts'));
        
        // Initialize admin interface
        if (is_admin()) {
            new FoxFuelAdmin();
        }
        
        // Register shortcode
        add_shortcode('fox_fuel_selector', array($this, 'render_selector_shortcode'));
        
        // AJAX handlers
        add_action('wp_ajax_fox_fuel_submit_form', array($this, 'ajax_submit_form'));
        add_action('wp_ajax_nopriv_fox_fuel_submit_form', array($this, 'ajax_submit_form'));
        
        // Activation/Deactivation hooks
        register_activation_hook(__FILE__, array($this, 'activate'));
        register_deactivation_hook(__FILE__, array($this, 'deactivate'));
    }
    
    /**
     * Initialize plugin
     */
    public function init() {
        load_plugin_textdomain('fox-fuel-selector', false, dirname(FOX_FUEL_PLUGIN_BASENAME) . '/languages');
    }
    
    /**
     * Enqueue frontend scripts and styles
     */
    public function enqueue_scripts() {
        wp_enqueue_style(
            'fox-fuel-selector-css',
            FOX_FUEL_PLUGIN_URL . 'assets/css/fox-fuel-selector.css',
            array(),
            FOX_FUEL_PLUGIN_VERSION
        );
        
        // Use simplified JavaScript for better compatibility
        wp_enqueue_script(
            'fox-fuel-selector-js',
            FOX_FUEL_PLUGIN_URL . 'assets/js/fox-fuel-selector-simple.js',
            array('jquery'),
            FOX_FUEL_PLUGIN_VERSION,
            true
        );
        
        // Get configuration for JavaScript
        $config = FoxFuelConfig::getInstance();
        
        // Localize script for AJAX with better error handling
        $localize_data = array(
            'ajaxurl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('fox_fuel_nonce'),
            'pluginUrl' => FOX_FUEL_PLUGIN_URL,
            'debug' => defined('WP_DEBUG') && WP_DEBUG,
            'config' => array(
                'contact_phone' => $config->get('contact_phone'),
                'contact_email' => $config->get('contact_email'),
                'plans' => $config->get('plans'),
                'service_zips' => $this->getServiceZips()
            )
        );
        
        wp_localize_script('fox-fuel-selector-js', 'foxFuelAjax', $localize_data);
    }
    
    /**
     * Get service area ZIP codes
     */
    private function getServiceZips() {
        $zip_file = FOX_FUEL_PLUGIN_PATH . 'includes/service-area-zips.php';
        if (file_exists($zip_file)) {
            return include $zip_file;
        }
        
        // Fallback ZIP codes
        return array(
            '18966', '18974', '18976', '19001', '19002', '19006', '19009',
            '19012', '19025', '19027', '19031', '19034', '19038', '19040',
            '19044', '19046', '19053', '19075', '19090', '19095', '19115',
            '19126', '19150', '19422', '19454'
        );
    }
    
    /**
     * Render selector shortcode
     */
    public function render_selector_shortcode($atts) {
        $atts = shortcode_atts(array(
            'mode' => 'guided',
            'theme' => 'light',
            'width' => '100%',
            'height' => 'auto'
        ), $atts, 'fox_fuel_selector');
        
        ob_start();
        include FOX_FUEL_PLUGIN_PATH . 'templates/selector-template.php';
        return ob_get_clean();
    }
    
    /**
     * AJAX handler for form submission
     */
    public function ajax_submit_form() {
        // Verify nonce
        if (!wp_verify_nonce($_POST['nonce'] ?? '', 'fox_fuel_nonce')) {
            wp_send_json_error('Security check failed.');
            return;
        }
        
        // Get form data
        $form_data_json = $_POST['form_data'] ?? '';
        $form_data = json_decode(stripslashes($form_data_json), true);
        
        if (!$form_data) {
            wp_send_json_error('Invalid form data.');
            return;
        }
        
        // Store submission
        $submission_id = $this->store_submission($form_data);
        
        // Send notification email
        $email_sent = $this->send_notification_email($form_data);
        
        wp_send_json_success(array(
            'message' => 'Thank you for your interest! We will contact you shortly.',
            'next_steps' => 'A Fox Fuel representative will call you within 24 hours to discuss your plan options.',
            'submission_id' => $submission_id,
            'email_sent' => $email_sent
        ));
    }
    
    /**
     * Store form submission in database
     */
    private function store_submission($form_data) {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'fox_fuel_submissions';
        
        $result = $wpdb->insert(
            $table_name,
            array(
                'submission_date' => current_time('mysql'),
                'customer_name' => sanitize_text_field($form_data['customer_name'] ?? ''),
                'email' => sanitize_email($form_data['customer_email'] ?? ''),
                'phone' => sanitize_text_field($form_data['customer_phone'] ?? ''),
                'zip_code' => sanitize_text_field($form_data['zip_code'] ?? ''),
                'selected_plan' => sanitize_text_field($form_data['selected_plan'] ?? ''),
                'annual_usage' => sanitize_text_field($form_data['annual_usage'] ?? ''),
                'form_data' => wp_json_encode($form_data),
                'status' => 'pending'
            )
        );
        
        return $result ? $wpdb->insert_id : false;
    }
    
    /**
     * Send notification email
     */
    private function send_notification_email($form_data) {
        $config = FoxFuelConfig::getInstance();
        $to = $config->get('contact_email', 'info@foxfuel.com');
        
        $subject = 'New Fox Fuel Plan Interest - ' . ($form_data['customer_name'] ?? 'Anonymous');
        
        $message = "New plan selector submission:\n\n";
        $message .= "Name: " . ($form_data['customer_name'] ?? 'Not provided') . "\n";
        $message .= "Email: " . ($form_data['customer_email'] ?? 'Not provided') . "\n";
        $message .= "Phone: " . ($form_data['customer_phone'] ?? 'Not provided') . "\n";
        $message .= "ZIP Code: " . ($form_data['zip_code'] ?? 'Not provided') . "\n";
        $message .= "Selected Plan: " . ($form_data['selected_plan'] ?? 'Not selected') . "\n";
        $message .= "Annual Usage: " . ($form_data['annual_usage'] ?? 'Not provided') . "\n";
        $message .= "Priorities: " . (isset($form_data['priorities']) ? implode(', ', $form_data['priorities']) : 'Not provided') . "\n";
        $message .= "Contact Time: " . ($form_data['preferred_contact_time'] ?? 'Not specified') . "\n";
        if (!empty($form_data['additional_notes'])) {
            $message .= "Notes: " . $form_data['additional_notes'] . "\n";
        }
        
        wp_mail($to, $subject, $message);
    }
    
    /**
     * Plugin activation
     */
    public function activate() {
        // Initialize configuration with defaults
        $config = FoxFuelConfig::getInstance();
        
        // Create database tables
        $this->create_tables();
        
        // Set default options if they don't exist
        if (!get_option('fox_fuel_config')) {
            // This will trigger the default config creation in FoxFuelConfig
            $config->get('contact_phone');
        }
    }
    
    /**
     * Plugin deactivation
     */
    public function deactivate() {
        // Clean up if needed
    }
    
    /**
     * Create database tables
     */
    private function create_tables() {
        global $wpdb;
        
        $table_name = $wpdb->prefix . 'fox_fuel_submissions';
        $charset_collate = $wpdb->get_charset_collate();
        
        $sql = "CREATE TABLE $table_name (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            submission_date datetime DEFAULT CURRENT_TIMESTAMP NOT NULL,
            customer_name tinytext NOT NULL,
            email varchar(100) NOT NULL,
            phone varchar(20),
            zip_code varchar(10),
            selected_plan varchar(50),
            annual_usage varchar(50),
            form_data longtext,
            status varchar(20) DEFAULT 'pending',
            PRIMARY KEY (id)
        ) $charset_collate;";
        
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
    }
}

// Initialize the plugin
new FoxFuelSelectorPlugin();