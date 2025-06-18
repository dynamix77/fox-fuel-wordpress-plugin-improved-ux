/**
 * Fox Fuel Selector JavaScript
 * Updated with value-first UX flow and optional contact information
 * Fixed initialization and error handling
 */

class FoxFuelSelector {
    constructor(container, options = {}) {
        console.log('FoxFuelSelector: Initializing...', { container, options });
        
        this.container = container;
        this.options = {
            mode: 'guided',
            theme: 'light',
            valueFirstFlow: true,
            optionalContactInfo: true,
            trustBuildingCopy: true,
            ...options
        };

        // Check if foxFuelAjax is available
        if (typeof foxFuelAjax === 'undefined') {
            console.warn('FoxFuelSelector: foxFuelAjax not found, using fallback data');
        }

        // Service area ZIP codes - with fallback data
        this.serviceZips = this.getServiceZips();
        console.log('FoxFuelSelector: Service ZIPs loaded:', this.serviceZips.length);

        // Plan data from configuration with fallback
        this.plansData = this.getPlansData();
        console.log('FoxFuelSelector: Plans loaded:', this.plansData.length);

        this.currentStep = 1;
        this.formData = {};
        this.recommendations = [];

        this.init();
    }

    getServiceZips() {
        // Try to get from WordPress config first
        if (typeof foxFuelAjax !== 'undefined' && foxFuelAjax.config?.service_zips) {
            return foxFuelAjax.config.service_zips;
        }
        
        // Fallback ZIP codes
        return [
            '18966', '18974', '18976', '19001', '19002', '19006', '19009',
            '19012', '19025', '19027', '19031', '19034', '19038', '19040',
            '19044', '19046', '19053', '19075', '19090', '19095', '19115',
            '19126', '19150', '19422', '19454'
        ];
    }

    getPlansData() {
        // Try to get from WordPress config first
        if (typeof foxFuelAjax !== 'undefined' && foxFuelAjax.config?.plans) {
            return this.convertPlansData(foxFuelAjax.config.plans);
        }
        
        // Fallback plan data
        return this.getFallbackPlansData();
    }

    getFallbackPlansData() {
        return [
            {
                id: 'smartpay-cap',
                name: 'SmartPay Price Cap',
                type: 'cap',
                badge: 'Most Popular',
                description: 'Price ceiling protection against major spikes with benefits if prices drop',
                price: '$3.89/gallon cap',
                monthlyPayment: '12 equal monthly payments',
                features: [
                    'Price ceiling protection',
                    'Benefit from price drops',
                    'Automatic delivery included',
                    'Most popular plan',
                    '12 equal monthly payments'
                ],
                bestFor: ['price-protection', 'predictable-payments'],
                enrollmentFee: 239.88,
                monthlyEnrollFee: 19.99
            },
            {
                id: 'fixed-price-budget',
                name: 'Fixed Price Budget',
                type: 'fixed',
                badge: 'Price Guarantee',
                description: 'Guaranteed fixed price for the entire heating season',
                price: '$3.59/gallon fixed',
                monthlyPayment: '10 equal monthly payments',
                features: [
                    'Guaranteed fixed price',
                    'Complete price protection',
                    'Predictable monthly payments',
                    '10 equal payments',
                    'No enrollment fees'
                ],
                bestFor: ['price-protection', 'predictable-payments'],
                enrollmentFee: 0,
                monthlyEnrollFee: 0
            },
            {
                id: 'fuelsaver-budget',
                name: 'FuelSaver Budget',
                type: 'budget',
                badge: 'Flexible',
                description: 'Equal monthly payments with no price protection',
                price: 'Market pricing',
                monthlyPayment: '$125-200 monthly',
                features: [
                    'Predictable monthly payments',
                    'No minimum gallons',
                    'Market pricing',
                    'Budget-friendly option',
                    'Final adjustment in May'
                ],
                bestFor: ['lowest-price', 'no-commitment'],
                enrollmentFee: 0,
                monthlyEnrollFee: 0
            }
        ];
    }

