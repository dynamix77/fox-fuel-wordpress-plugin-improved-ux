# Fox Fuel WordPress Plugin - Installation Instructions

## 📦 **PACKAGING FOR INSTALLATION**

### **Step 1: Create Plugin ZIP**
1. Navigate to the plugin directory
2. Select all files in `fox-fuel-wordpress-plugin-improved-ux/`
3. Create a ZIP file named: `fox-fuel-selector-v2.1.zip`

### **Step 2: WordPress Installation**
1. **Login** to WordPress Admin
2. Go to **Plugins → Add New**
3. Click **Upload Plugin**
4. **Choose File** and select `fox-fuel-selector-v2.1.zip`
5. Click **Install Now**
6. **Activate** the plugin

### **Step 3: Initial Configuration**
1. Go to **Settings → Fox Fuel Selector**
2. **Contact Info Tab**:
   - Verify phone: `(215) 659-1616`
   - Verify email: `info@foxfuel.com`
   - Update business hours if needed
3. **Content Tab**:
   - Review headings and step titles
   - Customize messaging as needed
4. **Plans Tab**:
   - Review plan offerings
   - Update pricing if needed
5. **Save Settings**

### **Step 4: Add to Website**
1. **Edit any page** where you want the selector
2. **Add shortcode**: `[fox_fuel_selector]`
3. **Preview** and test functionality
4. **Publish** the page

---

## 🧪 **TESTING CHECKLIST**

### **Basic Functionality**
- [ ] Plugin activates without errors
- [ ] Shortcode displays correctly
- [ ] All 4 steps advance properly
- [ ] ZIP code validation works
- [ ] Plan recommendations appear
- [ ] Contact form submission works
- [ ] Email notifications received

### **Accessibility Testing**
- [ ] Tab navigation works through all elements
- [ ] Screen reader announces content properly
- [ ] High contrast mode displays correctly
- [ ] Focus indicators are visible
- [ ] Error messages are announced
- [ ] Skip link functions properly

### **Mobile Testing**
- [ ] Responsive design on phone
- [ ] Touch targets are adequate size
- [ ] Form inputs work on mobile
- [ ] Progress bar displays correctly
- [ ] Contact form works on mobile

### **Browser Compatibility**
- [ ] Chrome (latest)
- [ ] Firefox (latest)
- [ ] Safari (latest)
- [ ] Edge (latest)
- [ ] Internet Explorer 11 (if needed)

---

## ⚙️ **CONFIGURATION OPTIONS**

### **Shortcode Parameters**
```html
[fox_fuel_selector]                    <!-- Default settings -->
[fox_fuel_selector mode="guided"]      <!-- Guided mode (default) -->
[fox_fuel_selector theme="light"]      <!-- Light theme (default) -->
[fox_fuel_selector width="100%"]       <!-- Full width -->
[fox_fuel_selector height="auto"]      <!-- Auto height -->
```

### **Admin Panel Settings**

#### **Contact Information Tab**
```
Phone Number: (215) 659-1616
Email Address: info@foxfuel.com
Business Hours: Monday-Friday, 8 AM - 5 PM
Service Tagline: Serving southeastern Pennsylvania and South Jersey since 1981
```

#### **Content Management Tab**
```
Main Heading: Find Your Perfect Heating Oil Plan
Main Subtitle: Get personalized recommendations in under 2 minutes
Step 1 Title: First, let's check if we serve your area
Step 2 Title: Tell us about your heating oil usage
Step 3 Title: What's most important to you?
Step 4 Title: Your Recommended Plans
```

#### **Plan Management Tab**
- SmartPay Price Cap ($3.89/gallon cap)
- Fixed Price Budget ($3.59/gallon fixed)
- FuelSaver Budget (Market pricing)

---

## 🔧 **CUSTOMIZATION GUIDE**

### **Styling Customization**
Add custom CSS to your theme:
```css
/* Customize Fox Fuel selector colors */
.fox-fuel-selector-container {
    --fox-fuel-red: #YOUR_RED_COLOR;
    --fox-fuel-blue: #YOUR_BLUE_COLOR;
}

/* Customize button styling */
.fox-fuel-btn-primary {
    background: #YOUR_BUTTON_COLOR;
}
```

### **Content Customization**
Use the WordPress admin panel to update:
- Contact information
- Step titles and descriptions  
- Button labels
- Success messages
- Error messages

### **Plan Customization**
Through the admin panel, you can:
- Add new plans
- Modify existing plans
- Update pricing
- Change plan features
- Set plan priorities

---

## 🚨 **TROUBLESHOOTING**

### **Common Issues**

#### **Plugin Won't Activate**
- Check PHP version (7.4+ required)
- Verify WordPress version (5.0+ required)
- Check for plugin conflicts

#### **Shortcode Not Displaying**
- Verify plugin is activated
- Check shortcode spelling: `[fox_fuel_selector]`
- Test on different page/post

#### **Styling Issues**
- Check for theme conflicts
- Verify CSS files are loading
- Test with default WordPress theme

#### **Form Not Submitting**
- Check admin email settings
- Verify AJAX functionality
- Check browser console for errors

#### **Mobile Display Issues**
- Clear cache (browser and WordPress)
- Test on actual mobile device
- Check viewport meta tag in theme

### **Debug Mode**
Enable WordPress debug mode in `wp-config.php`:
```php
define('WP_DEBUG', true);
define('WP_DEBUG_LOG', true);
```

---

## 📧 **SUPPORT**

### **Getting Help**
- **Email**: info@foxfuel.com  
- **Phone**: (215) 659-1616
- **WordPress Admin**: Settings → Fox Fuel Selector → Usage Instructions tab

### **Before Contacting Support**
1. **Test with default theme** (Twenty Twenty-Four)
2. **Deactivate other plugins** temporarily
3. **Check browser console** for JavaScript errors
4. **Verify shortcode placement** and spelling
5. **Clear all caches** (browser, WordPress, CDN)

---

## 🔄 **UPDATES & MAINTENANCE**

### **Plugin Updates**
- Updates will be provided as new ZIP files
- Always **backup your site** before updating
- **Test on staging site** first if possible
- **Backup custom settings** from admin panel

### **Regular Maintenance**
- Review plan pricing quarterly
- Update contact information as needed
- Test functionality monthly
- Monitor form submission rates
- Review accessibility annually

---

## 📊 **ANALYTICS SETUP**

### **Google Analytics Integration**
Add this code to your theme's `functions.php`:
```php
function fox_fuel_analytics() {
    ?>
    <script>
    // Track Fox Fuel form completions
    document.addEventListener('fox-fuel-step-completed', function(e) {
        gtag('event', 'form_step_completed', {
            'step_number': e.detail.step,
            'step_name': e.detail.stepName
        });
    });
    </script>
    <?php
}
add_action('wp_footer', 'fox_fuel_analytics');
```

### **Key Metrics to Track**
- Form completion rate by step
- Plan selection preferences  
- Contact form conversion rate
- Mobile vs desktop usage
- Page load performance

---

**🎯 Your Fox Fuel selector is now ready to deliver an exceptional user experience with improved conversions, accessibility, and customer trust!**