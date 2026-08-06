#!/bin/bash
# Upload sidebar.js and navbar.js to production

echo "Creating sidebar.js..."
cat > resources/js/sidebar.js << 'SIDEBAR_EOF'
/**
 * Sidebar Management - Vanilla JS (SPMB Technology)
 * 
 * Features:
 * - No Alpine.js dependency
 * - Bootstrap tooltips
 * - Hover expand (CSS-based)
 * - No flash on page load
 * - localStorage persistence
 */

(function() {
    'use strict';
    
    // ============================================
    // PREVENT FLASH ON LOAD
    // ============================================
    // Read saved state immediately (before DOM ready)
    const sidebarOpen = localStorage.getItem('sidebarOpen') !== 'false';
    const sidebar = document.getElementById('adminSidebar');
    
    // Apply width before page render to prevent flash
    if (sidebar) {
        sidebar.style.width = sidebarOpen ? '16rem' : '5rem';
        if (!sidebarOpen) {
            sidebar.classList.add('collapsed');
            document.body.classList.add('sidebar-collapsed');
        }
    }
    
    // ============================================
    // INITIALIZE AFTER DOM READY
    // ============================================
    document.addEventListener('DOMContentLoaded', function() {
        initializeSidebar();
        initializeTooltips();
        initializeMobileMenu();
        initializeDarkMode();
    });
    
    // ============================================
    // SIDEBAR TOGGLE FUNCTIONALITY
    // ============================================
    function initializeSidebar() {
        const sidebar = document.getElementById('adminSidebar');
        const toggleBtn = document.getElementById('sidebarToggle');
        
        if (!sidebar || !toggleBtn) {
            console.warn('Sidebar or toggle button not found');
            return;
        }
        
        // Apply initial state to body class
        const initialState = sidebar.classList.contains('collapsed');
        if (initialState) {
            document.body.classList.add('sidebar-collapsed');
        }
        
        // Toggle button click handler
        toggleBtn.addEventListener('click', function() {
            const isOpen = !sidebar.classList.contains('collapsed');
            
            if (isOpen) {
                // Collapse sidebar
                sidebar.classList.add('collapsed');
                sidebar.style.width = '5rem';
                document.body.classList.add('sidebar-collapsed');
                localStorage.setItem('sidebarOpen', 'false');
            } else {
                // Expand sidebar
                sidebar.classList.remove('collapsed');
                sidebar.style.width = '16rem';
                document.body.classList.remove('sidebar-collapsed');
                localStorage.setItem('sidebarOpen', 'true');
            }
            
            // Dispatch event for other components
            window.dispatchEvent(new CustomEvent('sidebar-toggled', { 
                detail: { isOpen: !isOpen } 
            }));
            
            // Reinitialize tooltips after animation completes
            setTimeout(initializeTooltips, 350);
        });
    }

    // ============================================
    // BOOTSTRAP TOOLTIP INITIALIZATION
    // ============================================
    function initializeTooltips() {
        const sidebar = document.getElementById('adminSidebar');
        if (!sidebar) return;
        
        // Check if Bootstrap is available
        if (typeof bootstrap === 'undefined') {
            console.warn('Bootstrap is not loaded. Tooltips will not work.');
            return;
        }
        
        const isCollapsed = sidebar.classList.contains('collapsed');
        
        // Destroy existing tooltips first
        const existingTooltips = document.querySelectorAll('[data-bs-toggle="tooltip"]');
        existingTooltips.forEach(el => {
            const tooltip = bootstrap.Tooltip.getInstance(el);
            if (tooltip) {
                tooltip.dispose();
            }
        });
        
        // Initialize tooltips ONLY when sidebar is collapsed
        if (isCollapsed) {
            const tooltipTriggerList = [].slice.call(
                document.querySelectorAll('[data-bs-toggle="tooltip"]')
            );
            
            tooltipTriggerList.forEach(function (tooltipTriggerEl) {
                new bootstrap.Tooltip(tooltipTriggerEl, {
                    placement: 'right',
                    trigger: 'hover focus',
                    delay: { show: 300, hide: 100 }
                });
            });
        }
    }

    
    // ============================================
    // MOBILE MENU FUNCTIONALITY (SPMB Style)
    // ============================================
    function initializeMobileMenu() {
        const overlay = document.getElementById('sidebarOverlay');
        const sidebar = document.getElementById('adminSidebar');
        const menuLinks = sidebar?.querySelectorAll('.sidebar-menu-item');
        
        if (!sidebar) return;
        
        // Overlay click to close
        if (overlay) {
            overlay.addEventListener('click', closeMobileMenu);
        }
        
        // Close menu when clicking nav links (mobile)
        if (menuLinks) {
            menuLinks.forEach(link => {
                link.addEventListener('click', function() {
                    if (window.innerWidth < 1024) {
                        closeMobileMenu();
                    }
                });
            });
        }
        
        // Close mobile menu on window resize to desktop
        window.addEventListener('resize', function() {
            if (window.innerWidth >= 1024) {
                closeMobileMenu();
            }
        });
    }
    
    function openMobileMenu() {
        const sidebar = document.getElementById('adminSidebar');
        const overlay = document.getElementById('sidebarOverlay');
        
        if (sidebar) sidebar.classList.add('mobile-show');
        if (overlay) overlay.classList.add('show');
        
        // Hide hamburger button when menu is open
        document.body.classList.add('sidebar-menu-open');
    }
    
    function closeMobileMenu() {
        const sidebar = document.getElementById('adminSidebar');
        const overlay = document.getElementById('sidebarOverlay');
        
        if (sidebar) sidebar.classList.remove('mobile-show');
        if (overlay) overlay.classList.remove('show');
        
        // Show hamburger button when menu is closed
        document.body.classList.remove('sidebar-menu-open');
    }
    
    // Expose to window for navbar button access
    window.toggleMobileMenu = function() {
        const sidebar = document.getElementById('adminSidebar');
        const isOpen = sidebar?.classList.contains('mobile-show');
        
        if (isOpen) {
            closeMobileMenu();
        } else {
            openMobileMenu();
        }
    };

    
    // ============================================
    // DARK MODE FUNCTIONALITY
    // ============================================
    function initializeDarkMode() {
        const darkModeBtn = document.getElementById('darkModeToggle');
        if (!darkModeBtn) return;
        
        // Read saved dark mode state
        const isDark = localStorage.getItem('darkMode') === 'true';
        
        // Apply dark mode class
        if (isDark) {
            document.documentElement.classList.add('dark');
        }
        
        // Toggle button click
        darkModeBtn.addEventListener('click', function() {
            const isCurrentlyDark = document.documentElement.classList.contains('dark');
            
            if (isCurrentlyDark) {
                document.documentElement.classList.remove('dark');
                localStorage.setItem('darkMode', 'false');
            } else {
                document.documentElement.classList.add('dark');
                localStorage.setItem('darkMode', 'true');
            }
        });
    }
    
    // ============================================
    // BADGE COUNT LOADING
    // ============================================
    function loadBadgeCounts() {
        fetch('/api/attendance/today-stats')
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    const badge = document.querySelector('.sidebar-badge');
                    if (badge && data.absent > 0) {
                        badge.textContent = data.absent;
                        badge.style.display = 'flex';
                    }
                }
            })
            .catch(err => {
                console.log('Badge count fetch error:', err);
            });
    }
    
    // Load badge counts on init
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', loadBadgeCounts);
    } else {
        loadBadgeCounts();
    }
    
})();
SIDEBAR_EOF