    init() {
        console.log('FoxFuelSelector: Starting initialization...');
        
        try {
            this.hideLoading();
            this.bindEvents();
            console.log('FoxFuelSelector: Initialization complete');
        } catch (error) {
            console.error('FoxFuelSelector: Initialization error:', error);
            this.showError('Sorry, there was an error loading the selector. Please refresh the page.');
        }
    }

    convertPlansData(plansConfig) {
        const plansArray = [];
        
        for (const [planId, planData] of Object.entries(plansConfig)) {
            if (planData.active) {
                plansArray.push({
                    id: planId,
                    name: planData.name,
                    type: planData.type,
                    badge: planData.badge,
                    description: planData.description,
                    price: planData.price,
                    monthlyPayment: planData.monthly_payment,
                    features: planData.features,
                    bestFor: planData.best_for,
                    enrollmentFee: planData.enrollment_fee,
                    monthlyEnrollFee: planData.monthly_enroll_fee
                });
            }
        }
        
        return plansArray;
    }

    hideLoading() {
        console.log('FoxFuelSelector: Hiding loading spinner...');
        const loading = this.container.querySelector('.fox-fuel-loading');
        if (loading) {
            loading.style.display = 'none';
            console.log('FoxFuelSelector: Loading spinner hidden');
        } else {
            console.warn('FoxFuelSelector: Loading element not found');
        }
    }

    bindEvents() {
        // Form navigation
        this.bindFormNavigation();
        
        // Form inputs
        this.bindFormInputs();
        
        // NEW: Contact form toggle - IMPROVED UX
        this.bindContactFormEvents();
    }

    bindContactFormEvents() {
        const showContactBtn = this.container.querySelector('#show-contact-form');
        const skipContactBtn = this.container.querySelector('#skip-contact');
        const contactForm = this.container.querySelector('.fox-fuel-contact-form');

        if (showContactBtn) {
            showContactBtn.addEventListener('click', () => {
                contactForm.style.display = 'block';
                showContactBtn.style.display = 'none';
                skipContactBtn.style.display = 'none';
            });
        }

        if (skipContactBtn) {
            skipContactBtn.addEventListener('click', () => {
                // Just hide the contact section - user got their value
                const contactSection = this.container.querySelector('.fox-fuel-contact-section');
                contactSection.style.display = 'none';
                
                // Show a brief thank you message
                this.showThankYouMessage();
            });
        }

        // Contact form submission
        const submitContactBtn = this.container.querySelector('#submit-contact-form');
        if (submitContactBtn) {
            submitContactBtn.addEventListener('click', () => {
                this.submitContactForm();
            });
        }
    }

    bindFormNavigation() {
        const nextButtons = this.container.querySelectorAll('.fox-fuel-btn-next');
        nextButtons.forEach(btn => {
            btn.addEventListener('click', (e) => {
                const nextStep = parseInt(e.currentTarget.dataset.nextStep);
                this.goToStep(nextStep);
            });
        });
    }

    goToStep(stepNumber) {
        if (!this.validateCurrentStep()) {
            return;
        }

        // Hide current step
        const currentStepEl = this.container.querySelector(`[data-step="${this.currentStep}"]`);
        if (currentStepEl) {
            currentStepEl.style.display = 'none';
        }

        // Show next step
        const nextStepEl = this.container.querySelector(`[data-step="${stepNumber}"]`);
        if (nextStepEl) {
            nextStepEl.style.display = 'block';
        }

        this.currentStep = stepNumber;
        this.updateProgressBar();

        // Special handling for recommendations step
        if (stepNumber === 4) {
            this.generateRecommendations();
        }
    }

