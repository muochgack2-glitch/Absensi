/**
 * Real-time Form Validation
 * Validates form fields as user types or leaves the field
 */

window.FormValidator = {
    /**
     * Initialize validation for a form
     */
    init: function(formId) {
        const form = document.getElementById(formId);
        if (!form) return;

        // NISN validation (10 digits)
        const nisnField = form.querySelector('[name="nisn"]');
        if (nisnField) {
            nisnField.addEventListener('blur', function() {
                FormValidator.validateNISN(this);
            });
            nisnField.addEventListener('input', function() {
                // Remove non-numeric characters
                this.value = this.value.replace(/\D/g, '');
            });
        }

        // Email validation
        const emailField = form.querySelector('[name="email"]');
        if (emailField) {
            emailField.addEventListener('blur', function() {
                if (this.value) { // Only validate if not empty
                    FormValidator.validateEmail(this);
                } else {
                    FormValidator.clearValidation(this);
                }
            });
        }

        // Phone number validation
        const phoneFields = form.querySelectorAll('[name="no_telepon"], [name="no_hp_ortu"], [name="no_hp_wali"]');
        phoneFields.forEach(field => {
            field.addEventListener('blur', function() {
                if (this.value) {
                    FormValidator.validatePhone(this);
                } else {
                    FormValidator.clearValidation(this);
                }
            });
            field.addEventListener('input', function() {
                // Remove non-numeric characters
                this.value = this.value.replace(/\D/g, '');
            });
        });

        // NIK validation (16 digits)
        const nikField = form.querySelector('[name="nik"]');
        if (nikField) {
            nikField.addEventListener('blur', function() {
                if (this.value) {
                    FormValidator.validateNIK(this);
                } else {
                    FormValidator.clearValidation(this);
                }
            });
            nikField.addEventListener('input', function() {
                // Remove non-numeric characters
                this.value = this.value.replace(/\D/g, '');
            });
        }

        // Required field validation
        const requiredFields = form.querySelectorAll('[required]');
        requiredFields.forEach(field => {
            field.addEventListener('blur', function() {
                FormValidator.validateRequired(this);
            });
        });
    },

    /**
     * Validate NISN (must be 10 digits)
     */
    validateNISN: function(field) {
        const value = field.value.trim();
        
        if (value.length === 0) {
            this.showError(field, 'NISN wajib diisi');
            return false;
        }
        
        if (value.length !== 10) {
            this.showError(field, 'NISN harus 10 digit');
            return false;
        }
        
        if (!/^\d+$/.test(value)) {
            this.showError(field, 'NISN hanya boleh angka');
            return false;
        }
        
        this.showSuccess(field, 'NISN valid');
        return true;
    },

    /**
     * Validate NIK (must be 16 digits)
     */
    validateNIK: function(field) {
        const value = field.value.trim();
        
        if (value.length !== 16) {
            this.showError(field, 'NIK harus 16 digit');
            return false;
        }
        
        if (!/^\d+$/.test(value)) {
            this.showError(field, 'NIK hanya boleh angka');
            return false;
        }
        
        this.showSuccess(field, 'NIK valid');
        return true;
    },

    /**
     * Validate email format
     */
    validateEmail: function(field) {
        const value = field.value.trim();
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        
        if (!emailRegex.test(value)) {
            this.showError(field, 'Format email tidak valid');
            return false;
        }
        
        this.showSuccess(field, 'Email valid');
        return true;
    },

    /**
     * Validate phone number (must start with 08 and 10-13 digits)
     */
    validatePhone: function(field) {
        const value = field.value.trim();
        
        if (!value.startsWith('08')) {
            this.showError(field, 'Nomor HP harus diawali 08');
            return false;
        }
        
        if (value.length < 10 || value.length > 13) {
            this.showError(field, 'Nomor HP harus 10-13 digit');
            return false;
        }
        
        if (!/^\d+$/.test(value)) {
            this.showError(field, 'Nomor HP hanya boleh angka');
            return false;
        }
        
        this.showSuccess(field, 'Nomor HP valid');
        return true;
    },

    /**
     * Validate required field
     */
    validateRequired: function(field) {
        const value = field.value.trim();
        
        if (value.length === 0) {
            this.showError(field, 'Field ini wajib diisi');
            return false;
        }
        
        this.clearValidation(field);
        return true;
    },

    /**
     * Show error message
     */
    showError: function(field, message) {
        // Remove existing feedback
        this.clearValidation(field);
        
        // Add error class
        field.classList.add('is-invalid');
        field.classList.remove('is-valid');
        
        // Create error message
        const feedback = document.createElement('div');
        feedback.className = 'invalid-feedback d-block';
        feedback.style.fontSize = '0.875rem';
        feedback.style.color = '#dc3545';
        feedback.style.marginTop = '0.25rem';
        feedback.innerHTML = '<i class="fas fa-exclamation-circle me-1"></i>' + message;
        
        // Insert after field
        field.parentNode.appendChild(feedback);
    },

    /**
     * Show success message
     */
    showSuccess: function(field, message) {
        // Remove existing feedback
        this.clearValidation(field);
        
        // Add success class
        field.classList.add('is-valid');
        field.classList.remove('is-invalid');
        
        // Create success message
        const feedback = document.createElement('div');
        feedback.className = 'valid-feedback d-block';
        feedback.style.fontSize = '0.875rem';
        feedback.style.color = '#28a745';
        feedback.style.marginTop = '0.25rem';
        feedback.innerHTML = '<i class="fas fa-check-circle me-1"></i>' + message;
        
        // Insert after field
        field.parentNode.appendChild(feedback);
    },

    /**
     * Clear validation state
     */
    clearValidation: function(field) {
        field.classList.remove('is-invalid', 'is-valid');
        
        // Remove existing feedback
        const parent = field.parentNode;
        const existingFeedback = parent.querySelectorAll('.invalid-feedback, .valid-feedback');
        existingFeedback.forEach(el => el.remove());
    }
};

// Auto-initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    // Initialize for pendaftar forms
    FormValidator.init('formPendaftar');
});
