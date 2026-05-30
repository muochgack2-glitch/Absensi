/**
 * Auto-save Draft System
 * Automatically saves form data to localStorage every 5 seconds
 */

window.AutoSaveDraft = {
    /**
     * Initialize auto-save for a form
     */
    init: function(formId, draftKey) {
        const form = document.getElementById(formId);
        if (!form) return;

        this.formId = formId;
        this.draftKey = draftKey || 'form_draft_' + formId;
        this.saveInterval = null;

        // Check for existing draft on page load
        this.checkExistingDraft();

        // Start auto-save
        this.startAutoSave();

        // Clear draft on successful submit
        form.addEventListener('submit', () => {
            this.clearDraft();
        });

        // Save on page unload (browser close/refresh)
        window.addEventListener('beforeunload', () => {
            this.saveDraft();
        });
    },

    /**
     * Check if there's an existing draft
     */
    checkExistingDraft: function() {
        const draft = localStorage.getItem(this.draftKey);
        
        if (draft) {
            const draftData = JSON.parse(draft);
            const savedTime = new Date(draftData.timestamp);
            const now = new Date();
            const hoursDiff = (now - savedTime) / (1000 * 60 * 60);

            // Only show draft if less than 24 hours old
            if (hoursDiff < 24) {
                Modal.confirm(
                    `Ada draft yang tersimpan pada <strong>${savedTime.toLocaleString('id-ID')}</strong>.<br><br>Apakah ingin melanjutkan mengisi form dengan data tersebut?`,
                    () => {
                        this.loadDraft(draftData.data);
                        Modal.alert('Draft berhasil dimuat', 'Berhasil', 'success');
                    },
                    {
                        title: 'Draft Ditemukan',
                        confirmText: 'Ya, Lanjutkan',
                        cancelText: 'Tidak, Mulai Baru',
                        type: 'info'
                    }
                );
            } else {
                // Draft too old, clear it
                this.clearDraft();
            }
        }
    },

    /**
     * Start auto-save interval
     */
    startAutoSave: function() {
        // Save every 5 seconds
        this.saveInterval = setInterval(() => {
            this.saveDraft();
        }, 5000);

        console.log('Auto-save draft started for form:', this.formId);
    },

    /**
     * Stop auto-save interval
     */
    stopAutoSave: function() {
        if (this.saveInterval) {
            clearInterval(this.saveInterval);
            console.log('Auto-save draft stopped');
        }
    },

    /**
     * Save form data to localStorage
     */
    saveDraft: function() {
        const form = document.getElementById(this.formId);
        if (!form) return;

        const formData = new FormData(form);
        const data = {};

        // Convert FormData to object
        for (let [key, value] of formData.entries()) {
            // Skip CSRF token and method fields
            if (key === '_token' || key === '_method') continue;
            
            // Only save if field has value
            if (value && value.trim() !== '') {
                data[key] = value;
            }
        }

        // Only save if there's actual data
        if (Object.keys(data).length > 0) {
            const draftData = {
                data: data,
                timestamp: new Date().toISOString()
            };

            localStorage.setItem(this.draftKey, JSON.stringify(draftData));
            console.log('Draft saved:', Object.keys(data).length, 'fields');
        }
    },

    /**
     * Load draft data into form
     */
    loadDraft: function(data) {
        const form = document.getElementById(this.formId);
        if (!form) return;

        Object.keys(data).forEach(key => {
            const field = form.querySelector(`[name="${key}"]`);
            if (field) {
                if (field.type === 'checkbox') {
                    field.checked = data[key] === 'on' || data[key] === '1';
                } else if (field.type === 'radio') {
                    const radio = form.querySelector(`[name="${key}"][value="${data[key]}"]`);
                    if (radio) radio.checked = true;
                } else {
                    field.value = data[key];
                }

                // Trigger change event for validation
                field.dispatchEvent(new Event('change', { bubbles: true }));
            }
        });

        console.log('Draft loaded:', Object.keys(data).length, 'fields');
    },

    /**
     * Clear draft from localStorage
     */
    clearDraft: function() {
        localStorage.removeItem(this.draftKey);
        console.log('Draft cleared');
    },

    /**
     * Manually save draft (for testing)
     */
    manualSave: function() {
        this.saveDraft();
        Modal.alert('Draft berhasil disimpan', 'Tersimpan', 'success');
    }
};

// Auto-initialize for pendaftar forms
document.addEventListener('DOMContentLoaded', function() {
    // Auto-save for create form
    if (document.getElementById('formPendaftar') && window.location.pathname.includes('/create')) {
        AutoSaveDraft.init('formPendaftar', 'pendaftar_create_draft');
    }

    // Auto-save for edit form
    if (document.getElementById('formPendaftar') && window.location.pathname.includes('/edit')) {
        const pendaftarId = window.location.pathname.split('/').filter(Boolean).pop();
        AutoSaveDraft.init('formPendaftar', 'pendaftar_edit_draft_' + pendaftarId);
    }
});
