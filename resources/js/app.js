import './bootstrap';

const THEME_STORAGE_KEY = 'bcc-theme';
const SIDEBAR_COLLAPSE_KEY = 'bcc-sidebar-collapsed';
const SIDEBAR_SECTION_KEY = 'bcc-sidebar-sections';
const SIDEBAR_SCROLL_KEY = 'bcc-sidebar-scroll';
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

    // Sidebar scroll persistence via localStorage (survives tab across navigation)
    const sidebarScrollEl = document.querySelector('.sidebar-scroll');    if (sidebarScrollEl) {
        const target = parseInt(window.localStorage.getItem(SIDEBAR_SCROLL_KEY), 10) || 0;
        if (target > 0) {
            // Keep trying with rAF until the scroll sticks (panels may still be
            // expanding which increases scrollHeight and allows larger scrollTop).
            let attempts = 0;
            const tryRestore = () => {
                sidebarScrollEl.scrollTop = target;
                attempts++;
                if (sidebarScrollEl.scrollTop < target - 5 && attempts < 40) {
                    requestAnimationFrame(tryRestore);
                }
            };
            requestAnimationFrame(tryRestore);
        }
        let scrollSaveTimer;
        sidebarScrollEl.addEventListener('scroll', () => {
            clearTimeout(scrollSaveTimer);
            scrollSaveTimer = setTimeout(() => {
                window.localStorage.setItem(SIDEBAR_SCROLL_KEY, String(sidebarScrollEl.scrollTop));
            }, 80);
        });
    }
}

// ── Toast notification system ─────────────────────────────────────────────
const TOAST_ICONS = {
    success: `<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg>`,
    error:   `<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><line x1="15" y1="9" x2="9" y2="15"></line><line x1="9" y1="9" x2="15" y2="15"></line></svg>`,
    warning: `<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"></path><line x1="12" y1="9" x2="12" y2="13"></line><line x1="12" y1="17" x2="12.01" y2="17"></line></svg>`,
    info:    `<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="16" x2="12" y2="12"></line><line x1="12" y1="8" x2="12.01" y2="8"></line></svg>`,
};

function getOrCreateToastContainer() {
    let container = document.getElementById('bcc-toast-container');
    if (!container) {
        container = document.createElement('div');
        container.id = 'bcc-toast-container';
        container.className = 'toast-container';
        container.setAttribute('aria-live', 'polite');
        container.setAttribute('aria-atomic', 'false');
        document.body.appendChild(container);
    }
    return container;
}

function dismissToast(toast) {
    if (toast.dataset.dismissed) return;
    toast.dataset.dismissed = 'true';
    toast.style.animation = 'toastSlideOut 320ms cubic-bezier(0.4, 0, 1, 1) forwards';
    toast.addEventListener('animationend', () => toast.remove(), { once: true });
}

function showToast(type, message, duration = 5200) {
    const container = getOrCreateToastContainer();
    const safeType = ['success', 'error', 'warning', 'info'].includes(type) ? type : 'info';
    const label = safeType.charAt(0).toUpperCase() + safeType.slice(1);

    const toast = document.createElement('div');
    toast.className = `toast toast-${safeType}`;
    toast.setAttribute('role', 'alert');
    toast.style.setProperty('--toast-duration', `${duration}ms`);
    toast.innerHTML = `
        <span class="toast-icon toast-icon-${safeType}" aria-hidden="true">${TOAST_ICONS[safeType]}</span>
        <div class="toast-body">
            <p class="toast-type-label">${label}</p>
            <p class="toast-message">${message}</p>
        </div>
        <button type="button" class="toast-dismiss" aria-label="Dismiss notification">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.6" stroke-linecap="round"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
        </button>
        <div class="toast-progress toast-progress-${safeType}"></div>
    `;

    toast.querySelector('.toast-dismiss').addEventListener('click', () => dismissToast(toast));
    container.appendChild(toast);

    const timer = setTimeout(() => dismissToast(toast), duration);
    toast.addEventListener('mouseenter', () => clearTimeout(timer));
}

