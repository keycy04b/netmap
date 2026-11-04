(() => {
    'use strict';

    const root = document.documentElement;
    const storageKey = 'netmap-theme';
    const saved = localStorage.getItem(storageKey);
    root.dataset.theme = saved === 'light' ? 'light' : 'dark';

    function updateButton() {
        const button = document.querySelector('[data-theme-toggle]');
        if (!button) return;
        const dark = root.dataset.theme === 'dark';
        button.textContent = dark ? '☀ Modo claro' : '☾ Modo oscuro';
        button.setAttribute('aria-pressed', String(dark));
    }

    document.addEventListener('click', event => {
        const button = event.target.closest('[data-theme-toggle]');
        if (!button) return;
        root.dataset.theme = root.dataset.theme === 'dark' ? 'light' : 'dark';
        localStorage.setItem(storageKey, root.dataset.theme);
        updateButton();
    });

    updateButton();
})();