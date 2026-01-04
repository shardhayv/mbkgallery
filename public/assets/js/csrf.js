// CSRF Protection Helper
window.CSRF = {
    token: null,
    
    init: function() {
        // Get token from meta tag or form
        const tokenMeta = document.querySelector('meta[name="csrf-token"]');
        const tokenInput = document.querySelector('input[name="csrf_token"]');
        
        if (tokenMeta) {
            this.token = tokenMeta.getAttribute('content');
        } else if (tokenInput) {
            this.token = tokenInput.value;
        }
    },
    
    getToken: function() {
        if (!this.token) this.init();
        return this.token;
    },
    
    addToFormData: function(formData) {
        formData.append('csrf_token', this.getToken());
        return formData;
    },
    
    addToHeaders: function(headers = {}) {
        headers['X-CSRF-Token'] = this.getToken();
        return headers;
    }
};

// Auto-initialize
document.addEventListener('DOMContentLoaded', function() {
    CSRF.init();
    
    // Add CSRF token to all AJAX requests
    const originalFetch = window.fetch;
    window.fetch = function(url, options = {}) {
        if (options.method && options.method.toLowerCase() === 'post') {
            options.headers = CSRF.addToHeaders(options.headers || {});
        }
        return originalFetch(url, options);
    };
});