    validateCurrentStep() {
        switch (this.currentStep) {
            case 1:
                return this.validateZipCode();
            case 2:
                return this.validateUsageInfo();
            case 3:
                return this.validatePreferences();
            default:
                return true;
        }
    }

    validateZipCode() {
        const zipInput = this.container.querySelector('#zip-code');
        const zipError = this.container.querySelector('.zip-error');
        
        if (!zipInput || !zipInput.value) {
            this.showError('Please enter your ZIP code.');
            return false;
        }

        const zip = zipInput.value.trim();
        if (zip.length !== 5) {
            this.showError('Please enter a valid 5-digit ZIP code.');
            return false;
        }

        if (!this.serviceZips.includes(zip)) {
            zipError.style.display = 'block';
            return false;
        }

        zipError.style.display = 'none';
        this.updateFormData('zip_code', zip);
        return true;
    }

    validateUsageInfo() {
        const usageSelect = this.container.querySelector('select[name="annual_usage"]');
        
        if (!usageSelect || !usageSelect.value) {
            this.showError('Please select your annual usage amount.');
            return false;
        }

        this.updateFormData('annual_usage', usageSelect.value);
        return true;
    }

    validatePreferences() {
        const priorities = this.formData.priorities || [];
        
        if (priorities.length === 0) {
            this.showError('Please select at least one priority to help us recommend the best plan for you.');
            return false;
        }

        return true;
    }

    generateRecommendations() {
        const priorities = this.formData.priorities || [];
        const usage = this.formData.annual_usage || '';
        
        // Score and rank plans based on user preferences
        const scoredPlans = this.plansData.map(plan => {
            let score = 0;
            let reasons = [];
            
            // Score based on priorities
            priorities.forEach(priority => {
                if (plan.bestFor.includes(priority)) {
                    score += 2;
                    
                    // Add specific reasons based on priority
                    switch(priority) {
                        case 'predictable-payments':
                            reasons.push('Offers predictable monthly payments');
                            break;
                        case 'price-protection':
                            reasons.push('Protects against price increases');
                            break;
                        case 'lowest-price':
                            reasons.push('Competitive pricing structure');
                            break;
                        case 'no-commitment':
                            reasons.push('Flexible terms without long commitments');
                            break;
                    }
                }
            });
            
            // Score based on usage patterns
            if (usage) {
                const usageScore = this.scoreByUsage(plan, usage);
                score += usageScore.points;
                if (usageScore.reason) {
                    reasons.push(usageScore.reason);
                }
            }
            
            // Add plan-specific benefits
            const planBenefits = this.getPlanSpecificBenefits(plan, priorities, usage);
            reasons.push(...planBenefits);

            return { ...plan, score, reasons };
        }).sort((a, b) => b.score - a.score);

        this.recommendations = scoredPlans.slice(0, 3);
        this.renderRecommendations();
    }

    renderRecommendations() {
        const plansContainer = this.container.querySelector('.fox-fuel-plans-container');
        if (!plansContainer) return;

        plansContainer.innerHTML = '';

        this.recommendations.forEach((plan, index) => {
            const planCard = this.createPlanCard(plan, index === 0);
            plansContainer.appendChild(planCard);
        });
    }
    
    scoreByUsage(plan, usage) {
        let points = 0;
        let reason = '';
        
        // Extract usage amount for comparison
        const usageNum = this.extractUsageNumber(usage);
        
        // Score plans based on usage patterns
        if (plan.id === 'smartpay-cap') {
            if (usageNum >= 600) {
                points = 1;
                reason = 'SmartPay Cap works well for moderate to high usage';
            }
        } else if (plan.id === 'fixed-price-budget') {
            if (usageNum >= 400) {
                points = 1;
                reason = 'Fixed pricing provides certainty for your usage level';
            }
        } else if (plan.id === 'fuelsaver-budget') {
            if (usageNum < 600 || usage === 'not-sure') {
                points = 1;
                reason = 'Budget-friendly option with flexible gallon requirements';
            }
        }
        
        return { points, reason };
    }
    
