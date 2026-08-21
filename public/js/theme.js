/**
 * Manejo de Tema Claro / Oscuro - Gestión Institucional Cristo Rey
 */
(function() {
    // Inicialización inmediata para evitar parpadeo blanco (FOUC)
    const savedTheme = localStorage.getItem('theme');
    const systemPrefersDark = window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches;
    
    if (savedTheme === 'dark' || (!savedTheme && systemPrefersDark)) {
        document.documentElement.setAttribute('data-theme', 'dark');
    } else {
        document.documentElement.setAttribute('data-theme', 'light');
    }
})();

function toggleTheme() {
    const currentTheme = document.documentElement.getAttribute('data-theme') || 'light';
    const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
    
    document.documentElement.setAttribute('data-theme', newTheme);
    localStorage.setItem('theme', newTheme);
    
    updateThemeToggleIcons();
}

function updateThemeToggleIcons() {
    const isDark = document.documentElement.getAttribute('data-theme') === 'dark';
    document.querySelectorAll('.theme-toggle-icon').forEach(el => {
        el.textContent = isDark ? '☀️' : '🌙';
    });
}

function ensureTopbarThemeToggle() {
    const topbarRight = document.querySelector('.topbar-right');
    if (topbarRight && !topbarRight.querySelector('.theme-toggle-btn')) {
        const btn = document.createElement('button');
        btn.type = 'button';
        btn.className = 'theme-toggle-btn';
        btn.setAttribute('onclick', 'toggleTheme()');
        btn.setAttribute('title', 'Alternar modo claro / oscuro');
        btn.setAttribute('aria-label', 'Alternar tema');
        btn.innerHTML = '<span class="theme-toggle-icon">' + (document.documentElement.getAttribute('data-theme') === 'dark' ? '☀️' : '🌙') + '</span>';
        topbarRight.insertBefore(btn, topbarRight.firstChild);
    }
}

window.addEventListener('DOMContentLoaded', () => {
    ensureTopbarThemeToggle();
    updateThemeToggleIcons();
});
