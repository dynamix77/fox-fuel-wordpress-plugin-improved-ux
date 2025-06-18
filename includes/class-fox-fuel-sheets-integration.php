<?php
/**
 * Fox Fuel Google Sheets Integration
 * Dynamic pricing updates from Google Sheets
 */

if (!defined('ABSPATH')) {
    exit;
}

class FoxFuelSheetsIntegration {
    
    private $config;
    private $cache_key = 'fox_fuel_sheets_data';
    private $error_log_key = 'fox_fuel_sheets_errors';
    private $last_sync_key = 'fox_fuel_sheets_last_sync';
    
    public function __construct() {
        $this->config = FoxFuelConfig::getInstance();
        
        // Hook into WordPress actions
        add_action('init', array($this, 'init'));
        add_action('wp_ajax_fox_fuel_sync_sheets', array($this, 'manual_sync'));
        add_action('wp_ajax_fox_fuel_test_sheets', array($this, 'test_connection'));
        add_action('wp_ajax_fox_fuel_get_sync_log', array($this, 'get_sync_log'));
        add_action('fox_fuel_scheduled_sync', array($this, 'scheduled_sync'));
        
        // Admin notices for sync issues
        add_action('admin_notices', array($this, 'show_admin_notices'));
        
        // Schedule automatic syncing
        add_action('wp_loaded', array($this, 'maybe_schedule_sync'));
    }
    
    public function init() {
        // Register settings are handled by admin class
    }
    
    public function maybe_schedule_sync() {
        if (!wp_next_scheduled('fox_fuel_scheduled_sync')) {
            $this->schedule_sync();
        }
    }
    
    /**
     * Sync pricing data from Google Sheets
     */
    public function sync_pricing_data() {
        $config = get_option('fox_fuel_sheets_config', array());
        
        if (empty($config['sheets_url'])) {
            $this->log_error('No Google Sheets URL configured');
            return false;
        }
        
        try {
            // Try API method first, then fallback to CSV
            $data = $this->fetch_sheets_data($config);
            
            if ($data === false) {
                throw new Exception('Failed to fetch data from Google Sheets');
            }
            
            // Process and update pricing
            $processed_data = $this->process_sheets_data($data, $config);
            
            if ($processed_data) {
                $this->update_pricing($processed_data);
                $this->cache_data($processed_data);
                $this->clear_errors();
                update_option($this->last_sync_key, current_time('mysql'));
                
                $this->log_success('Pricing data synced successfully from Google Sheets');
                return true;
            }
            
        } catch (Exception $e) {
            $this->log_error('Sync failed: ' . $e->getMessage());
            
            // Use fallback if enabled
            if (!empty($config['enable_fallback'])) {
                return $this->use_fallback_pricing();
            }
            
            return false;
        }
    }
    
    private function fetch_sheets_data($config) {
        // Try CSV method (simpler and works without API key)
        return $this->fetch_via_csv($config);
    }
    
    private function fetch_via_csv($config) {
        // Convert regular Sheets URL to CSV export URL if needed
        $csv_url = $this->convert_to_csv_url($config['sheets_url']);
        
        $response = wp_remote_get($csv_url, array(
            'timeout' => 30,
            'headers' => array(
                'Accept' => 'text/csv'
            )
        ));
        
        if (is_wp_error($response)) {
            $this->log_error('CSV request failed: ' . $response->get_error_message());
            return false;
        }
        
        $csv_data = wp_remote_retrieve_body($response);
        
        // Parse CSV data
        $lines = explode("\n", $csv_data);
        $parsed_data = array();
        
        foreach ($lines as $line) {
            if (trim($line)) {
                $parsed_data[] = str_getcsv($line);
            }
        }
        
        return $parsed_data;
    }
    
    private function convert_to_csv_url($url) {
        $sheet_id = $this->extract_sheet_id($url);
        if ($sheet_id) {
            return "https://docs.google.com/spreadsheets/d/{$sheet_id}/export?format=csv&gid=0";
        }
        return $url; // Return as-is if already a CSV URL
    }
    
    private function extract_sheet_id($url) {
        if (preg_match('/\/spreadsheets\/d\/([a-zA-Z0-9-_]+)/', $url, $matches)) {
            return $matches[1];
        }
        return false;
    }
    