    extractUsageNumber(usage) {
        if (!usage) return 0;
        
        const usageMap = {
            'less-than-200': 150,
            '200-400': 300,
            '400-600': 500,
            '600-800': 700,
            '800-1000': 900,
            '1000-1200': 1100,
            '1200+': 1300,
            'not-sure': 500 // Default to medium usage
        };
        
        return usageMap[usage] || 500;
    }
    
    getPlanSpecificBenefits(plan, priorities, usage) {
        const benefits = [];
        
        switch(plan.id) {
            case 'smartpay-cap':
                if (priorities.includes('price-protection')) {
                    benefits.push('Price cap protects you from market volatility');
                }
                if (priorities.includes('predictable-payments')) {
                    benefits.push('12 equal monthly payments spread the cost evenly');
                }
                break;
                
            case 'fixed-price-budget':
                if (priorities.includes('price-protection')) {
                    benefits.push('Guaranteed fixed price eliminates all price risk');
                }
                if (priorities.includes('predictable-payments')) {
                    benefits.push('No enrollment fees and predictable 10-month payments');
                }
                break;
                
            case 'fuelsaver-budget':
                if (priorities.includes('lowest-price')) {
                    benefits.push('Market pricing means you pay current rates without premiums');
                }
                if (priorities.includes('no-commitment')) {
                    benefits.push('No minimum gallon requirements for maximum flexibility');
                }
                break;
        }
        
        return benefits;
    }

    createPlanCard(plan, isRecommended = false) {
        const card = document.createElement('div');
        card.className = `fox-fuel-plan-card ${isRecommended ? 'recommended' : ''}`;
        
        card.innerHTML = `
            ${isRecommended ? '<div class="recommendation-badge">🏆 Our Top Recommendation</div>' : ''}
            ${plan.badge ? `<div class="plan-badge">${plan.badge}</div>` : ''}
            <div class="plan-header">
                <h3>${plan.name}</h3>
                <div class="plan-price">${plan.price}</div>
            </div>
            <p class="plan-description">${plan.description}</p>
            <div class="plan-payment">
                <strong>Payment:</strong> ${plan.monthlyPayment}
            </div>
            <ul class="plan-features">
                ${plan.features.map(feature => `<li>✓ ${feature}</li>`).join('')}
            </ul>
            <div class="plan-actions">
                <button type="button" class="fox-fuel-btn-primary plan-select" data-plan-id="${plan.id}">
                    Select This Plan
                </button>
                <button type="button" class="fox-fuel-btn-secondary plan-details" data-plan-id="${plan.id}">
                    Learn More
                </button>
            </div>
        `;

        // Bind plan selection events
        const selectBtn = card.querySelector('.plan-select');
        const detailsBtn = card.querySelector('.plan-details');
        
        selectBtn.addEventListener('click', () => {
            this.selectPlan(plan.id);
        });
        
        detailsBtn.addEventListener('click', () => {
            this.showPlanDetails(plan.id);
        });

        return card;
    }

    selectPlan(planId) {
        this.updateFormData('selected_plan', planId);
        
        // Highlight selected plan
        const planCards = this.container.querySelectorAll('.fox-fuel-plan-card');
        planCards.forEach(card => {
            card.classList.remove('selected');
            const btn = card.querySelector('.plan-select');
            if (btn && btn.dataset.planId === planId) {
                card.classList.add('selected');
                btn.textContent = '✓ Selected';
            } else if (btn) {
                btn.textContent = 'Select This Plan';
            }
        });

        // NOW show contact section (AFTER value is delivered)
        const contactSection = this.container.querySelector('.fox-fuel-contact-section');
        if (contactSection) {
            contactSection.style.display = 'block';
            contactSection.scrollIntoView({ behavior: 'smooth' });
        }
    }

