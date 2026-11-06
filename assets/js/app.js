document.addEventListener('DOMContentLoaded', () => {
    const btn = document.getElementById('btnMenu');
    const sidebar = document.getElementById('sidebar');

    if (!btn || !sidebar) return;

    btn.addEventListener('click', () => {
        sidebar.classList.toggle('abierto');
    });

    // Cerrar al tocar un link (en móvil)
    sidebar.querySelectorAll('a').forEach(a => {
        a.addEventListener('click', () => sidebar.classList.remove('abierto'));
    });
});