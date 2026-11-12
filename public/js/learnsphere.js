(() => {
    const sidebar = document.getElementById('appSidebar');
    const backdrop = document.getElementById('sidebarBackdrop');
    const collapseBtn = document.getElementById('sidebarCollapseBtn');

    window.toggleSidebar = function toggleSidebar() {
        sidebar?.classList.toggle('show');
        backdrop?.classList.toggle('show');
    };

    collapseBtn?.addEventListener('click', () => {
        sidebar?.classList.toggle('collapsed');
        localStorage.setItem('ls-sidebar-collapsed', sidebar.classList.contains('collapsed') ? '1' : '0');
    });

    if (sidebar && localStorage.getItem('ls-sidebar-collapsed') === '1' && window.innerWidth >= 992) {
        sidebar.classList.add('collapsed');
    }

    // Animate progress bars
    document.querySelectorAll('.ls-progress[data-value]').forEach((el) => {
        const value = Number(el.getAttribute('data-value') || 0);
        requestAnimationFrame(() => {
            const bar = el.querySelector('span');
            if (bar) bar.style.width = `${Math.max(0, Math.min(100, value))}%`;
        });
    });

    // Tooltips
    if (window.bootstrap?.Tooltip) {
        document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach((el) => {
            new bootstrap.Tooltip(el);
        });
    }

    // Auto-dismiss alerts softly
    document.querySelectorAll('.alert-dismissible').forEach((alert) => {
        setTimeout(() => {
            alert.classList.add('fade');
            alert.classList.remove('show');
            setTimeout(() => alert.remove(), 400);
        }, 5000);
    });

    // Demo account click-to-fill
    document.querySelectorAll('[data-demo-email]').forEach((row) => {
        row.addEventListener('click', () => {
            const email = row.getAttribute('data-demo-email');
            const emailInput = document.querySelector('input[name="email"]');
            const passwordInput = document.querySelector('input[name="password"]');
            if (emailInput) emailInput.value = email;
            if (passwordInput) passwordInput.value = 'password';
            emailInput?.focus();
            row.classList.add('table-success');
            setTimeout(() => row.classList.remove('table-success'), 700);
        });
    });

    // Live filter for tables/lists marked with data-ls-filter
    const filterInput = document.querySelector('[data-ls-filter-input]');
    if (filterInput) {
        filterInput.addEventListener('input', () => {
            const q = filterInput.value.trim().toLowerCase();
            document.querySelectorAll('[data-ls-filter-item]').forEach((item) => {
                const hay = (item.getAttribute('data-ls-filter-item') || item.textContent || '').toLowerCase();
                item.classList.toggle('d-none', q !== '' && !hay.includes(q));
            });
        });
    }

    // Confirm destructive buttons
    document.querySelectorAll('[data-ls-confirm]').forEach((btn) => {
        btn.addEventListener('click', (e) => {
            const message = btn.getAttribute('data-ls-confirm') || 'Are you sure?';
            if (!window.confirm(message)) {
                e.preventDefault();
            }
        });
    });
})();
