document.addEventListener('DOMContentLoaded', () => {
    // Mobile Menu Toggle
    const menuBtn = document.getElementById('menu-btn');
    const navMenu = document.getElementById('nav-menu');
    const menuIcon = menuBtn.querySelector('i');

    menuBtn.addEventListener('click', () => {
        navMenu.classList.toggle('active');
        
        // Toggle icon between menu and x
        const isOpened = navMenu.classList.contains('active');
        menuIcon.setAttribute('data-lucide', isOpened ? 'x' : 'menu');
        lucide.createIcons(); // Re-render icons
    });

    // Close mobile menu when clicking a link
    document.querySelectorAll('.nav-links a').forEach(link => {
        link.addEventListener('click', () => {
            navMenu.classList.remove('active');
            menuIcon.setAttribute('data-lucide', 'menu');
            lucide.createIcons();
        });
    });

    // Intersection Observer for Scroll Animations
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };

    const scrollObserver = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('visible');
                scrollObserver.unobserve(entry.target); // Only animate once
            }
        });
    }, observerOptions);

    // Apply animation classes to elements
    const animateElements = document.querySelectorAll('.course-card, .feature-item, .hero-content');
    
    animateElements.forEach(el => {
        el.style.opacity = '0';
        el.style.transform = 'translateY(30px)';
        el.style.transition = 'opacity 0.8s ease-out, transform 0.8s ease-out';
        scrollObserver.observe(el);
    });

    // Custom CSS for the visible state (could also be in style.css)
    const style = document.createElement('style');
    style.textContent = `
        .visible {
            opacity: 1 !important;
            transform: translateY(0) !important;
        }
    `;
    document.head.appendChild(style);

    // Navbar Background Change on Scroll
    const navbar = document.querySelector('.navbar');
    window.addEventListener('scroll', () => {
        if (window.scrollY > 50) {
            navbar.style.background = 'rgba(15, 23, 42, 0.95)';
            navbar.style.padding = '0.5rem 0';
        } else {
            navbar.style.background = 'rgba(15, 23, 42, 0.8)';
            navbar.style.padding = '0';
        }
    });
});
