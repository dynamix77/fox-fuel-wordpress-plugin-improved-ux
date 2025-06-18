# Fox Fuel Google Sheets Integration Guide

## 🔄 **DYNAMIC PRICING FROM GOOGLE SHEETS**

The Fox Fuel WordPress plugin now includes powerful Google Sheets integration that automatically syncs pricing data from your spreadsheet to keep plan prices current without manual plugin updates.

---

## 🎯 **KEY FEATURES**

### **Automatic Pricing Updates**
- ✅ **Real-time sync** from Google Sheets
- ✅ **Configurable schedule** (hourly, daily, weekly, or manual)
- ✅ **Fallback protection** with cached pricing
- ✅ **Error handling** with admin notifications

### **Flexible Connection Methods**
- ✅ **Google Sheets API** (recommended for reliability)
- ✅ **Published CSV URL** (simple setup, no API key needed)
- ✅ **Automatic fallback** between methods

### **Smart Column Mapping**
- ✅ **Configurable mapping** of spreadsheet columns to pricing fields
- ✅ **Support for letters** (A, B, C) or numbers (1, 2, 3)
- ✅ **Flexible sheet structure** - adapt to your existing layout

---

## 📊 **YOUR SPREADSHEET SETUP**

Based on your Google Sheet structure, here's the optimal configuration:

### **Column Mapping for Your Sheet**
```
Column A: Plan Name (SmartPay Cap, Fixed Price Budget, etc.)
Column E: Description (What Happens in May)
Column F: Min. Gallons (400, None, etc.)
Column I: Price (3.0590, 3.0990, etc.)
Column K: Enrollment Fee (239.88, 0, etc.)
Column L: Monthly Enroll Fee (19.99, 0, etc.)
```

### **Expected Data Format**
```
Row 1: Headers (Plan, Price Lock Period, Payment Months, etc.)
Row 2: SmartPay Cap, Aug 1 - Apr 30, Aug - July, 12, Final adjustment/true-up, 400, Yes, 3.0590, 239.88, 19.99
Row 3: Fixed Price Budget, Aug 1 - Apr 30, Aug - May, 10, Final adjustment/true-up, 400, Yes, 3.0990, 0, 0
Row 4: FuelSaver Budget, Aug 1 - Apr 30*, Aug - May, 10, Final true-up for floating price, None, Yes, 3.5990, 0, 0
Row 5: Safeguard Prepay, Aug 1 - Apr 30, Up-front, 1, May is final delivery/settlement, 400, Yes, 2.9990, 0, 0
```

---

## ⚙️ **SETUP INSTRUCTIONS**

### **Step 1: Prepare Your Google Sheet**
1. **Open your Google Sheet**: https://docs.google.com/spreadsheets/d/1pmyFTXrKzmZI-5ANTV0nvu1HnQKoDaodERjsXkUj5QY/edit
2. **Share the sheet**:
   - Click **Share** button
   - Set to **"Anyone with the link can view"**
   - Copy the sharing URL

### **Step 2: Configure WordPress Plugin**
1. **Go to WordPress Admin** → Settings → Fox Fuel Selector → Google Sheets tab
2. **Enter your Google Sheets URL**:
   ```
   https://docs.google.com/spreadsheets/d/1pmyFTXrKzmZI-5ANTV0nvu1HnQKoDaodERjsXkUj5QY/edit?usp=sharing
   ```
3. **Set column mapping** (pre-configured for your sheet):
   - Plan Name: `A`
   - Price: `I`
   - Enrollment Fee: `K`
   - Monthly Fee: `L`
   - Min Gallons: `F`
   - Description: `E`

### **Step 3: Test & Configure Sync**
1. **Click "Test Connection"** to verify setup
2. **Set sync frequency** (recommend: Daily)
3. **Enable fallback** (recommended)
4. **Save settings**

### **Step 4: Initial Sync**
1. **Click "Sync Now"** to perform first sync
2. **Verify pricing** updated in the Plans tab
3. **Check frontend** to confirm new prices display

---

## 🔒 **SECURITY CONSIDERATIONS**

### **Data Access**
- **Read-only access** - plugin only reads pricing data
- **No personal data** stored or transmitted
- **WordPress permissions** - only admin users can configure
- **Nonce protection** for all AJAX requests

### **API Key (Optional)**
- **Not required** - CSV method works without API key
- **Enhanced reliability** if provided
- **Free tier sufficient** for most use cases
- **Stored securely** in WordPress options

### **Fallback Protection**
- **Cached pricing** used if sync fails
- **Default pricing** as final fallback
- **Site continues** to function during outages
- **Admin notifications** for sync issues

---

## 📈 **MONITORING & MAINTENANCE**

### **Sync Status Dashboard**
The WordPress admin shows real-time sync status:
- ✅ **Last successful sync** time
- ⚠️ **Recent errors** with timestamps
- 📅 **Next scheduled sync** countdown
- 📊 **Sync frequency** settings

### **Error Notifications**
- **Admin notices** for sync failures
- **Email alerts** (optional, via WordPress)
- **Detailed error logs** with timestamps
- **Automatic retry** on next scheduled sync

### **Testing Tools**
- **Test Connection** - Verify sheet access
- **Manual Sync** - Force immediate update
- **View Sync Log** - Review recent activity
- **Data Preview** - See raw sheet data

---

## 🚀 **PERFORMANCE OPTIMIZATIONS**

### **Caching Strategy**
- **24-hour cache** of pricing data
- **Fallback to cache** during sync failures
- **Minimal server load** with scheduled syncs
- **Fast page loads** with cached data

