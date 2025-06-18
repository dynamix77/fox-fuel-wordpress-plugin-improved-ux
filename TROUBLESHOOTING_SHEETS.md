# 🚨 GOOGLE SHEETS INTEGRATION - TROUBLESHOOTING

## **IMMEDIATE FIXES FOR YOUR ISSUES**

### **1. JavaScript Buttons Not Working**
**Problem**: "Sync Now" and "View Sync Log" buttons hang without response

**Solution**: 
1. **Clear browser cache** completely
2. **Check browser console** for JavaScript errors (F12 → Console tab)
3. **Verify admin script** is loading: `fox-fuel-admin.js`
4. **Test on different browser** to isolate caching issues

### **2. Headers Question - YES, Keep Them!**
**Your sheet structure is PERFECT**:
```
Row 1 (Headers): Plan | Price Lock Period | Payment Months | # of Payments | What Happens in May | Min. Gallons | Automatic Delivery | Price | Enrollment Fee | Monthly Enroll Fee
Row 2 (Data):    SmartPay Cap | Aug 1 - Apr 30 | Aug - July | 12 | Final adjustment/true-up | 400 | Yes | 3.0590 | 239.88 | 19.99
Row 3 (Data):    Fixed Price Budget | Aug 1 - Apr 30 | Aug - May | 10 | Final adjustment/true-up | 400 | Yes | 3.0990 | 0 | 0
```

**The plugin automatically skips row 1** and processes data starting from row 2.

---

## **DEBUGGING STEPS**

### **Step 1: Test Connection**
1. **Enter your sheet URL** in the Google Sheets tab
2. **Click "Test Connection"** 
3. **Check browser console** for any JavaScript errors
4. **Expected result**: Should show "Connection successful! Found X rows"

### **Step 2: Check Sheet URL**
**Your URL should be**: 
```
https://docs.google.com/spreadsheets/d/1pmyFTXrKzmZI-5ANTV0nvu1HnQKoDaodERjsXkUj5QY/edit?usp=sharing
```

**Plugin converts this to CSV automatically**:
```
https://docs.google.com/spreadsheets/d/1pmyFTXrKzmZI-5ANTV0nvu1HnQKoDaodERjsXkUj5QY/export?format=csv&gid=0
```

### **Step 3: Verify Sheet Permissions**
1. **Open your sheet** in incognito browser
2. **Should be viewable** without login
3. **If not accessible**: Share → Change to "Anyone with link can view"

### **Step 4: Check WordPress Debug**
Add to `wp-config.php`:
```php
define('WP_DEBUG', true);
define('WP_DEBUG_LOG', true);
```

Check `/wp-content/debug.log` for errors.

---

## **QUICK TESTS TO RUN**

### **Test 1: Manual CSV Download**
Visit this URL in your browser:
```
https://docs.google.com/spreadsheets/d/1pmyFTXrKzmZI-5ANTV0nvu1HnQKoDaodERjsXkUj5QY/export?format=csv&gid=0
```

**Expected**: CSV file downloads with your pricing data

### **Test 2: JavaScript Console**
1. **Open WordPress admin** → Settings → Fox Fuel Selector → Google Sheets
2. **Press F12** → Console tab
3. **Click "Test Connection"**
4. **Look for errors** in red text

### **Test 3: AJAX Response**
1. **Open browser Network tab** (F12 → Network)
2. **Click "Test Connection"**
3. **Look for AJAX call** to `admin-ajax.php`
4. **Check response** for error messages

---

## **COMMON ISSUES & SOLUTIONS**

### **Issue: "This just gets hung up here"**
**Causes**:
- JavaScript not loading
- AJAX request blocked
- Incorrect nonce/permissions
- Server timeout

**Solutions**:
1. **Clear all caches** (browser, WordPress, CDN)
2. **Disable other plugins** temporarily
3. **Test with default WordPress theme**
4. **Check server PHP error logs**

### **Issue: No Google Sheets URL configured**
**Solution**: 
1. **Enter full Google Sheets URL** in the "Google Sheets URL" field
2. **Save settings** before testing
3. **Don't use CSV export URL** - plugin converts automatically

### **Issue: Connection test failed**
**Causes**:
- Sheet not publicly accessible
- Invalid URL format
- Google Sheets service down

**Solutions**:
1. **Verify sheet sharing** settings
2. **Test URL in incognito** browser
3. **Try again later** if Google services are down

---

## **EXPECTED BEHAVIOR**

### **Test Connection Success**:
```
✅ Connection successful! Found 5 rows of data.

Data Preview:
[
  ["Plan", "Price Lock Period", "Payment Months", ...],
  ["SmartPay Cap", "Aug 1 - Apr 30", "Aug - July", ...],
  ["Fixed Price Budget", "Aug 1 - Apr 30", "Aug - May", ...]
]
```

### **Manual Sync Success**:
```
✅ Pricing data synced successfully!
Last sync: 2025-06-17 14:30:15
```

### **Sync Log Example**:
```
Recent Sync Log (3 entries):
✅ 2025-06-17 14:30:15: Pricing data synced successfully from Google Sheets
✅ 2025-06-17 14:30:12: Processed plan: SmartPay Cap - $3.06/gallon
✅ 2025-06-17 14:30:12: Processed plan: Fixed Price Budget - $3.10/gallon
```

---

## **NEXT STEPS**

1. **Save your current settings** in WordPress admin
2. **Test the "Test Connection" button** and check browser console
3. **Share the specific error messages** you see
4. **Try on a different browser** to rule out caching issues
5. **Check if JavaScript is enabled** and no ad blockers are interfering

**Once we get the "Test Connection" working, everything else will follow!** 🎯