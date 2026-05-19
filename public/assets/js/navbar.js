// Navigation Bar Component Loader
class NavbarLoader {
    constructor() {
        this.navbarLoaded = false;
        this.init();
    }

    init() {
        // Load navbar when DOM is ready
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', () => this.loadNavbar());
        } else {
            this.loadNavbar();
        }
    }

    async loadNavbar() {
        try {
            const response = await fetch('navbar.html', {
                headers: {
                    'Cache-Control': 'no-cache'
                }
            });
            const navbarHTML = await response.text();
            
            // Find navbar placeholder and replace with loaded content
            const navbarPlaceholder = document.getElementById('navbar');
            if (navbarPlaceholder) {
                navbarPlaceholder.outerHTML = navbarHTML;
                this.navbarLoaded = true;
                this.initializeNavbarFeatures();
            }
        } catch (error) {
            console.error('Error loading navbar:', error);
        }
    }

    initializeNavbarFeatures() {
        // Initialize tooltips for navbar items
        const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });

        // Set active navigation item based on current page
        this.setActiveNavItem();

        // Initialize notification badge updates
        this.initializeNotifications();

        // Handle notification mark as read
        this.handleNotificationActions();
    }

    setActiveNavItem() {
        const currentPage = window.location.pathname.split('/').pop() || 'dashboard.html';
        const navLinks = document.querySelectorAll('.navbar-nav .nav-link');
        
        navLinks.forEach(link => {
            const href = link.getAttribute('href');
            if (href === currentPage) {
                link.classList.add('active');
            } else {
                link.classList.remove('active');
            }
        });
    }

    initializeNotifications() {
        // Simulate real-time notification updates
        setInterval(() => {
            this.updateNotificationBadge();
        }, 30000); // Update every 30 seconds
    }

    updateNotificationBadge() {
        const badge = document.querySelector('#notificationDropdown .badge');
        if (badge) {
            // Simulate notification count changes
            const currentCount = parseInt(badge.textContent);
            const newCount = Math.max(0, currentCount + Math.floor(Math.random() * 3) - 1);
            badge.textContent = newCount;
            
            if (newCount > 0) {
                badge.classList.remove('d-none');
            } else {
                badge.classList.add('d-none');
            }
        }
    }

    handleNotificationActions() {
        // Mark notifications as read when clicked
        const notificationItems = document.querySelectorAll('#notificationDropdown .dropdown-item');
        notificationItems.forEach(item => {
            item.addEventListener('click', (e) => {
                if (!item.querySelector('small')) return; // Skip header items
                
                // Mark as read (visual feedback)
                item.style.opacity = '0.7';
                
                // Decrease notification count
                const badge = document.querySelector('#notificationDropdown .badge');
                if (badge) {
                    const currentCount = parseInt(badge.textContent);
                    badge.textContent = Math.max(0, currentCount - 1);
                    
                    if (badge.textContent === '0') {
                        badge.classList.add('d-none');
                    }
                }
            });
        });
    }

    // Public method to add new notification
    addNotification(type, title, message) {
        const notificationList = document.querySelector('#notificationDropdown .dropdown-menu');
        if (!notificationList) return;

        const icons = {
            'info': 'fas fa-info-circle text-info',
            'success': 'fas fa-check-circle text-success',
            'warning': 'fas fa-exclamation-triangle text-warning',
            'error': 'fas fa-times-circle text-danger',
            'patient': 'fas fa-user-plus text-primary',
            'appointment': 'fas fa-calendar-check text-info',
            'payment': 'fas fa-money-bill-wave text-success'
        };

        const newNotification = document.createElement('li');
        newNotification.innerHTML = `
            <a class="dropdown-item" href="#">
                <div class="d-flex align-items-center">
                    <i class="${icons[type] || icons['info']} me-2"></i>
                    <div>
                        <div class="fw-bold">${title}</div>
                        <small class="text-muted">${message}</small>
                    </div>
                </div>
            </a>
        `;

        // Insert after header
        const header = notificationList.querySelector('.dropdown-header');
        if (header) {
            header.insertAdjacentElement('afterend', newNotification);
        }

        // Update badge
        const badge = document.querySelector('#notificationDropdown .badge');
        if (badge) {
            const currentCount = parseInt(badge.textContent);
            badge.textContent = currentCount + 1;
            badge.classList.remove('d-none');
        }
    }

    // Public method to clear all notifications
    clearNotifications() {
        const notificationList = document.querySelector('#notificationDropdown .dropdown-menu');
        if (!notificationList) return;

        // Remove all notification items except header and divider
        const items = notificationList.querySelectorAll('li');
        items.forEach(item => {
            if (!item.querySelector('.dropdown-header') && !item.querySelector('.dropdown-divider')) {
                item.remove();
            }
        });

        // Reset badge
        const badge = document.querySelector('#notificationDropdown .badge');
        if (badge) {
            badge.textContent = '0';
            badge.classList.add('d-none');
        }
    }
}

// Initialize navbar loader
const navbarLoader = new NavbarLoader();

// Export for global access
window.navbarLoader = navbarLoader;
