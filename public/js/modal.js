/**
 * Modern Modal System - Inspired by eRapor8
 * 
 * Usage:
 * Modal.show('myModal');
 * Modal.hide('myModal');
 * Modal.confirm('Are you sure?', callback);
 */

// Helper function to check if dark mode is active
function isDarkMode() {
    const isDark = document.documentElement.classList.contains('admin-dark');
    console.log('🌓 Modal.js loaded - Dark mode:', isDark);
    return isDark;
}

// Log version on load
console.log('✅ Modal.js version: 4.0 (FIXED: added background to modal-body)');
console.log('📅 Loaded at:', new Date().toLocaleTimeString());

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
        const {
            title = 'Konfirmasi',
            confirmText = 'Ya',
            cancelText = 'Batal',
            type = 'warning'
        } = options;
        
        const modalId = 'confirmModal_' + Date.now();
        const currentIsDark = isDarkMode();
        
        console.log('🔍 Creating confirm modal - Dark mode detected:', currentIsDark);
        console.log('📋 HTML element classes:', document.documentElement.className);
        
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
        
        const themeStyles = currentIsDark ? {
            modalBg: 'background: #1e293b !important; color: #e5e7eb !important; border: 1px solid #334155 !important;',
            titleColor: 'color: #f8fafc !important;',
            messageColor: 'color: #cbd5e1 !important;',
            cancelBtn: 'background: #334155 !important; border: 1px solid #475569 !important; color: #e5e7eb !important; min-width: 100px; padding: 0.5rem 1rem; font-weight: 600; border-radius: 0.5rem;'
        } : {
            modalBg: 'background: #ffffff !important; color: #1e293b !important; border: 1px solid #e2e8f0 !important;',
            titleColor: 'color: #1e293b !important;',
            messageColor: 'color: #64748b !important;',
            cancelBtn: 'background: #f3f4f6 !important; border: 1px solid #d1d5db !important; color: #374151 !important; min-width: 100px; padding: 0.5rem 1rem; font-weight: 600; border-radius: 0.5rem;'
        };
        
        console.log('🎨 Theme styles applied:', themeStyles);
        
        const modalHTML = `
            <div id="${modalId}" class="modal-modern modal-js-generated" data-theme="${currentIsDark ? 'dark' : 'light'}">
                <div class="modal-backdrop"></div>
                <div class="modal-dialog modal-dialog-centered" style="max-width: 400px;">
                    <div class="modal-content modal-js-content" style="${themeStyles.modalBg}">
                        <div class="modal-body text-center modal-confirm-body" style="padding: 2rem; ${themeStyles.modalBg}">
                            <div class="modal-icon-circle" style="width: 80px; height: 80px; margin: 0 auto 1.5rem; border-radius: 50%; display: flex; align-items: center; justify-content: center; background: ${colors[type]}20;">
                                <i class="${icons[type]} modal-icon-main" style="font-size: 40px; color: ${colors[type]};"></i>
                            </div>
                            <h5 class="modal-title modal-confirm-title" style="font-size: 1.25rem; font-weight: 700; margin-bottom: 1rem; ${themeStyles.titleColor}">${title}</h5>
                            <p class="modal-message modal-confirm-message" style="font-size: 0.875rem; line-height: 1.6; margin-bottom: 1.5rem; ${themeStyles.messageColor}">${message}</p>
                            <div class="d-flex gap-3 justify-content-center">
                                <button type="button" class="btn btn-secondary modal-btn-cancel" data-modal-close style="${themeStyles.cancelBtn}">
                                    ${cancelText}
                                </button>
                                <button type="button" class="btn-modal-confirm modal-btn-confirm-${type}" id="${modalId}_confirm" style="${buttonColors[type]}">
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
            
            if (!confirmBtn) {
                console.error('Confirm button not found!');
                return;
            }
            
            // Confirm action
            confirmBtn.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                
                if (onConfirm && typeof onConfirm === 'function') {
                    try {
                        onConfirm();
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
            });
            
            // Cancel action
            cancelBtn.addEventListener('click', function() {
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
            
            // Re-apply styles after modal is shown (fix for CSS override issue)
            setTimeout(() => {
                const modalContent = modal.querySelector('.modal-content');
                const modalTitle = modal.querySelector('.modal-confirm-title');
                const modalMessage = modal.querySelector('.modal-confirm-message');
                const cancelButton = modal.querySelector('.modal-btn-cancel');
                
                if (modalContent) {
                    modalContent.style.cssText = themeStyles.modalBg;
                    console.log('✅ Re-applied modal content styles');
                }
                if (modalTitle) {
                    modalTitle.style.cssText = 'font-size: 1.25rem; font-weight: 700; margin-bottom: 1rem; ' + themeStyles.titleColor;
                    console.log('✅ Re-applied title styles');
                }
                if (modalMessage) {
                    modalMessage.style.cssText = 'font-size: 0.875rem; line-height: 1.6; margin-bottom: 1.5rem; ' + themeStyles.messageColor;
                    console.log('✅ Re-applied message styles');
                }
                if (cancelButton) {
                    cancelButton.style.cssText = themeStyles.cancelBtn;
                    console.log('✅ Re-applied cancel button styles');
                }
            }, 50);
        }, 10);
    },
    
    /**
     * Alert modal
     */
    alert: function(message, title = 'Informasi', type = 'info') {
        const modalId = 'alertModal_' + Date.now();
        const currentIsDark = isDarkMode();
        
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
        
        const themeStyles = currentIsDark ? {
            modalBg: 'background: #1e293b !important; color: #e5e7eb !important; border: 1px solid #334155 !important;',
            titleColor: 'color: #f8fafc !important;',
            messageColor: 'color: #cbd5e1 !important;'
        } : {
            modalBg: 'background: #ffffff !important; color: #1e293b !important; border: 1px solid #e2e8f0 !important;',
            titleColor: 'color: #1e293b !important;',
            messageColor: 'color: #64748b !important;'
        };
        
        const modalHTML = `
            <div id="${modalId}" class="modal-modern modal-js-generated" data-theme="${currentIsDark ? 'dark' : 'light'}">
                <div class="modal-backdrop"></div>
                <div class="modal-dialog modal-dialog-centered" style="max-width: 400px;">
                    <div class="modal-content modal-js-content" style="${themeStyles.modalBg}">
                        <div class="modal-body text-center modal-confirm-body" style="padding: 2rem; ${themeStyles.modalBg}">
                            <div class="modal-icon-circle" style="width: 80px; height: 80px; margin: 0 auto 1.5rem; border-radius: 50%; display: flex; align-items: center; justify-content: center; background: ${colors[type]}20;">
                                <i class="${icons[type]} modal-icon-main" style="font-size: 40px; color: ${colors[type]};"></i>
                            </div>
                            <h5 class="modal-title modal-confirm-title" style="font-size: 1.25rem; font-weight: 700; margin-bottom: 1rem; ${themeStyles.titleColor}">${title}</h5>
                            <p class="modal-message modal-confirm-message" style="font-size: 0.875rem; line-height: 1.6; margin-bottom: 1.5rem; ${themeStyles.messageColor}">${message}</p>
                            <div class="d-flex gap-3 justify-content-center">
                                <button type="button" class="btn btn-primary modal-btn-ok" data-modal-close style="background: linear-gradient(135deg, #3b82f6, #2563eb); border: none; color: white; min-width: 100px; padding: 0.5rem 1rem; font-weight: 600; border-radius: 0.5rem;">
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
        
        // OK action
        okBtn.addEventListener('click', function() {
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
