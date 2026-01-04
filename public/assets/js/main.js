// Enterprise Gallery JavaScript
class GalleryApp {
    constructor() {
        this.cart = JSON.parse(localStorage.getItem('cart') || '[]');
        this.init();
    }
    
    init() {
        this.updateCartCount();
        this.bindEvents();
    }
    
    bindEvents() {
        // Mobile menu toggle
        window.toggleMobileMenu = () => {
            const mobileNav = document.getElementById('mobile-nav');
            if (mobileNav) {
                mobileNav.classList.toggle('active');
            }
        };
        
        // Mobile dropdown toggle
        document.addEventListener('click', (e) => {
            if (e.target.classList.contains('mobile-dropdown-toggle')) {
                e.preventDefault();
                const dropdown = e.target.closest('.mobile-dropdown');
                dropdown.classList.toggle('active');
            }
        });
        
        // Close mobile menu when clicking outside
        document.addEventListener('click', (event) => {
            const navbar = document.querySelector('.navbar');
            const mobileNav = document.getElementById('mobile-nav');
            if (navbar && mobileNav && !navbar.contains(event.target)) {
                mobileNav.classList.remove('active');
            }
        });
        
        // Cart functions
        window.addToCart = (paintingId) => {
            if (!this.cart.includes(paintingId)) {
                this.cart.push(paintingId);
                localStorage.setItem('cart', JSON.stringify(this.cart));
                this.updateCartCount();
                this.showNotification('Added to cart!', 'success');
            } else {
                this.showNotification('Item already in cart!', 'warning');
            }
        };
        
        window.removeFromCart = (paintingId) => {
            this.cart = this.cart.filter(id => id !== paintingId);
            localStorage.setItem('cart', JSON.stringify(this.cart));
            this.updateCartCount();
            this.showNotification('Removed from cart!', 'info');
        };
    }
    
    updateCartCount() {
        const countElements = document.querySelectorAll('#cart-count, #cart-count-mobile');
        countElements.forEach(element => {
            if (element) element.textContent = this.cart.length;
        });
    }
    
    showNotification(message, type = 'info') {
        // Create notification element
        const notification = document.createElement('div');
        notification.className = `notification notification-${type}`;
        notification.textContent = message;
        notification.style.cssText = `
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 1rem 1.5rem;
            border-radius: 8px;
            color: white;
            font-weight: 500;
            z-index: 10000;
            transform: translateX(100%);
            transition: transform 0.3s ease;
        `;
        
        // Set background color based on type
        const colors = {
            success: '#27ae60',
            warning: '#f39c12',
            error: '#e74c3c',
            info: '#3498db'
        };
        notification.style.backgroundColor = colors[type] || colors.info;
        
        document.body.appendChild(notification);
        
        // Animate in
        setTimeout(() => {
            notification.style.transform = 'translateX(0)';
        }, 100);
        
        // Remove after 3 seconds
        setTimeout(() => {
            notification.style.transform = 'translateX(100%)';
            setTimeout(() => {
                if (notification.parentNode) {
                    notification.parentNode.removeChild(notification);
                }
            }, 300);
        }, 3000);
    }
    
    // API helper methods
    async apiCall(endpoint, options = {}) {
        const defaultOptions = {
            headers: {
                'Content-Type': 'application/json',
            },
        };
        
        const config = { ...defaultOptions, ...options };
        
        try {
            const response = await fetch(`/gallery/api${endpoint}`, config);
            const data = await response.json();
            
            if (!response.ok) {
                throw new Error(data.message || 'API request failed');
            }
            
            return data;
        } catch (error) {
            console.error('API Error:', error);
            this.showNotification('Network error occurred', 'error');
            throw error;
        }
    }
    
    async loadCartItems() {
        if (this.cart.length === 0) return [];
        
        try {
            const response = await this.apiCall('/cart/items', {
                method: 'POST',
                body: JSON.stringify({ ids: this.cart })
            });
            
            return response.success ? response.data : [];
        } catch (error) {
            return [];
        }
    }
    
    async placeOrder(orderData) {
        try {
            const response = await this.apiCall('/orders', {
                method: 'POST',
                body: JSON.stringify({
                    ...orderData,
                    items: this.cart,
                    total: orderData.total
                })
            });
            
            if (response.success) {
                localStorage.removeItem('cart');
                this.cart = [];
                this.updateCartCount();
                return response.data;
            }
            
            throw new Error(response.message);
        } catch (error) {
            throw error;
        }
    }
}

// Initialize app when DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
    window.galleryApp = new GalleryApp();
});

// Utility functions
window.formatPrice = (price) => {
    return new Intl.NumberFormat('en-IN', {
        style: 'currency',
        currency: 'INR'
    }).format(price);
};

window.debounce = (func, wait) => {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
};