function initializeToasts() {
    const dataEl = document.getElementById('bcc-toast-data');
    if (!dataEl) return;
    dataEl.querySelectorAll('[data-toast]').forEach((el, i) => {
        setTimeout(() => {
            showToast(el.dataset.toast, el.dataset.toastMsg);
        }, i * 130);
    });
}

// Expose globally for in-page toast calls
window.showToast = showToast;

// ── Button ripple micro-interaction ───────────────────────────────────────
function initializeRipple() {
    document.addEventListener('pointerdown', (e) => {
        const btn = e.target.closest('.btn-primary, .btn-secondary, .btn-danger');
        if (!btn) return;
        const rect = btn.getBoundingClientRect();
        const ripple = document.createElement('span');
        const size = Math.max(rect.width, rect.height) * 2;
        ripple.className = 'btn-ripple';
        ripple.style.cssText = `width:${size}px;height:${size}px;left:${e.clientX - rect.left - size / 2}px;top:${e.clientY - rect.top - size / 2}px`;
        btn.appendChild(ripple);
        ripple.addEventListener('animationend', () => ripple.remove(), { once: true });
    });
}

// ── Custom confirm modal ─────────────────────────────────────────────────
function showConfirmModal(message, onConfirm) {
    const backdrop = document.createElement('div');
    backdrop.className = 'bcc-modal-backdrop';
    backdrop.setAttribute('aria-modal', 'true');
    backdrop.setAttribute('role', 'alertdialog');
    backdrop.innerHTML = `
        <div class="bcc-modal">
            <div class="bcc-modal-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"></path><line x1="12" y1="9" x2="12" y2="13"></line><line x1="12" y1="17" x2="12.01" y2="17"></line></svg>
            </div>
            <p class="bcc-modal-title">Are you sure?</p>
            <p class="bcc-modal-message">${message}</p>
            <div class="bcc-modal-actions">
                <button type="button" class="bcc-modal-cancel">Cancel</button>
                <button type="button" class="bcc-modal-confirm">Confirm</button>
            </div>
        </div>
    `;

    let closed = false;

    function close(confirmed) {
        if (closed) return;
        closed = true;
        document.removeEventListener('keydown', onEsc);
        const modal = backdrop.querySelector('.bcc-modal');
        modal.style.animation = 'bccModalOut 240ms cubic-bezier(0.4, 0, 1, 1) forwards';
        backdrop.style.animation = 'bccBackdropOut 240ms ease forwards';
        setTimeout(() => {
            backdrop.remove();
            if (confirmed) onConfirm();
        }, 250);
    }

    const onEsc = (e) => { if (e.key === 'Escape') close(false); };
    document.addEventListener('keydown', onEsc);

    backdrop.querySelector('.bcc-modal-cancel').addEventListener('click', () => close(false));
    backdrop.querySelector('.bcc-modal-confirm').addEventListener('click', () => close(true));
    backdrop.addEventListener('click', (e) => { if (e.target === backdrop) close(false); });

    document.body.appendChild(backdrop);
    setTimeout(() => backdrop.querySelector('.bcc-modal-confirm').focus(), 40);
}

function initializeConfirmForms() {
    document.addEventListener('submit', (e) => {
        const form = e.target;
        if (!(form instanceof HTMLFormElement) || !form.dataset.confirm) return;
        e.preventDefault();
        const message = form.dataset.confirm;
        showConfirmModal(message, () => {
            form.removeAttribute('data-confirm');
            form.requestSubmit();
        });
    });
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => {
        initializeTheme();
        initializeSidebar();
        initializeToasts();
        initializeRipple();
        initializeConfirmForms();
    }, { once: true });
} else {
    initializeTheme();
    initializeSidebar();
    initializeToasts();
    initializeRipple();
    initializeConfirmForms();
}