    private function process_sheets_data($raw_data, $config) {
        if (empty($raw_data) || !is_array($raw_data)) {
            return false;
        }
        
        $mapping = isset($config['column_mapping']) ? $config['column_mapping'] : array();
        $processed_plans = array();
        
        // Skip header row (row 1) - contains "Plan", "Price Lock Period", etc.
        // Your sheet structure has headers in row 1, data starts from row 2
        $data_rows = array_slice($raw_data, 1);
        
        foreach ($data_rows as $row_index => $row) {
            if (empty($row) || !isset($row[0]) || empty(trim($row[0]))) {
                continue; // Skip empty rows
            }
            
            $plan_data = array();
            
            // Map columns to plan data
            foreach ($mapping as $field => $column) {
                $col_index = $this->column_to_index($column);
                if (isset($row[$col_index])) {
                    $value = trim($row[$col_index]);
                    // Clean up the data based on field type
                    if ($field === 'price' && is_numeric($value)) {
                        // Format numeric price as currency
                        $plan_data[$field] = '$' . number_format(floatval($value), 2) . '/gallon';
                    } elseif (in_array($field, array('enrollment_fee', 'monthly_fee')) && is_numeric($value)) {
                        // Keep fees as numbers for calculation
                        $plan_data[$field] = floatval($value);
                    } else {
                        $plan_data[$field] = $value;
                    }
                }
            }
            
            // Validate required fields - need at least plan name and price
            if (!empty($plan_data['plan_name']) && !empty($plan_data['price'])) {
                // Log the processed plan for debugging
                $this->log_success("Processed plan: {$plan_data['plan_name']} - {$plan_data['price']}");
                $processed_plans[] = $plan_data;
            } else {
                $this->log_error("Skipped row " . ($row_index + 2) . ": Missing plan name or price");
            }
        }
        
        return $processed_plans;
    }
    
    private function column_to_index($column) {
        if (is_numeric($column)) {
            return intval($column) - 1; // Convert 1-based to 0-based
        }
        
        // Convert letter to number (A=0, B=1, etc.)
        $column = strtoupper($column);
        $index = 0;
        $length = strlen($column);
        
        for ($i = 0; $i < $length; $i++) {
            $index = $index * 26 + (ord($column[$i]) - ord('A') + 1);
        }
        
        return $index - 1; // Convert to 0-based
    }
    
    private function update_pricing($sheets_data) {
        $current_plans = $this->config->get('plans', array());
        
        foreach ($sheets_data as $sheet_plan) {
            $plan_name = $sheet_plan['plan_name'];
            
            // Find matching plan in current config
            foreach ($current_plans as $plan_id => &$plan) {
                if (stripos($plan['name'], $plan_name) !== false || 
                    stripos($plan_name, $plan['name']) !== false) {
                    
                    // Update pricing fields
                    if (!empty($sheet_plan['price'])) {
                        $plan['price'] = $sheet_plan['price'];
                    }
                    
                    if (!empty($sheet_plan['enrollment_fee'])) {
                        $plan['enrollment_fee'] = floatval($sheet_plan['enrollment_fee']);
                    }
                    
                    if (!empty($sheet_plan['monthly_fee'])) {
                        $plan['monthly_enroll_fee'] = floatval($sheet_plan['monthly_fee']);
                    }
                    
                    if (!empty($sheet_plan['description'])) {
                        $plan['description'] = sanitize_text_field($sheet_plan['description']);
                    }
                    
                    break;
                }
            }
        }
        
        // Update the configuration
        $this->config->set('plans', $current_plans);
        
        // Fire action for other plugins to hook into
        do_action('fox_fuel_pricing_updated', $current_plans, $sheets_data);
    }
    
    private function cache_data($data) {
        $cache_data = array(
            'data' => $data,
            'timestamp' => time(),
            'expiry' => time() + (24 * HOUR_IN_SECONDS) // 24 hour cache
        );
        
        update_option($this->cache_key, $cache_data);
    }
    
    private function get_cached_data() {
        $cache_data = get_option($this->cache_key, array());
        
        if (empty($cache_data) || !isset($cache_data['expiry'])) {
            return false;
        }
        
        if (time() > $cache_data['expiry']) {
            delete_option($this->cache_key);
            return false;
        }
        
        return $cache_data['data'];
    }
    