echo "Creating navbar.js..."
cat > resources/js/navbar.js << 'NAVBAR_EOF'
/**
 * ============================================
 * DYNAMIC NAVBAR - SCROLL BEHAVIOR
 * ============================================
 */

document.addEventListener('DOMContentLoaded', function() {
    const navbar = document.getElementById('dynamicNavbar');
    if (!navbar) return;
    
    let lastScrollTop = 0;
    let scrollThreshold = 100; // Start hiding after 100px
    let isNavbarHidden = false;
    
    window.addEventListener('scroll', function() {
        let scrollTop = window.pageYOffset || document.documentElement.scrollTop;
        
        // Scroll down & past threshold → hide navbar
        if (scrollTop > lastScrollTop && scrollTop > scrollThreshold) {
            if (!isNavbarHidden) {
                navbar.classList.add('navbar-hidden');
                isNavbarHidden = true;
            }
        }
        // Scroll up → show navbar
        else if (scrollTop < lastScrollTop) {
            if (isNavbarHidden) {
                navbar.classList.remove('navbar-hidden');
                isNavbarHidden = false;
            }
        }
        
        lastScrollTop = scrollTop <= 0 ? 0 : scrollTop;
    }, { passive: true });
    
    
    /**
     * Keyboard Shortcut: Ctrl+K for Search
     */
    document.addEventListener('keydown', function(e) {
        if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
            e.preventDefault();
            const searchInput = document.querySelector('.search-input');
            if (searchInput) {
                searchInput.focus();
            }
        }
    });
    
    
    /**
     * Dark Mode Icon Sync (Navbar)
     */
    function updateNavbarDarkModeIcon() {
        const isDark = document.documentElement.classList.contains('dark');
        const moonIcon = document.getElementById('navbar-icon-moon');
        const sunIcon = document.getElementById('navbar-icon-sun');
        
        if (moonIcon && sunIcon) {
            if (isDark) {
                moonIcon.classList.add('hidden');
                sunIcon.classList.remove('hidden');
            } else {
                moonIcon.classList.remove('hidden');
                sunIcon.classList.add('hidden');
            }
        }
    }
    
    // Initialize icon on load
    updateNavbarDarkModeIcon();
    
    // Update icon on dark mode toggle
    const darkModeToggle = document.getElementById('navbarDarkModeToggle');
    if (darkModeToggle) {
        darkModeToggle.addEventListener('click', function() {
            setTimeout(updateNavbarDarkModeIcon, 50);
        });
    }
});
NAVBAR_EOF

echo "Files created successfully!"
echo "Now running build..."
npm run build
php artisan view:clear

echo "Done! Check if errors are gone."
