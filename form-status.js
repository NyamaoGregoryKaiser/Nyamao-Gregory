document.addEventListener('DOMContentLoaded', function() {
    // Function to get URL parameters
    function getUrlParameter(name) {
        name = name.replace(/[\[]/, '\\[').replace(/[\]]/, '\\]');
        var regex = new RegExp('[\\?&]' + name + '=([^&#]*)');
        var results = regex.exec(location.search);
        return results === null ? '' : decodeURIComponent(results[1].replace(/\+/g, ' '));
    }

    // Check if status parameter exists
    var status = getUrlParameter('status');
    
    // Create alert element
    if (status) {
        var alertDiv = document.createElement('div');
        alertDiv.className = 'alert';
        
        if (status === 'success') {
            alertDiv.className += ' alert-success';
            alertDiv.innerHTML = '<i class="bx bx-check-circle"></i> Your task has been sent successfully!';
        } else if (status === 'error') {
            alertDiv.className += ' alert-danger';
            alertDiv.innerHTML = '<i class="bx bx-error-circle"></i> Failed to send the task. Please try again.';
        }
        
        // Style the alert
        alertDiv.style.padding = '15px';
        alertDiv.style.margin = '20px 0';
        alertDiv.style.borderRadius = '5px';
        alertDiv.style.animation = 'fadeIn 0.5s';
        
        // Add animation style
        var style = document.createElement('style');
        style.textContent = `
            @keyframes fadeIn {
                from { opacity: 0; transform: translateY(-10px); }
                to { opacity: 1; transform: translateY(0); }
            }
            .alert-success {
                background-color: #d4edda;
                color: #155724;
                border: 1px solid #c3e6cb;
            }
            .alert-danger {
                background-color: #f8d7da;
                color: #721c24;
                border: 1px solid #f5c6cb;
            }
            .alert i {
                margin-right: 8px;
            }
        `;
        document.head.appendChild(style);
        
        // Insert the alert at the top of the form
        var form = document.querySelector('.php-email-form');
        if (form) {
            form.parentNode.insertBefore(alertDiv, form);
            
            // Auto-dismiss after 5 seconds
            setTimeout(function() {
                alertDiv.style.animation = 'fadeOut 0.5s forwards';
                style.textContent += `
                    @keyframes fadeOut {
                        from { opacity: 1; transform: translateY(0); }
                        to { opacity: 0; transform: translateY(-10px); }
                    }
                `;
                setTimeout(function() {
                    alertDiv.remove();
                }, 500);
            }, 5000);
            
            // Clean URL without reloading page
            var newUrl = window.location.protocol + "//" + 
                         window.location.host + 
                         window.location.pathname + 
                         window.location.hash;
            window.history.replaceState({path: newUrl}, '', newUrl);
        }
    }
});