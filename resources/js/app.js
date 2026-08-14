import { createIcons, icons } from 'lucide';

// Initialize Lucide icons on page load and on Livewire DOM updates
function initLucide() {
    createIcons({ icons });
}

document.addEventListener('DOMContentLoaded', initLucide);
document.addEventListener('livewire:navigated', initLucide);
document.addEventListener('livewire:init', () => {
    Livewire.hook('morph.updated', () => {
        initLucide();
    });
});

window.initLucide = initLucide;
