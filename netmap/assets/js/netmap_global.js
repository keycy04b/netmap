(() => {
    'use strict';
    const links = [
        ['Inicio','index.html'],
        ['Inventario','equipos_lista.php'],
        ['Registrar','equipos_agregar.php'],
        ['Editar puertos','equipos_editar_puertos.php'],
        ['Presets','ajustes_presets.php']
    ];
    const init = () => {
        let nav = document.querySelector('.netmap-nav');
        if (!nav) {
            nav = document.createElement('nav');
            nav.className = 'netmap-nav';
            nav.setAttribute('aria-label','Navegación principal');
            nav.innerHTML = '<a class="brand" href="index.html">◈ NetMap</a>' +
                links.map(([label,url]) => '<a href="' + url + '">' + label + '</a>').join('') +
                '<span class="spacer"></span><button type="button" id="netmapThemeToggle">☾ Modo oscuro</button>';
            const header = document.querySelector('header');
            if (header) header.insertAdjacentElement('afterend', nav);
            else document.body.prepend(nav);
        }
        const key = 'netmap-theme';
        const apply = dark => {
            document.documentElement.classList.toggle('netmap-dark', dark);
            const button = document.getElementById('netmapThemeToggle');
            if (button) button.textContent = dark ? '☀ Modo claro' : '☾ Modo oscuro';
        };
        let dark = true;
        try {
            const savedTheme = localStorage.getItem(key);
            if (savedTheme !== null) dark = savedTheme === 'dark';
        } catch (_) {}
        apply(dark);
        document.getElementById('netmapThemeToggle')?.addEventListener('click', () => {
            dark = !document.documentElement.classList.contains('netmap-dark');
            apply(dark);
            try { localStorage.setItem(key, dark ? 'dark' : 'light'); } catch (_) {}
        });
    };
    if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded',init);
    else init();
})();