import './bootstrap';

const THEME_STORAGE_KEY = 'bcc-theme';
const SIDEBAR_COLLAPSE_KEY = 'bcc-sidebar-collapsed';
const SIDEBAR_SECTION_KEY = 'bcc-sidebar-sections';
const SIDEBAR_SCROLL_KEY = 'bcc-sidebar-scroll';
const themeQuery = window.matchMedia('(prefers-color-scheme: dark)');
const desktopSidebarQuery = window.matchMedia('(min-width: 1024px)');

// ── THEME SYSTEM (Classic + Premium) ──────────────────────────────────────
const AVAILABLE_THEMES = ['light', 'dark', 'solarized', 'forest'];
const DEFAULT_THEME = 'dark';

const THEME_LABELS = {
    light: { name: 'Light', preview: 'Clean & bright' },
    dark: { name: 'Dark', preview: 'Classic dark' },
    solarized: { name: 'Solarized', preview: 'Warm amber & cream' },
    forest: { name: 'Forest', preview: 'Green & natural' },
};

function getStoredTheme() {
    const theme = window.localStorage.getItem(THEME_STORAGE_KEY);
    return AVAILABLE_THEMES.includes(theme) ? theme : null;
}

function getPreferredTheme() {
    return getStoredTheme() ?? DEFAULT_THEME;
}

function updateThemeUI(theme) {
    // Update any theme labels
    document.querySelectorAll('[data-theme-label]').forEach((node) => {
        node.textContent = THEME_LABELS[theme]?.name || theme;
    });

    // Update theme toggle buttons
    document.querySelectorAll('[data-theme-toggle]').forEach((button) => {
        button.setAttribute('aria-label', `Switch to ${THEME_LABELS[theme]?.name || theme} theme`);
        button.dataset.themeState = theme;
    });

    // Update theme picker trigger label
    document.querySelectorAll('[data-theme-picker-trigger]').forEach((button) => {
        const themeName = THEME_LABELS[theme]?.name || theme;
        button.setAttribute('aria-label', `Theme Select (${themeName})`);
        button.setAttribute('title', `Theme Select (${themeName})`);
    });

    // Update theme picker menu items
    document.querySelectorAll('.theme-option').forEach((option) => {
        const isActive = option.dataset.theme === theme;
        option.dataset.active = isActive ? 'true' : 'false';
        if (isActive) {
            option.setAttribute('aria-current', 'page');
        } else {
            option.removeAttribute('aria-current');
        }
    });
}

function applyTheme(theme) {
    if (!AVAILABLE_THEMES.includes(theme)) {
        theme = DEFAULT_THEME;
    }

    // Apply theme to document root
    document.documentElement.dataset.theme = theme;

    // Set color scheme based on theme
    if (theme === 'light') {
        document.documentElement.style.colorScheme = 'light';
    } else {
        document.documentElement.style.colorScheme = 'dark';
    }

    // Update UI elements
    updateThemeUI(theme);

    // Dispatch custom event for any theme-aware components
    window.dispatchEvent(new CustomEvent('bcc-theme-change', {
        detail: { theme, label: THEME_LABELS[theme]?.name },
    }));
}

function setTheme(theme) {
    if (AVAILABLE_THEMES.includes(theme)) {
        window.localStorage.setItem(THEME_STORAGE_KEY, theme);
        applyTheme(theme);

        // Close all theme pickers if open
        document.querySelectorAll('.theme-picker-menu').forEach((picker) => {
            picker.dataset.hidden = 'true';
        });
        document.querySelectorAll('[data-theme-picker-trigger]').forEach((trigger) => {
            trigger.setAttribute('aria-expanded', 'false');
        });
    }
}

function cycleTheme() {
    const currentTheme = getStoredTheme() || DEFAULT_THEME;
    const nextIndex = (AVAILABLE_THEMES.indexOf(currentTheme) + 1) % AVAILABLE_THEMES.length;
    setTheme(AVAILABLE_THEMES[nextIndex]);
}

