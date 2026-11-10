(() => {
    'use strict';

    const links = [
        ['Inicio', 'index.php'],
        ['Topología', 'topologia.php'],
        ['Inventario', 'equipos_lista.php'],
        ['Registrar', 'equipos_agregar.php'],
        ['Editar puertos', 'equipos_editar_puertos.php'],
        ['Ubicaciones', 'ubicaciones.php'],
        ['Tipos y presets', 'ajustes_presets.php']
    ];

    const init = () => {
        let nav = document.querySelector('.netmap-nav');

        if (!nav) {
            nav = document.querySelector('header') || document.createElement('nav');
            nav.className = 'netmap-nav';
            nav.setAttribute('aria-label', 'Navegación principal');

            if (!nav.parentElement) {
                document.body.prepend(nav);
            }
        }

        const currentPage = location.pathname.split('/').pop() || 'index.php';

        nav.innerHTML =
            '<a class="brand" href="index.php">◈ NetMap</a>' +
            links.map(([label, url]) => {
                const active = currentPage === url ? ' aria-current="page"' : '';
                return '<a href="' + url + '"' + active + '>' + label + '</a>';
            }).join('') +
            '<span class="spacer"></span>' +
            '<button type="button" id="netmapThemeToggle">☾ Modo oscuro</button>';

        const key = 'netmap-theme';

        const apply = dark => {
            document.documentElement.classList.toggle('netmap-dark', dark);

            const button = document.getElementById('netmapThemeToggle');
            if (button) {
                button.textContent = dark ? '☀ Modo claro' : '☾ Modo oscuro';
                button.setAttribute('aria-pressed', String(dark));
            }
        };

        let dark = true;

        try {
            const savedTheme = localStorage.getItem(key);
            if (savedTheme !== null) {
                dark = savedTheme === 'dark';
            }
        } catch (_) {}

        apply(dark);

        document.getElementById('netmapThemeToggle')?.addEventListener('click', () => {
            dark = !document.documentElement.classList.contains('netmap-dark');
            apply(dark);

            try {
                localStorage.setItem(key, dark ? 'dark' : 'light');
            } catch (_) {}
        });
    };

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();
