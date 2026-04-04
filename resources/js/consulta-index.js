function spinnerWithLabel(label) {
    const wrap = document.createElement('span');
    wrap.className = 'inline-flex items-center';
    const svg = document.createElementNS('http://www.w3.org/2000/svg', 'svg');
    svg.setAttribute('class', 'animate-spin -ml-1 mr-2 h-4 w-4');
    svg.setAttribute('xmlns', 'http://www.w3.org/2000/svg');
    svg.setAttribute('fill', 'none');
    svg.setAttribute('viewBox', '0 0 24 24');
    svg.setAttribute('aria-hidden', 'true');
    const c1 = document.createElementNS('http://www.w3.org/2000/svg', 'circle');
    c1.setAttribute('class', 'opacity-25');
    c1.setAttribute('cx', '12');
    c1.setAttribute('cy', '12');
    c1.setAttribute('r', '10');
    c1.setAttribute('stroke', 'currentColor');
    c1.setAttribute('stroke-width', '4');
    const c2 = document.createElementNS('http://www.w3.org/2000/svg', 'path');
    c2.setAttribute('class', 'opacity-75');
    c2.setAttribute('fill', 'currentColor');
    c2.setAttribute(
        'd',
        'M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z',
    );
    svg.appendChild(c1);
    svg.appendChild(c2);
    wrap.appendChild(svg);
    wrap.appendChild(document.createTextNode(label));
    return wrap;
}

function boot() {
    const form = document.getElementById('consulta-codigo-form');
    const btn = document.getElementById('consulta-codigo-btn');
    const input = document.getElementById('codigo');
    let searching = '…';

    const cfgEl = document.getElementById('consulta-index-config');
    if (cfgEl) {
        try {
            const cfg = JSON.parse(cfgEl.textContent);
            if (cfg.searching) searching = cfg.searching;
        } catch {
            /* ignore */
        }
    }

    if (input) {
        input.addEventListener('input', () => {
            input.value = input.value.replace(/\D/g, '').slice(0, 8);
        });
        input.addEventListener('paste', (e) => {
            e.preventDefault();
            const t = (e.clipboardData?.getData('text') ?? '').replace(/\D/g, '').slice(0, 8);
            input.value = t;
        });
    }

    if (form && btn) {
        form.addEventListener('submit', () => {
            btn.disabled = true;
            btn.setAttribute('aria-busy', 'true');
            btn.replaceChildren(spinnerWithLabel(searching));
        });
    }
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', boot);
} else {
    boot();
}