function initializeThemePicker() {
    const pickers = document.querySelectorAll('.theme-picker');

    if (!pickers.length) {
        return;
    }

    pickers.forEach((pickerRoot) => {
        const trigger = pickerRoot.querySelector('[data-theme-picker-trigger]');
        const menu = pickerRoot.querySelector('.theme-picker-menu');

        if (!trigger || !menu) {
            return;
        }

        // Toggle menu on trigger click
        trigger.addEventListener('click', (e) => {
            e.preventDefault();
            e.stopPropagation();

            const isHidden = menu.dataset.hidden === 'true';

            // Close other picker menus first
            document.querySelectorAll('.theme-picker-menu').forEach((m) => {
                if (m !== menu) {
                    m.dataset.hidden = 'true';
                }
            });
            document.querySelectorAll('[data-theme-picker-trigger]').forEach((t) => {
                if (t !== trigger) {
                    t.setAttribute('aria-expanded', 'false');
                }
            });

            menu.dataset.hidden = isHidden ? 'false' : 'true';
            trigger.setAttribute('aria-expanded', isHidden ? 'true' : 'false');
        });

        // Prevent menu close when clicking inside menu
        menu.addEventListener('click', (e) => {
            e.stopPropagation();
        });

        // Close menu when clicking outside
        document.addEventListener('click', (e) => {
            if (!trigger.contains(e.target) && !menu.contains(e.target)) {
                menu.dataset.hidden = 'true';
                trigger.setAttribute('aria-expanded', 'false');
            }
        });

        // Handle theme option clicks (within this menu)
        menu.querySelectorAll('.theme-option').forEach((option) => {
            option.addEventListener('click', (e) => {
                e.preventDefault();
                e.stopPropagation();
                const selectedTheme = option.dataset.theme;
                if (selectedTheme) {
                    setTheme(selectedTheme);
                }
            });

            // Keyboard navigation
            option.addEventListener('keydown', (e) => {
                if (e.key === 'Enter' || e.key === ' ') {
                    e.preventDefault();
                    e.stopPropagation();
                    option.click();
                }
            });
        });

        // Close on Escape
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && menu.dataset.hidden === 'false') {
                menu.dataset.hidden = 'true';
                trigger.setAttribute('aria-expanded', 'false');
                trigger.focus();
            }
        });
    });

    // Set initial active theme on menu
    updateThemeUI(getPreferredTheme());
}

function initializeTheme() {
    applyTheme(getPreferredTheme());

    // Legacy theme toggle support (light/dark button)
    document.querySelectorAll('[data-theme-toggle]').forEach((button) => {
        button.addEventListener('click', cycleTheme);
    });

    // Initialize advanced theme picker
    initializeThemePicker();
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

function initializeSidebarHoverTooltips() {
    const links = document.querySelectorAll('.app-sidebar .nav-link[data-tooltip]');

    if (!links.length) {
        return;
    }

    const tooltip = document.createElement('div');
    tooltip.className = 'sidebar-hover-tooltip';
    tooltip.setAttribute('role', 'tooltip');
    tooltip.dataset.visible = 'false';
    document.body.appendChild(tooltip);

    let activeLink = null;

    const canShow = () => {
        return desktopSidebarQuery.matches && document.documentElement.dataset.sidebarCollapsed === 'true';
    };

    const hideTooltip = () => {
        tooltip.dataset.visible = 'false';
        tooltip.textContent = '';
        activeLink = null;
    };

    const positionTooltip = (link) => {
        const rect = link.getBoundingClientRect();
        const gap = 12;
        const top = rect.top + (rect.height / 2);
        const maxWidth = Math.min(300, window.innerWidth - rect.right - gap - 16);

        tooltip.style.maxWidth = `${Math.max(160, maxWidth)}px`;
        tooltip.style.left = `${rect.right + gap}px`;
        tooltip.style.top = `${top}px`;
    };

    const showTooltip = (link) => {
        if (!canShow()) {
            hideTooltip();
            return;
        }

        const text = link.getAttribute('data-tooltip');
        if (!text) {
            hideTooltip();
            return;
        }

        activeLink = link;
        tooltip.textContent = text;
        positionTooltip(link);
        tooltip.dataset.visible = 'true';
    };

    links.forEach((link) => {
        link.addEventListener('mouseenter', () => showTooltip(link));
        link.addEventListener('focus', () => showTooltip(link));
        link.addEventListener('mouseleave', hideTooltip);
        link.addEventListener('blur', hideTooltip);
        link.addEventListener('click', hideTooltip);
    });

    document.querySelector('.sidebar-scroll')?.addEventListener('scroll', () => {
        if (!activeLink) {
            return;
        }

        if (!canShow()) {
            hideTooltip();
            return;
        }

        positionTooltip(activeLink);
    }, { passive: true });

    window.addEventListener('resize', () => {
        if (!activeLink) {
            return;
        }

        if (!canShow()) {
            hideTooltip();
            return;
        }

        positionTooltip(activeLink);
    });

    window.addEventListener('scroll', () => {
        if (!activeLink) {
            return;
        }

        if (!canShow()) {
            hideTooltip();
            return;
        }

        positionTooltip(activeLink);
    }, { passive: true });

    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape') {
            hideTooltip();
        }
    });
}

