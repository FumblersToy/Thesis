 // Add interactive hover effects
        document.querySelectorAll('.hover-lift').forEach(card => {
            card.addEventListener('mouseenter', function() {
                this.style.transform = 'translateY(-8px) scale(1.02)';
            });
            
            card.addEventListener('mouseleave', function() {
                this.style.transform = 'translateY(0px) scale(1)';
            });
        });

        // Parallax effect for background elements
        document.addEventListener('mousemove', (e) => {
            const mouseX = e.clientX / window.innerWidth;
            const mouseY = e.clientY / window.innerHeight;
            
            const elements = document.querySelectorAll('.floating-element');
            elements.forEach((el, index) => {
                const speed = (index + 1) * 0.03;
                const x = (mouseX - 0.5) * speed * 100;
                const y = (mouseY - 0.5) * speed * 100;
                
                const currentTransform = el.style.transform || '';
                el.style.transform = `translate(${x}px, ${y}px) ${currentTransform.includes('rotate') ? currentTransform.split('translate')[1] || '' : ''}`;
            });
        });

        // Add click ripple effect to buttons
        document.querySelectorAll('a[href]').forEach(button => {
            button.addEventListener('click', function(e) {
                const ripple = document.createElement('span');
                const rect = this.getBoundingClientRect();
                const size = Math.max(rect.width, rect.height);
                const x = e.clientX - rect.left - size / 2;
                const y = e.clientY - rect.top - size / 2;
                
                ripple.style.width = ripple.style.height = size + 'px';
                ripple.style.left = x + 'px';
                ripple.style.top = y + 'px';
                ripple.classList.add('absolute', 'bg-white/30', 'rounded-full', 'pointer-events-none');
                ripple.style.animation = 'ripple 0.6s linear';
                
                this.appendChild(ripple);
                
                setTimeout(() => {
                    ripple.remove();
                }, 600);
            });
        });

        // Add CSS for ripple animation
        const style = document.createElement('style');
        style.textContent = `
            @keyframes ripple {
                to {
                    transform: scale(4);
                    opacity: 0;
                }
            }
        `;
        document.head.appendChild(style);

        // Staggered animation for cards
        setTimeout(() => {
            document.querySelector('.slide-in-left').style.animationDelay = '0s';
            document.querySelector('.slide-in-right').style.animationDelay = '0.2s';
        }, 100);

        // Dynamic background color shifting
        let hue = 0;
        setInterval(() => {
            hue = (hue + 1) % 360;
            document.body.style.background = `linear-gradient(135deg, hsl(${hue}, 70%, 70%) 0%, hsl(${(hue + 60) % 360}, 70%, 65%) 50%, hsl(${(hue + 120) % 360}, 70%, 75%) 100%)`;
        }, 100);