    private function use_fallback_pricing() {
        $cached_data = $this->get_cached_data();
        
        if ($cached_data) {
            $this->log_error('Using cached pricing data due to sync failure');
            return true;
        }
        
        $this->log_error('No cached data available, using default pricing');
        return false;
    }
    
    private function schedule_sync() {
        $config = get_option('fox_fuel_sheets_config', array());
        $frequency = isset($config['sync_frequency']) ? $config['sync_frequency'] : 'daily';
        
        if ($frequency !== 'manual') {
            wp_schedule_event(time(), $frequency, 'fox_fuel_scheduled_sync');
        }
    }
    
    public function scheduled_sync() {
        $this->sync_pricing_data();
    }
    
    public function manual_sync() {
        check_ajax_referer('fox_fuel_sheets_nonce', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_die('Unauthorized');
        }
        
        $result = $this->sync_pricing_data();
        
        if ($result) {
            wp_send_json_success(array(
                'message' => 'Pricing data synced successfully!',
                'last_sync' => get_option($this->last_sync_key)
            ));
        } else {
            wp_send_json_error(array(
                'message' => 'Sync failed. Check error log for details.'
            ));
        }
    }
    
    public function test_connection() {
        check_ajax_referer('fox_fuel_sheets_nonce', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_die('Unauthorized');
        }
        
        $config = get_option('fox_fuel_sheets_config', array());
        
        if (empty($config['sheets_url'])) {
            wp_send_json_error(array('message' => 'No Google Sheets URL configured'));
        }
        
        try {
            $data = $this->fetch_sheets_data($config);
            
            if ($data !== false && !empty($data)) {
                $row_count = count($data);
                wp_send_json_success(array(
                    'message' => "Connection successful! Found {$row_count} rows of data.",
                    'preview' => array_slice($data, 0, 3) // Show first 3 rows as preview
                ));
            } else {
                wp_send_json_error(array('message' => 'Connection failed or no data found'));
            }
            
        } catch (Exception $e) {
            wp_send_json_error(array('message' => 'Connection test failed: ' . $e->getMessage()));
        }
    }
    
    public function get_sync_log() {
        check_ajax_referer('fox_fuel_sheets_nonce', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_die('Unauthorized');
        }
        
        $errors = get_option($this->error_log_key, array());
        
        // Return recent log entries (last 10)
        $recent_log = array_slice($errors, -10);
        
        wp_send_json_success(array(
            'log' => $recent_log,
            'count' => count($recent_log)
        ));
    }
    
    private function log_error($message) {
        $errors = get_option($this->error_log_key, array());
        $errors[] = array(
            'message' => $message,
            'timestamp' => current_time('mysql'),
            'level' => 'error'
        );
        
        // Keep only last 10 errors
        $errors = array_slice($errors, -10);
        
        update_option($this->error_log_key, $errors);
        
        // Log to WordPress debug log if enabled
        if (defined('WP_DEBUG') && WP_DEBUG) {
            error_log('Fox Fuel Sheets Integration: ' . $message);
        }
    }
    
    private function log_success($message) {
        $errors = get_option($this->error_log_key, array());
        $errors[] = array(
            'message' => $message,
            'timestamp' => current_time('mysql'),
            'level' => 'success'
        );
        
        // Keep only last 10 entries
        $errors = array_slice($errors, -10);
        
        update_option($this->error_log_key, $errors);
    }
    
    private function clear_errors() {
        delete_option($this->error_log_key);
    }
    
    public function show_admin_notices() {
        if (!current_user_can('manage_options')) {
            return;
        }
        
        $errors = get_option($this->error_log_key, array());
        
        // Show only recent errors (last 24 hours)
        $recent_errors = array_filter($errors, function($error) {
            return $error['level'] === 'error' && 
                   strtotime($error['timestamp']) > (time() - DAY_IN_SECONDS);
        });
        
        if (!empty($recent_errors)) {
            $latest_error = end($recent_errors);
            echo '<div class="notice notice-warning is-dismissible">';
            echo '<p><strong>Fox Fuel Sheets Integration:</strong> ' . esc_html($latest_error['message']);
            echo ' <a href="' . admin_url('options-general.php?page=fox-fuel-selector') . '">Check settings</a></p>';
            echo '</div>';
        }
    }
}

// Initialize the integration
new FoxFuelSheetsIntegration();