function initializeSidebar() {
    const isDesktopAtInit = desktopSidebarQuery.matches;
    const storedCollapsed = window.localStorage.getItem(SIDEBAR_COLLAPSE_KEY) === 'true';
    document.documentElement.dataset.sidebarCollapsed = isDesktopAtInit && storedCollapsed ? 'true' : 'false';
    document.documentElement.dataset.sidebarOpen = 'false';

    const sectionState = getStoredSectionState();

    document.querySelectorAll('[data-nav-section]').forEach((section) => {
        const sectionId = section.dataset.sectionId;
        const active = section.dataset.activeSection === 'true';
        const defaultOpen = section.dataset.defaultOpen === 'true';
        const expanded = isDesktopAtInit
            ? (typeof sectionState[sectionId] === 'boolean' ? sectionState[sectionId] : (active || defaultOpen))
            : false;
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
        if (!desktopSidebarQuery.matches) {
            setSidebarCollapsed(false);
        }
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

    initializeSidebarHoverTooltips();
}

function initializeMobileTopbarAutoFold() {
    const topbar = document.querySelector('.app-topbar');
    if (!topbar) {
        return;
    }

    const mobileTopbarQuery = window.matchMedia('(max-width: 1023px)');
    const appMain = document.querySelector('.app-main');
    const getScrollY = () => {
        const windowY = window.scrollY || 0;
        const containerY = appMain instanceof HTMLElement ? appMain.scrollTop : 0;

        return Math.max(windowY, containerY);
    };

    let lastScrollY = getScrollY();
    let ticking = false;
    let isCompact = false;

    const setCompact = (compact) => {
        if (isCompact === compact) {
            return;
        }

        isCompact = compact;
        document.documentElement.dataset.topbarCompact = compact ? 'true' : 'false';
    };

    const evaluateState = () => {
        const currentY = getScrollY();
        const delta = currentY - lastScrollY;

        if (!mobileTopbarQuery.matches) {
            setCompact(false);
            lastScrollY = currentY;
            ticking = false;
            return;
        }

        if (document.documentElement.dataset.sidebarOpen === 'true') {
            setCompact(false);
            lastScrollY = currentY;
            ticking = false;
            return;
        }

        if (currentY <= 24) {
            setCompact(false);
        } else if (delta > 6 && currentY > 72) {
            setCompact(true);
        } else if (delta < -6) {
            setCompact(false);
        }

        lastScrollY = currentY;
        ticking = false;
    };

    const onScroll = () => {
        if (ticking) {
            return;
        }

        ticking = true;
        window.requestAnimationFrame(evaluateState);
    };

    const handleViewportChange = () => {
        lastScrollY = getScrollY();
        evaluateState();
    };

    window.addEventListener('scroll', onScroll, { passive: true });
    if (appMain instanceof HTMLElement) {
        appMain.addEventListener('scroll', onScroll, { passive: true });
    }
    mobileTopbarQuery.addEventListener('change', handleViewportChange);

    document.addEventListener('visibilitychange', () => {
        if (!document.hidden) {
            handleViewportChange();
        }
    });

    handleViewportChange();
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
        initializeMobileTopbarAutoFold();
        initializeToasts();
        initializeRipple();
        initializeConfirmForms();
    }, { once: true });
} else {
    initializeTheme();
    initializeSidebar();
    initializeMobileTopbarAutoFold();
    initializeToasts();
    initializeRipple();
    initializeConfirmForms();
}
