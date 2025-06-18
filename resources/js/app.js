import './bootstrap';
import Alpine from 'alpinejs';

// ✅ IMPORT these correctly from Lucide
import { createIcons, icons } from 'lucide';

window.Alpine = Alpine;
Alpine.start();

// ✅ MAKE SURE lucide is globally available (optional)
window.lucide = { createIcons, icons };

// ✅ INIT ICONS with the icons set
document.addEventListener('DOMContentLoaded', () => {
    window.lucide.createIcons({ icons: window.lucide.icons });
}); 
