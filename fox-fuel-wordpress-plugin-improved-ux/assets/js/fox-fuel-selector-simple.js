    errorDiv.style.display = 'block';
    
    // Auto-hide after 5 seconds
    setTimeout(function() {
        errorDiv.style.display = 'none';
    }, 5000);
}

// Make functions available globally for debugging
window.FoxFuelDebug = {
    initializeSelector: initializeSelector,
    showError: showError
};

console.log('Fox Fuel Selector: JavaScript loaded successfully');