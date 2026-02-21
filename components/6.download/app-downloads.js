(function() {
    document.addEventListener('DOMContentLoaded', () => {
        const section = document.querySelector('#sp-download-hub');
        const glass = section.querySelector('.sp-glass-mockup');

        // Professional Parallax Tracking
        section.addEventListener('mousemove', (e) => {
            const rect = section.getBoundingClientRect();
            const x = (e.clientX - rect.left) / rect.width - 0.5;
            const y = (e.clientY - rect.top) / rect.height - 0.5;

            glass.style.transform = `perspective(1000px) rotateY(${x * 10}deg) rotateX(${-y * 10}deg) translateY(-10px)`;
        });

        // Reset on leave
        section.addEventListener('mouseleave', () => {
            glass.style.transform = `perspective(1000px) rotateY(0deg) rotateX(0deg) translateY(0)`;
        });
        
        // Button Pulse Effect
        const playBtn = section.querySelector('.sp-play-btn');
        playBtn.addEventListener('mouseenter', () => {
            playBtn.style.transform = 'scale(1.1)';
            playBtn.style.borderColor = '#8b8bff';
        });
        playBtn.addEventListener('mouseleave', () => {
            playBtn.style.transform = 'scale(1)';
            playBtn.style.borderColor = '#d2d2d7';
        });
    });
})();