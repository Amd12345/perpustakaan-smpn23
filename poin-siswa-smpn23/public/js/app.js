// Perilaku kecil yang dipakai di seluruh aplikasi.
(() => {
    'use strict';

    const reduced = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

    // ---------- Toast notifikasi ----------
    document.querySelectorAll('[data-toast]').forEach((toast) => {
        const dismiss = () => {
            toast.classList.add('is-hiding');
            const done = () => toast.remove();
            toast.addEventListener('animationend', done, { once: true });
            setTimeout(done, 400);
        };

        toast.querySelector('[data-toast-close]')?.addEventListener('click', dismiss);
        const timer = window.setTimeout(dismiss, 6000);

        toast.addEventListener('mouseenter', () => window.clearTimeout(timer), { once: true });
    });

    // ---------- Kunci konfirmasi (SweetAlert) ----------
    document.addEventListener('click', (event) => {
        const trigger = event.target.closest('[data-confirm]');
        if (!trigger) return;

        event.preventDefault();

        const proceed = () => {
            const form = trigger.closest('form');
            if (form) {
                if (form.requestSubmit) {
                    form.requestSubmit(trigger.tagName === 'BUTTON' ? trigger : undefined);
                } else {
                    form.submit();
                }
            } else if (trigger.tagName === 'A' && trigger.getAttribute('href')) {
                window.location.href = trigger.getAttribute('href');
            }
        };

        if (!window.Swal) {
            if (window.confirm(trigger.dataset.confirm || 'Lanjutkan?')) proceed();
            return;
        }

        window.Swal.fire({
            title: trigger.dataset.confirmTitle || 'Yakin ingin melanjutkan?',
            text: trigger.dataset.confirm || '',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: trigger.dataset.confirmOk || 'Ya, lanjutkan',
            cancelButtonText: 'Batal',
            focusCancel: true,
        }).then((result) => {
            if (result.isConfirmed) proceed();
        });
    });

    // ---------- Cegah kirim ganda, beri umpan balik di tombol ----------
    document.querySelectorAll('form[method="post"], form[method="POST"]').forEach((form) => {
        form.addEventListener('submit', () => {
            const button = form.querySelector('button[type="submit"], input[type="submit"]');
            if (!button || button.classList.contains('is-loading')) return;
            button.classList.add('is-loading');
            button.setAttribute('aria-busy', 'true');
            const icon = button.querySelector('i');
            if (icon) icon.className = 'bi bi-arrow-repeat';
        });
    });

    // ---------- Grafik: gaya global ----------
    if (window.Chart) {
        window.Chart.defaults.font.family = "'Plus Jakarta Sans', system-ui, sans-serif";
        window.Chart.defaults.font.size = 11.5;
        window.Chart.defaults.color = '#5d6f81';
        window.Chart.defaults.borderColor = '#e2e8f0';
    }

    // ---------- Tutup menu ponsel setelah menekan tautan ----------
    document.querySelectorAll('.drawer .rail-link').forEach((link) => {
        link.addEventListener('click', () => {
            const drawer = link.closest('.offcanvas');
            if (drawer && window.bootstrap) {
                window.bootstrap.Offcanvas.getOrCreateInstance(drawer).hide();
            }
        });
    });

    // ---------- Animasi tekan tombol tidak dipakai saat reduced motion ----------
    if (reduced) {
        document.documentElement.classList.add('reduced-motion');
    }
})();
