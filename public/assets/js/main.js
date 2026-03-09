/**
 * Main JavaScript untuk Netmon App
 * Berisi fungsi-fungsi umum dan konfigurasi
 */

// Global configuration
var NetmonApp = NetmonApp || {};

// CSRF Token handling
NetmonApp.CSRF = {
    tokenName: null,
    tokenHash: null,
    
    init: function() {
        // Get CSRF token from meta tag
        var csrfMeta = document.querySelector('meta[name="csrf-token"]');
        if (csrfMeta) {
            var content = csrfMeta.getAttribute('content').split(':');
            this.tokenName = content[0];
            this.tokenHash = content[1];
            
            // Setup AJAX to include CSRF token automatically
            $.ajaxSetup({
                beforeSend: function(xhr, settings) {
                    if (!/^(GET|HEAD|OPTIONS|TRACE)$/i.test(settings.type) && !this.crossDomain) {
                        xhr.setRequestHeader('X-CSRF-TOKEN', NetmonApp.CSRF.tokenHash);
                    }
                }
            });
        }
    },
    
    refresh: function(callback) {
        // Get fresh CSRF token
        $.ajax({
            url: BASE_URL + 'users/get_csrf',
            type: 'GET',
            dataType: 'json',
            success: function(data) {
                NetmonApp.CSRF.tokenName = data.csrf_token_name;
                NetmonApp.CSRF.tokenHash = data.csrf_token;
                if (callback) callback(data);
            }
        });
    }
};

// Initialize on document ready
$(document).ready(function() {
    NetmonApp.CSRF.init();
});