### **Sync Frequency Recommendations**
```
High-volume sites: Hourly or Twice Daily
Medium traffic: Daily (recommended)
Low traffic or stable pricing: Weekly
Testing/development: Manual only
```

### **Network Optimization**
- **30-second timeout** for API calls
- **Graceful fallback** on network issues
- **Compressed data transfer** when possible
- **Error retry logic** built-in

---

## 🔧 **TROUBLESHOOTING GUIDE**

### **Common Issues**

#### **"Connection test failed"**
- ✅ Check sheet sharing permissions ("Anyone with link can view")
- ✅ Verify URL is correct and accessible
- ✅ Test URL in browser incognito mode
- ✅ Check for typos in column mapping

#### **"No data found"**
- ✅ Ensure sheet has data in mapped columns
- ✅ Check for empty rows at top of sheet
- ✅ Verify column letters match your data
- ✅ Test with simplified sheet structure

#### **"Sync failed"**
- ✅ Check internet connectivity
- ✅ Verify Google Sheets service status
- ✅ Review sync log for specific errors
- ✅ Try manual sync to isolate issue

#### **"Prices not updating"**
- ✅ Check last sync time in admin
- ✅ Verify column mapping configuration
- ✅ Clear any caching plugins
- ✅ Test with "Sync Now" button

### **Debug Steps**
1. **Enable WordPress debug mode**:
   ```php
   define('WP_DEBUG', true);
   define('WP_DEBUG_LOG', true);
   ```
2. **Check debug log** in `/wp-content/debug.log`
3. **Test with minimal data** (1-2 rows)
4. **Use browser dev tools** to check AJAX calls
5. **Contact support** with error details

---

## 📝 **API INTEGRATION DETAILS**

### **Google Sheets API Method**
```
Endpoint: https://sheets.googleapis.com/v4/spreadsheets/{SHEET_ID}/values/Sheet1
Method: GET
Authentication: API Key
Response: JSON with cell values
```

### **CSV Export Method**
```
Endpoint: https://docs.google.com/spreadsheets/d/{SHEET_ID}/export?format=csv&gid=0
Method: GET
Authentication: None (public sheet)
Response: CSV data
```

### **Data Processing Flow**
1. **Fetch data** from Google Sheets
2. **Parse structure** (API JSON or CSV)
3. **Map columns** to pricing fields
4. **Validate data** format and completeness
5. **Update plugin** configuration
6. **Cache results** for fallback
7. **Log activity** for monitoring

---

## 💡 **BEST PRACTICES**

### **Sheet Management**
- **Keep structure consistent** - don't reorganize columns frequently
- **Use descriptive headers** in row 1
- **Avoid empty rows** in data section
- **Format prices consistently** (numbers, not text)
- **Test changes** on copy before updating live sheet

### **Sync Configuration**
- **Start with daily sync** for most sites
- **Use manual sync** during testing
- **Enable fallback protection** always
- **Monitor sync status** regularly
- **Keep API key secure** (if using)

### **Error Prevention**
- **Backup current pricing** before major changes
- **Test on staging site** first
- **Document column mappings** for team
- **Set up monitoring alerts** for failures
- **Review sync logs** weekly

---

## 🔄 **UPDATE WORKFLOW**

### **Regular Price Updates**
1. **Update Google Sheet** with new pricing
2. **Wait for next sync** (or trigger manually)
3. **Verify updates** in WordPress admin
4. **Check frontend** pricing display
5. **Monitor for issues** over next 24 hours

### **Bulk Changes**
1. **Backup current config** in WordPress
2. **Update multiple rows** in sheet
3. **Test connection** before sync
4. **Manual sync** to apply changes
5. **Verify all plans** updated correctly

### **Emergency Updates**
1. **Update sheet immediately**
2. **Manual sync** via WordPress admin
3. **Clear site cache** (if using cache plugins)
4. **Verify customer-facing** prices
5. **Monitor sync status** for stability

---

## 📊 **ANALYTICS & REPORTING**

### **Available Metrics**
- **Sync success rate** (successful vs failed)
- **Response time** for Google Sheets API
- **Data freshness** (time since last update)
- **Error frequency** and types
- **Cache hit rate** during outages

### **Monitoring Dashboard**
The WordPress admin provides:
- 📈 **Sync status** overview
- 🕒 **Last sync** timestamp
- ⚠️ **Error alerts** and details
- 📅 **Next sync** countdown
- 📋 **Activity log** history

---

## 🎯 **SUCCESS METRICS**

### **Expected Benefits**
- **100% pricing accuracy** with sheet as source of truth
- **Instant updates** without developer involvement
- **Reduced maintenance** effort by 80%+
- **Improved reliability** with fallback protection
- **Better team workflow** with central pricing management

### **Performance Impact**
- **Minimal page load** impact (cached data)
- **Background sync** doesn't affect visitors
- **Graceful degradation** during issues
- **No database bloat** (efficient caching)

---

## 📞 **SUPPORT & RESOURCES**

### **Getting Help**
- **Plugin Settings**: WordPress Admin → Settings → Fox Fuel Selector → Google Sheets
- **Test Tools**: Use built-in connection test and sync log
- **Documentation**: This guide and inline help text
- **Support**: Contact Fox Fuel support with specific error messages

### **Useful Resources**
- [Google Sheets API Documentation](https://developers.google.com/sheets/api)
- [CSV Export Format Guide](https://support.google.com/docs/answer/37579)
- [WordPress Cron Jobs](https://developer.wordpress.org/plugins/cron/)
- [AJAX in WordPress](https://codex.wordpress.org/AJAX_in_Plugins)

---

**🚀 Your pricing is now dynamically managed through Google Sheets with enterprise-level reliability, security, and monitoring!**