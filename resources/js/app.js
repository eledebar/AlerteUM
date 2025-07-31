import './bootstrap';

import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.start();

document.addEventListener('DOMContentLoaded', () => {
    const toggle = document.getElementById('theme-toggle');

    if (localStorage.theme === 'dark') {
        document.documentElement.classList.add('dark');
    }

    toggle?.addEventListener('click', () => {
        const isDark = document.documentElement.classList.toggle('dark');
        localStorage.theme = isDark ? 'dark' : 'light';
    });
});
