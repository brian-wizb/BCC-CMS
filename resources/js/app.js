import './bootstrap';

const THEME_STORAGE_KEY = 'bcc-theme';
const SIDEBAR_COLLAPSE_KEY = 'bcc-sidebar-collapsed';
const SIDEBAR_SECTION_KEY = 'bcc-sidebar-sections';
const themeQuery = window.matchMedia('(prefers-color-scheme: dark)');
const desktopSidebarQuery = window.matchMedia('(min-width: 1024px)');

function getStoredTheme() {
    const theme = window.localStorage.getItem(THEME_STORAGE_KEY);

    return theme === 'light' || theme === 'dark' ? theme : null;
}

function getPreferredTheme() {
    return getStoredTheme() ?? (themeQuery.matches ? 'dark' : 'light');
}

function updateThemeToggleUI(theme) {
    document.querySelectorAll('[data-theme-label]').forEach((node) => {
        node.textContent = theme === 'dark' ? 'Dark mode' : 'Light mode';
    });

    document.querySelectorAll('[data-theme-toggle]').forEach((button) => {
        button.setAttribute('aria-label', theme === 'dark' ? 'Switch to light mode' : 'Switch to dark mode');
        button.dataset.themeState = theme;
    });
}

function applyTheme(theme) {
    document.documentElement.dataset.theme = theme;
    document.documentElement.style.colorScheme = theme;
    updateThemeToggleUI(theme);

    window.dispatchEvent(new CustomEvent('bcc-theme-change', {
        detail: { theme },
    }));
}

function toggleTheme() {
    const nextTheme = document.documentElement.dataset.theme === 'dark' ? 'light' : 'dark';
    window.localStorage.setItem(THEME_STORAGE_KEY, nextTheme);
    applyTheme(nextTheme);
}

function initializeTheme() {
    applyTheme(getPreferredTheme());

    document.querySelectorAll('[data-theme-toggle]').forEach((button) => {
        button.addEventListener('click', toggleTheme);
    });

    themeQuery.addEventListener('change', (event) => {
        if (getStoredTheme()) {
            return;
        }

        applyTheme(event.matches ? 'dark' : 'light');
    });
}

function getStoredSectionState() {
    try {
        const value = window.localStorage.getItem(SIDEBAR_SECTION_KEY);

        return value ? JSON.parse(value) : {};
    } catch {
        return {};
    }
}

function persistSectionState(state) {
    window.localStorage.setItem(SIDEBAR_SECTION_KEY, JSON.stringify(state));
}

function updateSidebarToggleUI() {
    const isDesktop = desktopSidebarQuery.matches;
    const isCollapsed = document.documentElement.dataset.sidebarCollapsed === 'true';
    const isOpen = document.documentElement.dataset.sidebarOpen === 'true';

    document.querySelectorAll('[data-sidebar-toggle]').forEach((button) => {
        const label = isDesktop
            ? (isCollapsed ? 'Expand navigation' : 'Collapse navigation')
            : (isOpen ? 'Close navigation' : 'Open navigation');

        button.setAttribute('aria-label', label);
        button.dataset.sidebarState = isDesktop
            ? (isCollapsed ? 'collapsed' : 'expanded')
            : (isOpen ? 'open' : 'closed');
    });
}

function setSidebarOpen(open) {
    document.documentElement.dataset.sidebarOpen = open ? 'true' : 'false';
    updateSidebarToggleUI();
}

function setSidebarCollapsed(collapsed) {
    document.documentElement.dataset.sidebarCollapsed = collapsed ? 'true' : 'false';
    window.localStorage.setItem(SIDEBAR_COLLAPSE_KEY, collapsed ? 'true' : 'false');
    updateSidebarToggleUI();
}

function syncSectionHeights() {
    document.querySelectorAll('[data-nav-section]').forEach((section) => {
        const panel = section.querySelector('[data-section-panel]');

        if (!panel) {
            return;
        }

        panel.style.maxHeight = section.dataset.expanded === 'true' ? `${panel.scrollHeight}px` : '0px';
    });
}

function setSectionExpanded(section, expanded, state, persist = true) {
    const button = section.querySelector('[data-section-toggle]');
    const panel = section.querySelector('[data-section-panel]');
    const sectionId = section.dataset.sectionId;

    if (!button || !panel || !sectionId) {
        return;
    }

    section.dataset.expanded = expanded ? 'true' : 'false';
    button.setAttribute('aria-expanded', expanded ? 'true' : 'false');
    panel.style.maxHeight = expanded ? `${panel.scrollHeight}px` : '0px';

    if (persist) {
        state[sectionId] = expanded;
        persistSectionState(state);
    }
}

function initializeSidebar() {
    const sectionState = getStoredSectionState();

    document.querySelectorAll('[data-nav-section]').forEach((section) => {
        const sectionId = section.dataset.sectionId;
        const active = section.dataset.activeSection === 'true';
        const defaultOpen = section.dataset.defaultOpen === 'true';
        const expanded = typeof sectionState[sectionId] === 'boolean' ? sectionState[sectionId] : (active || defaultOpen);
        const button = section.querySelector('[data-section-toggle]');

        setSectionExpanded(section, expanded, sectionState, false);

        button?.addEventListener('click', () => {
            const nextState = section.dataset.expanded !== 'true';
            setSectionExpanded(section, nextState, sectionState);
        });
    });

    syncSectionHeights();
    updateSidebarToggleUI();

    document.querySelectorAll('[data-sidebar-toggle]').forEach((button) => {
        button.addEventListener('click', () => {
            if (desktopSidebarQuery.matches) {
                setSidebarCollapsed(document.documentElement.dataset.sidebarCollapsed !== 'true');
                syncSectionHeights();
                return;
            }

            setSidebarOpen(document.documentElement.dataset.sidebarOpen !== 'true');
        });
    });

    document.querySelector('[data-sidebar-backdrop]')?.addEventListener('click', () => {
        setSidebarOpen(false);
    });

    document.querySelectorAll('[data-sidebar-close]').forEach((link) => {
        link.addEventListener('click', () => {
            if (!desktopSidebarQuery.matches) {
                setSidebarOpen(false);
            }
        });
    });

    desktopSidebarQuery.addEventListener('change', () => {
        setSidebarOpen(false);
        updateSidebarToggleUI();
        syncSectionHeights();
    });

    window.addEventListener('resize', syncSectionHeights);
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => {
        initializeTheme();
        initializeSidebar();
    }, { once: true });
} else {
    initializeTheme();
    initializeSidebar();
}