    showThankYouMessage() {
        const messageHtml = `
            <div class="fox-fuel-thank-you-simple">
                <h3>👍 Perfect! You have everything you need to make an informed decision.</h3>
                <p><strong>When you're ready to enroll:</strong></p>
                <ul style="text-align: left; display: inline-block;">
                    <li>Call us at <strong>(215) 659-1616</strong></li>
                    <li>Visit our website at foxfuel.com</li>
                    <li>Or save this page and contact us later</li>
                </ul>
                <p><em>Thanks for considering Fox Fuel - no pressure, just great service when you need it!</em></p>
            </div>
        `;
        
        const tempDiv = document.createElement('div');
        tempDiv.innerHTML = messageHtml;
        
        const contactSection = this.container.querySelector('.fox-fuel-contact-section');
        contactSection.parentNode.insertBefore(tempDiv.firstElementChild, contactSection);
    }

    submitContactForm() {
        const formData = this.collectContactFormData();
        
        if (!this.validateContactForm(formData)) {
            return;
        }

        // Show loading state
        const submitBtn = this.container.querySelector('#submit-contact-form');
        const originalText = submitBtn.textContent;
        submitBtn.textContent = 'Sending...';
        submitBtn.disabled = true;

        // Submit to WordPress AJAX
        const ajaxData = {
            action: 'fox_fuel_submit_form',
            nonce: foxFuelAjax.nonce,
            form_data: { ...this.formData, ...formData }
        };

        fetch(foxFuelAjax.ajaxurl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: new URLSearchParams(ajaxData)
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                this.goToStep('success');
            } else {
                this.showError('Sorry, there was an error submitting your information. Please try again or call us directly.');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            this.showError('Sorry, there was an error submitting your information. Please try again or call us directly.');
        })
        .finally(() => {
            submitBtn.textContent = originalText;
            submitBtn.disabled = false;
        });
    }

    collectContactFormData() {
        const formElements = this.container.querySelectorAll('.fox-fuel-contact-form input, .fox-fuel-contact-form select, .fox-fuel-contact-form textarea');
        const contactData = {};
        
        formElements.forEach(element => {
            if (element.name) {
                contactData[element.name] = element.value;
            }
        });
        
        return contactData;
    }

    validateContactForm(formData) {
        if (!formData.customer_name || !formData.customer_name.trim()) {
            this.showError('Please enter your name.');
            return false;
        }
        
        if (!formData.customer_phone && !formData.customer_email) {
            this.showError('Please provide either a phone number or email address.');
            return false;
        }
        
        if (formData.customer_email && !this.isValidEmail(formData.customer_email)) {
            this.showError('Please enter a valid email address.');
            return false;
        }
        
        return true;
    }

    bindFormInputs() {
        const inputs = this.container.querySelectorAll('input, select, textarea');
        inputs.forEach(input => {
            input.addEventListener('change', (e) => {
                this.updateFormData(e.target.name, e.target.value);
            });
        });

        // Checkbox handling for priorities
        const checkboxes = this.container.querySelectorAll('input[name="priorities[]"]');
        checkboxes.forEach(checkbox => {
            checkbox.addEventListener('change', () => {
                const priorities = Array.from(checkboxes)
                    .filter(cb => cb.checked)
                    .map(cb => cb.value);
                this.updateFormData('priorities', priorities);
            });
        });
    }

    updateFormData(key, value) {
        this.formData[key] = value;
    }

    updateProgressBar() {
        const progressBar = this.container.querySelector('.fox-fuel-progress-bar');
        const progressFill = this.container.querySelector('.progress-fill');
        const stepIndicators = this.container.querySelectorAll('.step-indicator');
        
        // Update progress fill
        const progressPercent = (this.currentStep - 1) / 3 * 100; // 4 steps total
        if (progressFill) {
            progressFill.style.width = `${progressPercent}%`;
        }
        
        // Update ARIA attributes for progress bar
        if (progressBar) {
            progressBar.setAttribute('aria-valuenow', this.currentStep);
            const stepNames = ['Service Area', 'Usage', 'Preferences', 'Recommendations'];
            const currentStepName = stepNames[this.currentStep - 1] || 'Complete';
            progressBar.setAttribute('aria-label', `Step ${this.currentStep} of 4: ${currentStepName}`);
        }
        
        // Update step indicators
        stepIndicators.forEach((indicator, index) => {
            const stepNumber = index + 1;
            indicator.classList.toggle('active', stepNumber <= this.currentStep);
            indicator.classList.toggle('completed', stepNumber < this.currentStep);
            
            // Update ARIA attributes
            if (stepNumber === this.currentStep) {
                indicator.setAttribute('aria-current', 'step');
            } else {
                indicator.removeAttribute('aria-current');
            }
        });
    }

    showError(message) {
        // Create or update error message display
        let errorDiv = this.container.querySelector('.fox-fuel-error');
        if (!errorDiv) {
            errorDiv = document.createElement('div');
            errorDiv.className = 'fox-fuel-error';
            errorDiv.setAttribute('role', 'alert');
            errorDiv.setAttribute('aria-live', 'assertive');
            
            const currentStep = this.container.querySelector(`[data-step="${this.currentStep}"]`);
            if (currentStep) {
                currentStep.insertBefore(errorDiv, currentStep.firstChild);
            }
        }
        
        errorDiv.innerHTML = `<p>⚠️ ${message}</p>`;
        errorDiv.style.display = 'block';
        
        // Focus the error for screen readers
        errorDiv.focus();
        
        // Auto-hide after 5 seconds
        setTimeout(() => {
            errorDiv.style.display = 'none';
        }, 5000);
    }

    showPlanDetails(planId) {
        const plan = this.plansData.find(p => p.id === planId);
        if (!plan) return;

        this.showModal({
            title: plan.name,
            content: this.generatePlanDetailsContent(plan)
        });
    }

    generatePlanDetailsContent(plan) {
        return `
            <div class="plan-details-content">
                <div class="plan-pricing">
                    <h4>Pricing Details</h4>
                    <p><strong>Price:</strong> ${plan.price}</p>
                    <p><strong>Payment Schedule:</strong> ${plan.monthlyPayment}</p>
                    ${plan.enrollmentFee > 0 ? `<p><strong>Enrollment Fee:</strong> $${plan.enrollmentFee}</p>` : ''}
                    ${plan.monthlyEnrollFee > 0 ? `<p><strong>Monthly Fee:</strong> $${plan.monthlyEnrollFee}</p>` : ''}
                </div>
                <div class="plan-features-detailed">
                    <h4>Features & Benefits</h4>
                    <ul>
                        ${plan.features.map(feature => `<li>${feature}</li>`).join('')}
                    </ul>
                </div>
            </div>
        `;
    }

    showModal(options) {
        const modal = document.createElement('div');
        modal.className = 'fox-fuel-modal';
        modal.innerHTML = `
            <div class="fox-fuel-modal-content">
                <div class="fox-fuel-modal-header">
                    <h3>${options.title}</h3>
                    <button type="button" class="fox-fuel-modal-close">&times;</button>
                </div>
                <div class="fox-fuel-modal-body">
                    ${options.content}
                </div>
            </div>
        `;
        
        document.body.appendChild(modal);
        
        // Close modal events
        const closeBtn = modal.querySelector('.fox-fuel-modal-close');
        closeBtn.addEventListener('click', () => {
            document.body.removeChild(modal);
        });
        
        modal.addEventListener('click', (e) => {
            if (e.target === modal) {
                document.body.removeChild(modal);
            }
        });
    }

    isValidEmail(email) {
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return emailRegex.test(email);
    }
}

// Make available globally
window.FoxFuelSelector = FoxFuelSelector;