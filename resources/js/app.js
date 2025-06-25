import './bootstrap';
import Alpine from 'alpinejs';
import AOS from 'aos';
import 'aos/dist/aos.css';

AOS.init();


// ✅ IMPORT these correctly from Lucide
import { createIcons, icons } from 'lucide';

window.Alpine = Alpine;
Alpine.start();

// ✅ MAKE SURE lucide is globally available (optional)
window.lucide = { createIcons, icons };

// ✅ INIT ICONS with the icons set
document.addEventListener('DOMContentLoaded', () => {
    window.lucide.createIcons({ icons: window.lucide.icons });
    


    document.addEventListener('DOMContentLoaded', () => {
    // Hide the loading overlay once everything has loaded
    window.addEventListener('load', () => {
        const loader = document.getElementById('loading-overlay');
        if (loader) {
            loader.classList.add('hidden');
        }
    });

    // Show loader on any form submit
    document.querySelectorAll('form').forEach(form => {
        form.addEventListener('submit', () => {
            const loader = document.getElementById('loading-overlay');
            if (loader) {
                loader.classList.remove('hidden');
            }
        });
    });
});

}); 
