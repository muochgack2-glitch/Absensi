/**
 * Modern Modal System - Inspired by eRapor8
 * 
 * Usage:
 * Modal.show('myModal');
 * Modal.hide('myModal');
 * Modal.confirm('Are you sure?', callback);
 */

window.Modal = {
    /**
     * Show modal by ID
     */
    show: function(modalId) {
        const modal = document.getElementById(modalId);
        if (!modal) return;
        
        modal.classList.add('modal-show');
        document.body.style.overflow = 'hidden';
        
        // Focus trap
        this.trapFocus(modal);
        
        // Close on backdrop click
        const backdrop = modal.querySelector('.modal-backdrop');
        if (backdrop) {
            backdrop.onclick = () => this.hide(modalId);
        }
        
        // Close on close button
        const closeButtons = modal.querySelectorAll('[data-modal-close]');
        closeButtons.forEach(btn => {
            btn.onclick = () => this.hide(modalId);
        });
        
        // Close on ESC key
        this.escapeHandler = (e) => {
            if (e.key === 'Escape') this.hide(modalId);
        };
        document.addEventListener('keydown', this.escapeHandler);
    },
    
    /**
     * Hide modal by ID
     */
    hide: function(modalId) {
        const modal = document.getElementById(modalId);
        if (!modal) return;
        
        modal.classList.remove('modal-show');
        document.body.style.overflow = '';
        
        // Remove ESC handler
        if (this.escapeHandler) {
            document.removeEventListener('keydown', this.escapeHandler);
        }
    },
    
    /**
     * Confirmation modal
     */
    confirm: function(message, onConfirm, options = {}) {
        console.log('Modal.confirm called', { message, options });
        
        const {
            title = 'Konfirmasi',
            confirmText = 'Ya',
            cancelText = 'Batal',
            type = 'warning'
        } = options;
        
        const modalId = 'confirmModal_' + Date.now();
        
        const icons = {
            warning: 'fas fa-exclamation-triangle',
            danger: 'fas fa-exclamation-circle',
            info: 'fas fa-info-circle',
            success: 'fas fa-check-circle'
        };
        
        const colors = {
            warning: '#f59e0b',
            danger: '#ef4444',
            info: '#3b82f6',
            success: '#10b981'
        };
        
        const buttonColors = {
            warning: 'background: linear-gradient(135deg, #f59e0b, #d97706); border: none; color: white;',
            danger: 'background: linear-gradient(135deg, #ef4444, #dc2626); border: none; color: white;',
            info: 'background: linear-gradient(135deg, #3b82f6, #2563eb); border: none; color: white;',
            success: 'background: linear-gradient(135deg, #10b981, #059669); border: none; color: white;'
        };
        
        const modalHTML = `
            <div id="${modalId}" class="modal-modern">
                <div class="modal-backdrop"></div>
                <div class="modal-dialog modal-dialog-centered" style="max-width: 400px;">
                    <div class="modal-content">
                        <div class="modal-body text-center" style="padding: 2rem;">
                            <div class="modal-icon mb-4" style="width: 80px; height: 80px; margin: 0 auto; border-radius: 50%; background: ${colors[type]}20; display: flex; align-items: center; justify-content: center;">
                                <i class="${icons[type]}" style="font-size: 40px; color: ${colors[type]};"></i>
                            </div>
                            <h5 class="modal-title mb-3" style="font-size: 1.25rem; font-weight: 700; color: #1e293b;">${title}</h5>
                            <p class="modal-message mb-4" style="font-size: 0.875rem; color: #64748b; line-height: 1.6;">${message}</p>
                            <div class="d-flex gap-3 justify-content-center">
                                <button type="button" class="btn btn-secondary" data-modal-close style="min-width: 100px; padding: 0.5rem 1rem; font-weight: 600; border-radius: 0.5rem;">
                                    ${cancelText}
                                </button>
                                <button type="button" class="btn-modal-confirm" id="${modalId}_confirm" style="min-width: 100px; padding: 0.5rem 1rem; font-weight: 600; border-radius: 0.5rem; ${buttonColors[type]}">
                                    ${confirmText}
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        `;
        
        document.body.insertAdjacentHTML('beforeend', modalHTML);
        
        // Wait for DOM to be ready
        setTimeout(() => {
            const modal = document.getElementById(modalId);
            const confirmBtn = document.getElementById(modalId + '_confirm');
            const cancelBtn = modal.querySelector('[data-modal-close]');
            
            console.log('=== Modal Debug ===');
            console.log('Modal ID:', modalId);
            console.log('Modal element:', modal);
            console.log('Confirm button ID:', modalId + '_confirm');
            console.log('Confirm button element:', confirmBtn);
            console.log('Confirm button exists:', !!confirmBtn);
            console.log('Cancel button:', cancelBtn);
            
            if (!confirmBtn) {
                console.error('CONFIRM BUTTON NOT FOUND!');
                return;
            }
            
            // Confirm action
            confirmBtn.addEventListener('click', function(e) {
                console.log('=== Confirm Button Clicked ===');
                console.log('Event:', e);
                console.log('Target:', e.target);
                console.log('CurrentTarget:', e.currentTarget);
                
                // Prevent any default behavior
                e.preventDefault();
                e.stopPropagation();
                
                if (onConfirm && typeof onConfirm === 'function') {
                    console.log('Executing callback...');
                    try {
                        onConfirm();
                        console.log('Callback executed successfully');
                    } catch (error) {
                        console.error('Callback error:', error);
                    }
                }
                
                // Close modal
                Modal.hide(modalId);
                setTimeout(() => {
                    if (modal && modal.parentNode) {
                        modal.remove();
                    }
                }, 300);
            }, { capture: false });
            
            console.log('Event listener added to confirm button');
            
            // Cancel action
            cancelBtn.addEventListener('click', function() {
                console.log('Cancel button clicked');
                Modal.hide(modalId);
                setTimeout(() => {
                    if (modal && modal.parentNode) {
                        modal.remove();
                    }
                }, 300);
            });
            
            // Backdrop click
            const backdrop = modal.querySelector('.modal-backdrop');
            if (backdrop) {
                backdrop.addEventListener('click', function() {
                    console.log('Backdrop clicked');
                    Modal.hide(modalId);
                    setTimeout(() => {
                        if (modal && modal.parentNode) {
                            modal.remove();
                        }
                    }, 300);
                });
            }
            
            Modal.show(modalId);
        }, 10);
    },
    
    /**
     * Alert modal
     */
    alert: function(message, title = 'Informasi', type = 'info') {
        const modalId = 'alertModal_' + Date.now();
        
        const icons = {
            warning: 'fas fa-exclamation-triangle',
            danger: 'fas fa-exclamation-circle',
            info: 'fas fa-info-circle',
            success: 'fas fa-check-circle'
        };
        
        const colors = {
            warning: '#f59e0b',
            danger: '#ef4444',
            info: '#3b82f6',
            success: '#10b981'
        };
        
        const modalHTML = `
            <div id="${modalId}" class="modal-modern">
                <div class="modal-backdrop"></div>
                <div class="modal-dialog modal-dialog-centered" style="max-width: 400px;">
                    <div class="modal-content">
                        <div class="modal-body text-center" style="padding: 2rem;">
                            <div class="modal-icon mb-4" style="width: 80px; height: 80px; margin: 0 auto; border-radius: 50%; background: ${colors[type]}20; display: flex; align-items: center; justify-content: center;">
                                <i class="${icons[type]}" style="font-size: 40px; color: ${colors[type]};"></i>
                            </div>
                            <h5 class="modal-title mb-3" style="font-size: 1.25rem; font-weight: 700; color: #1e293b;">${title}</h5>
                            <p class="modal-message mb-4" style="font-size: 0.875rem; color: #64748b; line-height: 1.6;">${message}</p>
                            <div class="d-flex gap-3 justify-content-center">
                                <button type="button" class="btn btn-primary" data-modal-close style="min-width: 100px; padding: 0.5rem 1rem; font-weight: 600; border-radius: 0.5rem; background: linear-gradient(135deg, #3b82f6, #2563eb); border: none; color: white;">
                                    OK
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        `;
        
        document.body.insertAdjacentHTML('beforeend', modalHTML);
        
        const modal = document.getElementById(modalId);
        const okBtn = modal.querySelector('[data-modal-close]');
        
        console.log('Alert modal created:', modalId);
        
        // OK action
        okBtn.addEventListener('click', function() {
            console.log('OK button clicked');
            Modal.hide(modalId);
            setTimeout(() => {
                if (modal && modal.parentNode) {
                    modal.remove();
                }
            }, 300);
        });
        
        // Backdrop click
        const backdrop = modal.querySelector('.modal-backdrop');
        if (backdrop) {
            backdrop.addEventListener('click', function() {
                Modal.hide(modalId);
                setTimeout(() => {
                    if (modal && modal.parentNode) {
                        modal.remove();
                    }
                }, 300);
            });
        }
        
        Modal.show(modalId);
    },
    
    /**
     * Focus trap for accessibility
     */
    trapFocus: function(modal) {
        const focusableElements = modal.querySelectorAll(
            'button, [href], input, select, textarea, [tabindex]:not([tabindex="-1"])'
        );
        
        if (focusableElements.length === 0) return;
        
        const firstElement = focusableElements[0];
        const lastElement = focusableElements[focusableElements.length - 1];
        
        firstElement.focus();
        
        modal.addEventListener('keydown', function(e) {
            if (e.key !== 'Tab') return;
            
            if (e.shiftKey) {
                if (document.activeElement === firstElement) {
                    lastElement.focus();
                    e.preventDefault();
                }
            } else {
                if (document.activeElement === lastElement) {
                    firstElement.focus();
                    e.preventDefault();
                }
            }
        });
    }
};

// Auto-initialize modals with data-modal-trigger
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('[data-modal-trigger]').forEach(trigger => {
        trigger.addEventListener('click', function() {
            const modalId = this.getAttribute('data-modal-trigger');
            Modal.show(modalId);
        });
    });
});
