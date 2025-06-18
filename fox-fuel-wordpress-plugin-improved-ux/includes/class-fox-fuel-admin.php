<?php
/**
 * Fox Fuel Admin Interface
 * Enhanced admin panel for configuration management
 */

if (!defined('ABSPATH')) {
    exit;
}

class FoxFuelAdmin {
    
    private $config;
    
    public function __construct() {
        $this->config = FoxFuelConfig::getInstance();
        add_action('admin_menu', array($this, 'add_admin_menu'));
        add_action('admin_init', array($this, 'admin_init'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_scripts'));
    }
    
    public function add_admin_menu() {
        add_options_page(
            __('Fox Fuel Selector Settings', 'fox-fuel-selector'),
            __('Fox Fuel Selector', 'fox-fuel-selector'),
            'manage_options',
            'fox-fuel-selector',
            array($this, 'admin_page')
        );
    }
    
    public function admin_init() {
        register_setting('fox_fuel_config_group', 'fox_fuel_config', array($this, 'sanitize_config'));
    }
    
    public function enqueue_admin_scripts($hook) {
        if ('settings_page_fox-fuel-selector' !== $hook) {
            return;
        }
        
        wp_enqueue_script('jquery-ui-tabs');
        wp_enqueue_style('jquery-ui-tabs', '//code.jquery.com/ui/1.12.1/themes/ui-lightness/jquery-ui.css');
        
        // Enqueue custom admin script
        wp_enqueue_script(
            'fox-fuel-admin-js',
            FOX_FUEL_PLUGIN_URL . 'assets/js/fox-fuel-admin.js',
            array('jquery', 'jquery-ui-tabs'),
            FOX_FUEL_PLUGIN_VERSION,
            true
        );
        
        // Localize script for AJAX
        wp_localize_script('fox-fuel-admin-js', 'foxFuelAdmin', array(
            'ajaxurl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('fox_fuel_sheets_nonce'),
            'strings' => array(
                'testing' => __('Testing...', 'fox-fuel-selector'),
                'syncing' => __('Syncing...', 'fox-fuel-selector'),
                'testConnection' => __('Test Connection', 'fox-fuel-selector'),
                'syncNow' => __('Sync Now', 'fox-fuel-selector'),
                'connectionFailed' => __('Connection test failed. Please try again.', 'fox-fuel-selector'),
                'syncFailed' => __('Sync failed. Please try again.', 'fox-fuel-selector'),
                'logFailed' => __('Failed to load sync log.', 'fox-fuel-selector')
            )
        ));
    }
    
    public function admin_page() {
        if (isset($_POST['submit'])) {
            $this->handle_form_submission();
        }
        
        ?>
        <div class="wrap">
            <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
            
            <div class="notice notice-info">
                <p><strong>🎉 Fox Fuel Selector v2.0</strong> - Enhanced with configurable content management and improved UX flow!</p>
            </div>
            
            <div id="fox-fuel-admin-tabs">
                <ul>
                    <li><a href="#tab-contact">Contact Info</a></li>
                    <li><a href="#tab-content">Content</a></li>
                    <li><a href="#tab-plans">Plans</a></li>
                    <li><a href="#tab-sheets">Google Sheets</a></li>
                    <li><a href="#tab-usage">Usage Instructions</a></li>
                </ul>
                
                <!-- Contact Information Tab -->
                <div id="tab-contact">
                    <form method="post" action="">
                        <?php wp_nonce_field('fox_fuel_config_nonce'); ?>
                        <table class="form-table">
                            <tr>
                                <th scope="row"><?php _e('Phone Number', 'fox-fuel-selector'); ?></th>
                                <td>
                                    <input type="text" name="contact_phone" value="<?php echo esc_attr($this->config->get('contact_phone')); ?>" class="regular-text" />
                                    <p class="description">Primary contact phone number displayed throughout the selector</p>
                                </td>
                            </tr>
                            <tr>
                                <th scope="row"><?php _e('Email Address', 'fox-fuel-selector'); ?></th>
                                <td>
                                    <input type="email" name="contact_email" value="<?php echo esc_attr($this->config->get('contact_email')); ?>" class="regular-text" />
                                    <p class="description">Primary contact email address</p>
                                </td>
                            </tr>
                            <tr>
                                <th scope="row"><?php _e('Business Hours', 'fox-fuel-selector'); ?></th>
                                <td>
                                    <input type="text" name="business_hours" value="<?php echo esc_attr($this->config->get('business_hours')); ?>" class="regular-text" />
                                    <p class="description">Business hours display text</p>
                                </td>
                            </tr>
                            <tr>
                                <th scope="row"><?php _e('Service Tagline', 'fox-fuel-selector'); ?></th>
                                <td>
                                    <input type="text" name="service_tagline" value="<?php echo esc_attr($this->config->get('service_tagline')); ?>" class="regular-text" />
                                    <p class="description">Company service area tagline</p>
                                </td>
                            </tr>
                        </table>
                        <?php submit_button('Save Contact Information'); ?>
                    </form>
                </div>
                
                <!-- Content Management Tab -->
                <div id="tab-content">
                    <form method="post" action="">
                        <?php wp_nonce_field('fox_fuel_config_nonce'); ?>
                        <h3>Main Content</h3>
                        <table class="form-table">
                            <tr>
                                <th scope="row"><?php _e('Main Heading', 'fox-fuel-selector'); ?></th>
                                <td>
                                    <input type="text" name="main_heading" value="<?php echo esc_attr($this->config->get('main_heading')); ?>" class="large-text" />
                                </td>
                            </tr>
                            <tr>
                                <th scope="row"><?php _e('Main Subtitle', 'fox-fuel-selector'); ?></th>
                                <td>
                                    <input type="text" name="main_subtitle" value="<?php echo esc_attr($this->config->get('main_subtitle')); ?>" class="large-text" />
                                </td>
                            </tr>
                        </table>
                        
                        <h3>Step Content</h3>
                        <table class="form-table">
                            <tr>
                                <th scope="row"><?php _e('Step 1 Title', 'fox-fuel-selector'); ?></th>
                                <td>
                                    <input type="text" name="step1_title" value="<?php echo esc_attr($this->config->get('step1_title')); ?>" class="large-text" />
                                </td>
                            </tr>
                            <tr>
                                <th scope="row"><?php _e('Step 2 Title', 'fox-fuel-selector'); ?></th>
                                <td>
                                    <input type="text" name="step2_title" value="<?php echo esc_attr($this->config->get('step2_title')); ?>" class="large-text" />
                                </td>
                            </tr>
                            <tr>
                                <th scope="row"><?php _e('Step 3 Title', 'fox-fuel-selector'); ?></th>
                                <td>
                                    <input type="text" name="step3_title" value="<?php echo esc_attr($this->config->get('step3_title')); ?>" class="large-text" />
                                </td>
                            </tr>
                            <tr>
                                <th scope="row"><?php _e('Step 4 Title', 'fox-fuel-selector'); ?></th>
                                <td>
                                    <input type="text" name="step4_title" value="<?php echo esc_attr($this->config->get('step4_title')); ?>" class="large-text" />
                                </td>
                            </tr>
                        </table>
                        
                        <?php submit_button('Save Content'); ?>
                    </form>
                </div>
                
                <!-- Google Sheets Integration Tab -->
                <div id="tab-sheets">
                    <h3>Google Sheets Integration</h3>
                    <p>Automatically sync pricing data from Google Sheets to keep your plans up-to-date.</p>
                    
                    <?php $this->render_sheets_status(); ?>
                    
                    <form method="post" action="">
                        <?php wp_nonce_field('fox_fuel_sheets_nonce'); ?>
                        <input type="hidden" name="action" value="save_sheets_config" />
                        
                        <h4>Connection Settings</h4>
                        <table class="form-table">
                            <tr>
                                <th scope="row"><?php _e('Google Sheets URL', 'fox-fuel-selector'); ?></th>
                                <td>
                                    <?php $sheets_config = get_option('fox_fuel_sheets_config', array()); ?>
                                    <input type="url" name="fox_fuel_sheets_config[sheets_url]" value="<?php echo esc_attr($sheets_config['sheets_url'] ?? ''); ?>" class="large-text" placeholder="https://docs.google.com/spreadsheets/d/YOUR_SHEET_ID/edit" />
                                    <p class="description">Your Google Sheets URL. Can be the regular sharing URL or CSV export URL.</p>
                                </td>
                            </tr>
                            <tr>
                                <th scope="row"><?php _e('API Key (Optional)', 'fox-fuel-selector'); ?></th>
                                <td>
                                    <input type="password" name="fox_fuel_sheets_config[api_key]" value="<?php echo esc_attr($sheets_config['api_key'] ?? ''); ?>" class="large-text" placeholder="Your Google Sheets API Key" />
                                    <p class="description">Optional: For more reliable access. <a href="https://developers.google.com/sheets/api/guides/authorizing#APIKey" target="_blank">Get API Key</a></p>
                                </td>
                            </tr>
                            <tr>
                                <th scope="row"><?php _e('Sync Frequency', 'fox-fuel-selector'); ?></th>
                                <td>
                                    <select name="fox_fuel_sheets_config[sync_frequency]">
                                        <option value="hourly" <?php selected($sheets_config['sync_frequency'] ?? 'daily', 'hourly'); ?>>Every Hour</option>
                                        <option value="twicedaily" <?php selected($sheets_config['sync_frequency'] ?? 'daily', 'twicedaily'); ?>>Twice Daily</option>
                                        <option value="daily" <?php selected($sheets_config['sync_frequency'] ?? 'daily', 'daily'); ?>>Daily</option>
                                        <option value="weekly" <?php selected($sheets_config['sync_frequency'] ?? 'daily', 'weekly'); ?>>Weekly</option>
                                        <option value="manual" <?php selected($sheets_config['sync_frequency'] ?? 'daily', 'manual'); ?>>Manual Only</option>
                                    </select>
                                    <p class="description">How often to automatically sync pricing from Google Sheets.</p>
                                </td>
                            </tr>
                        </table>
                        
                        <h4>Column Mapping</h4>
                        <p>Map your spreadsheet columns to pricing fields. Use column letters (A, B, C) or numbers (1, 2, 3).</p>
                        <table class="form-table">
                            <?php 
                            $mapping = $sheets_config['column_mapping'] ?? array();
                            $default_mapping = array(
                                'plan_name' => 'A',
                                'price' => 'I', 
                                'enrollment_fee' => 'K',
                                'monthly_fee' => 'L',
                                'min_gallons' => 'F',
                                'description' => 'E'
                            );
                            
                            $field_descriptions = array(
                                'plan_name' => 'Plan name/identifier',
                                'price' => 'Price per gallon or display price',
                                'enrollment_fee' => 'One-time enrollment fee',
                                'monthly_fee' => 'Monthly service fee',
                                'min_gallons' => 'Minimum gallon requirement',
                                'description' => 'Plan description'
                            );
                            
                            foreach ($default_mapping as $field => $default_col): 
                                $current_value = $mapping[$field] ?? $default_col;
                            ?>
                            <tr>
                                <th scope="row"><?php echo esc_html(ucwords(str_replace('_', ' ', $field))); ?></th>
                                <td>
                                    <input type="text" name="fox_fuel_sheets_config[column_mapping][<?php echo esc_attr($field); ?>]" value="<?php echo esc_attr($current_value); ?>" size="3" />
                                    <span class="description"><?php echo esc_html($field_descriptions[$field]); ?></span>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </table>
                        
                        <h4>Options</h4>
                        <table class="form-table">
                            <tr>
                                <th scope="row"><?php _e('Fallback Behavior', 'fox-fuel-selector'); ?></th>
                                <td>
                                    <label>
                                        <input type="checkbox" name="fox_fuel_sheets_config[enable_fallback]" value="1" <?php checked($sheets_config['enable_fallback'] ?? true, true); ?> />
                                        Enable fallback to cached/default prices if sync fails
                                    </label>
                                    <p class="description">Recommended: Ensures pricing is always available even if Google Sheets is temporarily unavailable.</p>
                                </td>
                            </tr>
                        </table>
                        
                        <?php submit_button('Save Google Sheets Settings'); ?>
                    </form>
                    
                    <div class="fox-fuel-sheets-actions">
                        <h4>Testing & Actions</h4>
                        <p>
                            <button type="button" id="test-sheets-connection" class="button">Test Connection</button>
                            <button type="button" id="manual-sync-sheets" class="button button-primary">Sync Now</button>
                            <button type="button" id="view-sync-log" class="button">View Sync Log</button>
                        </p>
                        <div id="sheets-test-results" style="margin-top: 15px;"></div>
                    </div>
                    
                    <div class="fox-fuel-sheets-help">
                        <h4>Setup Instructions</h4>
                        <ol>
                            <li><strong>Share your Google Sheet:</strong> Make sure your sheet is set to "Anyone with the link can view"</li>
                            <li><strong>Set up columns:</strong> Ensure your sheet has the data in the columns mapped above</li>
                            <li><strong>Test connection:</strong> Use the "Test Connection" button to verify setup</li>
                            <li><strong>Configure sync:</strong> Set your preferred sync frequency</li>
                            <li><strong>Monitor:</strong> Check the sync status and logs regularly</li>
                        </ol>
                        
                        <h4>Your Sheet Structure</h4>
                        <p>Based on your spreadsheet, the expected structure should be:</p>
                        <table class="widefat" style="margin-top: 10px;">
                            <thead>
                                <tr>
                                    <th>Column A</th><th>Column E</th><th>Column F</th><th>Column I</th><th>Column K</th><th>Column L</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>Plan Name</td><td>Description</td><td>Min. Gallons</td><td>Price</td><td>Enrollment Fee</td><td>Monthly Fee</td>
                                </tr>
                                <tr>
                                    <td>SmartPay Cap</td><td>Final adjustment/true-up</td><td>400</td><td>3.0590</td><td>239.88</td><td>19.99</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div id="tab-plans">
                    <h3>Heating Oil Plans</h3>
                    <p>Manage your heating oil plan offerings and pricing.</p>
                    
                    <?php $plans = $this->config->get('plans'); ?>
                    <div class="fox-fuel-plans-admin">
                        <?php foreach ($plans as $plan_id => $plan): ?>
                        <div class="plan-admin-card" style="border: 1px solid #ccd0d4; margin: 10px 0; padding: 15px; background: #fff;">
                            <h4><?php echo esc_html($plan['name']); ?> <span style="color: #666; font-size: 0.9em;">(<?php echo $plan['active'] ? 'Active' : 'Inactive'; ?>)</span></h4>
                            <p><strong>Price:</strong> <?php echo esc_html($plan['price']); ?></p>
                            <p><strong>Description:</strong> <?php echo esc_html($plan['description']); ?></p>
                            <p><strong>Features:</strong> <?php echo implode(', ', $plan['features']); ?></p>
                            <button type="button" class="button">Edit Plan</button>
                            <button type="button" class="button">Toggle Active/Inactive</button>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    
                    <button type="button" id="add-new-plan" class="button button-primary">Add New Plan</button>
                </div>
                
                <!-- Usage Instructions Tab -->
                <div id="tab-usage">
                    <h3><?php _e('Usage Instructions', 'fox-fuel-selector'); ?></h3>
                    <p><?php _e('Use the following shortcode to display the Fox Fuel selector anywhere on your site:', 'fox-fuel-selector'); ?></p>
                    <code style="background: #f1f1f1; padding: 10px; display: block; margin: 10px 0;">[fox_fuel_selector]</code>
                    
                    <h4><?php _e('Available Shortcode Parameters:', 'fox-fuel-selector'); ?></h4>
                    <ul>
                        <li><code>mode="guided"</code> - Show guided form (default)</li>
                        <li><code>theme="light"</code> - Light theme (default)</li>
                        <li><code>width="100%"</code> - Selector width</li>
                        <li><code>height="auto"</code> - Selector height</li>
                    </ul>
                    
                    <h4><?php _e('Value-First UX Benefits:', 'fox-fuel-selector'); ?></h4>
                    <ul>
                        <li>✅ Users see plan recommendations immediately</li>
                        <li>✅ Contact information is optional and comes after value delivery</li>
                        <li>✅ Higher completion rates and user trust</li>
                        <li>✅ Reduces "lead capture trap" feeling</li>
                        <li>✅ Improved accessibility with ARIA labels and keyboard navigation</li>
                        <li>✅ Mobile-responsive design</li>
                    </ul>
                    
                    <h4><?php _e('Recent Improvements:', 'fox-fuel-selector'); ?></h4>
                    <ul>
                        <li>✅ Phone number corrected to (215) 659-1616</li>
                        <li>✅ Enhanced 4-step wizard flow</li>
                        <li>✅ Improved form validation and error handling</li>
                        <li>✅ Centralized configuration management</li>
                        <li>✅ Better progress indicators</li>
                        <li>✅ Trust-building messaging throughout</li>
                    </ul>
                </div>
            </div>
        </div>
        
        <style>
        #fox-fuel-admin-tabs .ui-tabs-nav {
            background: #f1f1f1;
            border: 1px solid #ccd0d4;
        }
        #fox-fuel-admin-tabs .ui-tabs-panel {
            background: #fff;
            border: 1px solid #ccd0d4;
            border-top: none;
            padding: 20px;
        }
        .plan-admin-card {
            border-radius: 4px;
        }
        </style>
        <?php
    }
    
