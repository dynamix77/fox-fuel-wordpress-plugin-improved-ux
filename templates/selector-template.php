<?php
/**
 * Fox Fuel Selector Template
 * Updated with improved value-first UX flow - contact info moved to the end and made optional
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

$unique_id = 'fox-fuel-' . uniqid();
$config = FoxFuelConfig::getInstance();
?>

<div id="<?php echo esc_attr($unique_id); ?>" class="fox-fuel-selector-container" 
     data-width="<?php echo esc_attr($atts['width']); ?>" 
     data-height="<?php echo esc_attr($atts['height']); ?>"
     data-theme="<?php echo esc_attr($atts['theme']); ?>"
     data-mode="<?php echo esc_attr($atts['mode']); ?>"
     role="application"
     aria-label="Fox Fuel Heating Oil Plan Selector">
    
    <!-- Skip Link for Accessibility -->
    <a href="#fox-fuel-main-content" class="skip-link">Skip to main content</a>
    
    <!-- Loading State -->
    <div class="fox-fuel-loading">
        <div class="fox-fuel-spinner"></div>
        <p>Loading your heating oil plan selector...</p>
    </div>
    
    <!-- Header Section -->
    <div class="fox-fuel-header">
        <div class="fox-fuel-logo">
            <img src="<?php echo esc_url($config->get('logo_url')); ?>" 
                 alt="<?php echo esc_attr($config->get('company_name')); ?>" 
                 onerror="this.onerror=null; this.src='<?php echo esc_url($config->get('logo_fallback_url')); ?>';" />
        </div>
        <h1><?php echo esc_html($config->get('main_heading')); ?></h1>
        <p class="fox-fuel-subtitle"><?php echo esc_html($config->get('main_subtitle')); ?></p>
    </div>

    <!-- Guided Form Mode -->
    <div class="fox-fuel-guided-form" id="fox-fuel-main-content" role="main" aria-label="Plan Selection Wizard">
        
        <!-- Step 1: Service Area Check -->
        <div class="fox-fuel-step" data-step="1" role="group" aria-labelledby="step-1-title">
            <div class="step-header">
                <div class="step-number" aria-hidden="true">1</div>
                <h2 id="step-1-title"><?php echo esc_html($config->get('step1_title')); ?></h2>
            </div>
            <div class="form-group">
                <label for="zip-code">What's your ZIP code?</label>
                <input type="text" 
                       id="zip-code" 
                       name="zip_code" 
                       maxlength="5" 
                       placeholder="19001" 
                       aria-describedby="zip-code-help"
                       aria-required="true" />
                <div id="zip-code-help" class="sr-only">Enter your 5-digit ZIP code to check if we serve your area</div>
                <button type="button" 
                        class="fox-fuel-btn-next" 
                        data-next-step="2"
                        aria-describedby="step-1-description">Check Service Area</button>
                <div id="step-1-description" class="sr-only">This will verify if Fox Fuel delivers to your area</div>
            </div>
            <div class="zip-error" style="display: none;" role="alert" aria-live="polite">
                <p>Sorry, we don't currently serve that area. We deliver to parts of southeastern Pennsylvania and South Jersey.</p>
            </div>
        </div>

        <!-- Step 2: Usage Information -->
        <div class="fox-fuel-step" data-step="2" style="display: none;">
            <div class="step-header">
                <div class="step-number">2</div>
                <h2><?php echo esc_html($config->get('step2_title')); ?></h2>
                <p class="step-description"><?php echo esc_html($config->get('step2_subtitle')); ?></p>
            </div>
            <div class="form-group">
                <label>How many gallons did you use last heating season? 
                    <span class="tooltip-icon" title="Check your previous bills or delivery receipts. If unsure, choose 'Not sure' and we'll help estimate.">ℹ️</span>
                </label>
                <select name="annual_usage">
                    <option value="">Select your usage</option>
                    <option value="less-than-200">Less than 200 gallons</option>
                    <option value="200-400">200-400 gallons</option>
                    <option value="400-600">400-600 gallons</option>
                    <option value="600-800">600-800 gallons</option>
                    <option value="800-1000">800-1000 gallons</option>
                    <option value="1000-1200">1000-1200 gallons</option>
                    <option value="1200+">1200+ gallons</option>
                    <option value="not-sure">Not sure</option>
                </select>
            </div>
            <button type="button" class="fox-fuel-btn-next" data-next-step="3">Continue</button>
        </div>

        <!-- Step 3: Preferences -->
        <div class="fox-fuel-step" data-step="3" style="display: none;">
            <div class="step-header">
                <div class="step-number">3</div>
                <h2><?php echo esc_html($config->get('step3_title')); ?></h2>
                <p class="step-description"><?php echo esc_html($config->get('step3_subtitle')); ?></p>
            </div>
            <div class="form-group">
                <label>Your priorities (check all that apply):</label>
                <div class="checkbox-group">
                    <label>
                        <input type="checkbox" name="priorities[]" value="predictable-payments"> 
                        <div class="priority-content">
                            <span class="priority-title">Predictable monthly payments</span>
                            <span class="priority-desc">Same amount each month, no surprises</span>
                        </div>
                    </label>
                    <label>
                        <input type="checkbox" name="priorities[]" value="price-protection"> 
                        <div class="priority-content">
                            <span class="priority-title">Protection from price spikes</span>
                            <span class="priority-desc">Shield from sudden market increases</span>
                        </div>
                    </label>
                    <label>
                        <input type="checkbox" name="priorities[]" value="lowest-price"> 
                        <div class="priority-content">
                            <span class="priority-title">Lowest possible price</span>
                            <span class="priority-desc">Best value, willing to accept some price fluctuation</span>
                        </div>
                    </label>
                    <label>
                        <input type="checkbox" name="priorities[]" value="no-commitment"> 
                        <div class="priority-content">
                            <span class="priority-title">Flexibility with no long-term commitment</span>
                            <span class="priority-desc">Freedom to change plans or providers</span>
                        </div>
                    </label>
                </div>
            </div>
            <button type="button" class="fox-fuel-btn-next" data-next-step="4">Get My Recommendations</button>
        </div>

        <!-- Step 4: Plan Recommendations -->
        <div class="fox-fuel-step" data-step="4" style="display: none;">
            <div class="step-header">
                <div class="step-number">4</div>
                <h2><?php echo esc_html($config->get('step4_title')); ?></h2>
                <p class="recommendations-intro"><?php echo esc_html($config->get('step4_subtitle')); ?></p>
            </div>
            
            <div class="fox-fuel-plans-container">
                <!-- Plans will be dynamically inserted here -->
            </div>

            <!-- VALUE-FIRST CONTACT SECTION - MOVED TO THE END -->
            <div class="fox-fuel-contact-section" style="display: none;">
                <div class="contact-intro">
                    <h3><?php echo esc_html($config->get('contact_intro_heading')); ?></h3>
                    <p><strong><?php echo esc_html($config->get('contact_intro_text')); ?></strong></p>
                    <p class="no-pressure"><em><?php echo esc_html($config->get('contact_no_pressure')); ?></em></p>
                </div>
                
                <div class="contact-toggle">
                    <button type="button" class="fox-fuel-btn-primary" id="show-contact-form">
                        <?php echo esc_html($config->get('contact_yes_button')); ?>
                    </button>
                    <button type="button" class="fox-fuel-btn-ghost" id="skip-contact">
                        <?php echo esc_html($config->get('contact_no_button')); ?>
                    </button>
                </div>

                <!-- Optional Contact Form -->
                <div class="fox-fuel-contact-form" style="display: none;">
                    <h4>How can we reach you?</h4>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="customer-name">Name</label>
                            <input type="text" id="customer-name" name="customer_name" />
                        </div>
                        <div class="form-group">
                            <label for="customer-phone">Phone</label>
                            <input type="tel" id="customer-phone" name="customer_phone" />
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="customer-email">Email</label>
                        <input type="email" id="customer-email" name="customer_email" />
                    </div>
                    <div class="form-group">
                        <label>Best time to contact you:</label>
                        <select name="preferred_contact_time">
                            <option value="morning">Morning (8-11 AM)</option>
                            <option value="afternoon">Afternoon (12-4 PM)</option>
                            <option value="evening">Evening (5-7 PM)</option>
                            <option value="anytime">Anytime</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="additional-notes">Questions or special requests? (optional)</label>
                        <textarea id="additional-notes" name="additional_notes" rows="3" placeholder="Any questions about the plans or special delivery instructions..."></textarea>
                    </div>
                    <button type="button" class="fox-fuel-btn-primary" id="submit-contact-form">
                        Send My Information
                    </button>
                </div>
            </div>
        </div>

        <!-- Success Message -->
        <div class="fox-fuel-step" data-step="success" style="display: none;">
            <div class="success-message">
                <h2><?php echo esc_html($config->get('success_heading')); ?></h2>
                <p><?php echo esc_html($config->get('success_message')); ?></p>
                <div class="next-steps">
                    <h3>What happens next?</h3>
                    <ul>
                        <li>A Fox Fuel representative will call you at your preferred time</li>
                        <li>We'll review your recommended plans and pricing</li>
                        <li>Answer any questions about delivery, billing, or our service</li>
                        <li>Help you enroll in your chosen plan if you're ready</li>
                    </ul>
                </div>
                <div class="contact-info">
                    <p><strong>Need to speak with us right away?</strong><br>
                    Call us at <a href="tel:<?php echo esc_attr(str_replace(array('(', ')', ' ', '-'), '', $config->get('contact_phone'))); ?>"><?php echo esc_html($config->get('contact_phone')); ?></a><br>
                    <?php echo esc_html($config->get('business_hours')); ?></p>
                </div>
            </div>
        </div>
    </div>

    <!-- Progress Bar -->
    <div class="fox-fuel-progress-bar" role="progressbar" aria-valuenow="1" aria-valuemin="1" aria-valuemax="4" aria-label="Step 1 of 4: Service Area">
        <div class="progress-fill"></div>
        <div class="progress-steps">
            <span class="step-indicator active" data-step="1" aria-current="step">Service Area</span>
            <span class="step-indicator" data-step="2">Usage</span>
            <span class="step-indicator" data-step="3">Preferences</span>
            <span class="step-indicator" data-step="4">Recommendations</span>
        </div>
    </div>

    <!-- Footer -->
    <div class="fox-fuel-footer">
        <p>Questions? Call us at <a href="tel:<?php echo esc_attr(str_replace(array('(', ')', ' ', '-'), '', $config->get('contact_phone'))); ?>"><?php echo esc_html($config->get('contact_phone')); ?></a> or email <a href="mailto:<?php echo esc_attr($config->get('contact_email')); ?>"><?php echo esc_html($config->get('contact_email')); ?></a></p>
        <p class="service-area"><?php echo esc_html($config->get('service_tagline')); ?></p>
    </div>
</div>

<!-- The Fox Fuel Selector will be initialized automatically by the simplified JavaScript -->
<!-- No manual initialization required -->