    private function handle_form_submission() {
        if (!wp_verify_nonce($_POST['_wpnonce'], 'fox_fuel_config_nonce') && 
            !wp_verify_nonce($_POST['_wpnonce'], 'fox_fuel_sheets_nonce')) {
            return;
        }
        
        $config_updates = array();
        
        // Handle Google Sheets configuration
        if (isset($_POST['action']) && $_POST['action'] === 'save_sheets_config') {
            if (isset($_POST['fox_fuel_sheets_config'])) {
                $sheets_config = $_POST['fox_fuel_sheets_config'];
                
                // Sanitize sheets configuration
                $sanitized_sheets = array();
                if (isset($sheets_config['sheets_url'])) {
                    $sanitized_sheets['sheets_url'] = esc_url_raw($sheets_config['sheets_url']);
                }
                if (isset($sheets_config['api_key'])) {
                    $sanitized_sheets['api_key'] = sanitize_text_field($sheets_config['api_key']);
                }
                if (isset($sheets_config['sync_frequency'])) {
                    $sanitized_sheets['sync_frequency'] = sanitize_text_field($sheets_config['sync_frequency']);
                }
                if (isset($sheets_config['column_mapping']) && is_array($sheets_config['column_mapping'])) {
                    $sanitized_sheets['column_mapping'] = array_map('sanitize_text_field', $sheets_config['column_mapping']);
                }
                $sanitized_sheets['enable_fallback'] = isset($sheets_config['enable_fallback']) ? true : false;
                
                update_option('fox_fuel_sheets_config', $sanitized_sheets);
                add_settings_error('fox_fuel_config', 'sheets_updated', 'Google Sheets settings saved successfully!', 'updated');
            }
            return;
        }
        
        // Handle contact information updates
        if (isset($_POST['contact_phone'])) {
            $config_updates['contact_phone'] = sanitize_text_field($_POST['contact_phone']);
        }
        if (isset($_POST['contact_email'])) {
            $config_updates['contact_email'] = sanitize_email($_POST['contact_email']);
        }
        if (isset($_POST['business_hours'])) {
            $config_updates['business_hours'] = sanitize_text_field($_POST['business_hours']);
        }
        if (isset($_POST['service_tagline'])) {
            $config_updates['service_tagline'] = sanitize_text_field($_POST['service_tagline']);
        }
        
        // Handle content updates
        if (isset($_POST['main_heading'])) {
            $config_updates['main_heading'] = sanitize_text_field($_POST['main_heading']);
        }
        if (isset($_POST['main_subtitle'])) {
            $config_updates['main_subtitle'] = sanitize_text_field($_POST['main_subtitle']);
        }
        if (isset($_POST['step1_title'])) {
            $config_updates['step1_title'] = sanitize_text_field($_POST['step1_title']);
        }
        if (isset($_POST['step2_title'])) {
            $config_updates['step2_title'] = sanitize_text_field($_POST['step2_title']);
        }
        if (isset($_POST['step3_title'])) {
            $config_updates['step3_title'] = sanitize_text_field($_POST['step3_title']);
        }
        if (isset($_POST['step4_title'])) {
            $config_updates['step4_title'] = sanitize_text_field($_POST['step4_title']);
        }
        
        // Update configuration
        foreach ($config_updates as $key => $value) {
            $this->config->set($key, $value);
        }
        
        add_settings_error('fox_fuel_config', 'settings_updated', 'Settings saved successfully!', 'updated');
    }
    
    public function sanitize_config($input) {
        $sanitized = array();
        
        if (is_array($input)) {
            foreach ($input as $key => $value) {
                if (is_string($value)) {
                    $sanitized[$key] = sanitize_text_field($value);
                } else {
                    $sanitized[$key] = $value;
                }
            }
        }
        
        return $sanitized;
    }
    
    private function render_sheets_status() {
        // Check if sheets integration class exists
        if (!class_exists('FoxFuelSheetsIntegration')) {
            echo '<div class="notice notice-warning"><p>Google Sheets integration is not loaded.</p></div>';
            return;
        }
        
        $last_sync = get_option('fox_fuel_sheets_last_sync');
        $errors = get_option('fox_fuel_sheets_errors', array());
        $next_scheduled = wp_next_scheduled('fox_fuel_scheduled_sync');
        
        echo '<div class="fox-fuel-sync-status">';
        
        if ($last_sync) {
            $time_diff = human_time_diff(strtotime($last_sync), current_time('timestamp'));
            echo '<div class="notice notice-success"><p><strong>Last successful sync:</strong> ' . esc_html($time_diff) . ' ago (' . esc_html($last_sync) . ')</p></div>';
        } else {
            echo '<div class="notice notice-info"><p><strong>Status:</strong> No sync performed yet. Configure settings below and test connection.</p></div>';
        }
        
        // Show recent errors
        $recent_errors = array_filter($errors, function($error) {
            return $error['level'] === 'error' && 
                   strtotime($error['timestamp']) > (time() - DAY_IN_SECONDS);
        });
        
        if (!empty($recent_errors)) {
            $latest_error = end($recent_errors);
            echo '<div class="notice notice-error"><p><strong>Recent error:</strong> ' . esc_html($latest_error['message']) . ' <em>(' . esc_html($latest_error['timestamp']) . ')</em></p></div>';
        }
        
        // Show next scheduled sync
        if ($next_scheduled) {
            $next_sync_time = human_time_diff($next_scheduled, current_time('timestamp'));
            echo '<div class="notice notice-info"><p><strong>Next scheduled sync:</strong> in ' . esc_html($next_sync_time) . '</p></div>';
        }
        
        echo '</div>';
    }
}
