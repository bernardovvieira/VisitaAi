import 'leaflet/dist/leaflet.css';
import L from 'leaflet';
import html2canvas from 'html2canvas';

function showMapMessage(container, text) {
    container.replaceChildren();
    const wrap = document.createElement('div');
    wrap.className =
        'flex h-full min-h-[16rem] items-center justify-center px-4 text-center text-sm text-slate-500 dark:text-slate-400';
    wrap.textContent = text;
    container.appendChild(wrap);
}

function initMap(cfg) {
    const mapEl = document.getElementById('mapa-local');
    if (!mapEl || !cfg.map) return;

    const { lat, lng, popup } = cfg.map;
    const { noCoord, mapError } = cfg.i18n;

    const latN = lat != null ? Number(lat) : NaN;
    const lngN = lng != null ? Number(lng) : NaN;
    const invalid =
        !Number.isFinite(latN) ||
        !Number.isFinite(lngN) ||
        (Math.abs(latN) < 1e-9 && Math.abs(lngN) < 1e-9);

    if (invalid) {
        showMapMessage(mapEl, noCoord);
        return;
    }

    try {
        mapEl.replaceChildren();
        const map = L.map(mapEl, {
            zoomControl: true,
            attributionControl: true,
        }).setView([latN, lngN], 16);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; OpenStreetMap contributors',
            maxZoom: 19,
        }).addTo(map);

        const pinSvg =
            '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="28" height="40" aria-hidden="true"><path fill="#2563eb" stroke="#fff" stroke-width="1.5" d="M12 0C7.31 0 3.5 3.81 3.5 8.5c0 5.25 8.5 15.5 8.5 15.5s8.5-10.25 8.5-15.5C20.5 3.81 16.69 0 12 0z"/><circle fill="#fff" cx="12" cy="8.5" r="2.8"/></svg>';
        const pinIcon = L.divIcon({
            className: 'consulta-leaflet-pin',
            html: pinSvg,
            iconSize: [28, 40],
            iconAnchor: [14, 40],
            popupAnchor: [0, -40],
        });

        const marker = L.marker([latN, lngN], { icon: pinIcon }).addTo(map);
        if (popup) {
            marker.bindPopup(popup, { maxWidth: 280 }).openPopup();
        }

        requestAnimationFrame(() => map.invalidateSize());
        window.addEventListener(
            'resize',
            () => {
                try {
                    map.invalidateSize();
                } catch {
                    /* ignore */
                }
            },
            { passive: true },
        );
    } catch {
        showMapMessage(mapEl, mapError);
    }
}

function initDownload(cfg) {
    const btn = document.getElementById('btn-baixar-card');
    const adesivo = document.getElementById('adesivo');
    if (!btn || !adesivo) return;

    const { downloadFail } = cfg.i18n;
    const fileBase = String(cfg.codigoUnico || 'imovel').replace(/[^\w-]/g, '');

    btn.addEventListener('click', async () => {
        btn.disabled = true;
        btn.setAttribute('aria-busy', 'true');
        try {
            const canvas = await html2canvas(adesivo, {
                scale: 2,
                useCORS: true,
                logging: false,
                backgroundColor: '#ffffff',
            });
            const link = document.createElement('a');
            link.download = `adesivo_qrcode_visita_ai_${fileBase}.png`;
            link.href = canvas.toDataURL('image/png');
            link.click();
        } catch {
            window.alert(downloadFail);
        } finally {
            btn.disabled = false;
            btn.removeAttribute('aria-busy');
        }
    });
}

function initShare(cfg) {
    const btn = document.getElementById('btn-compartilhar');
    const span = document.getElementById('btn-copiar-texto');
    if (!btn) return;

    const url = btn.getAttribute('data-url') || window.location.href;
    const title = btn.getAttribute('data-title') || document.title;
    const {
        labelShare,
        labelCopied,
        labelCopyError,
        labelShared,
        textShare,
    } = cfg.i18n;

    const original = span ? span.textContent : labelShare;

    function feedbackCopied() {
        if (!span) return;
        span.textContent = labelCopied;
        span.classList.add('text-blue-600', 'dark:text-blue-400');
        setTimeout(() => {
            span.textContent = original;
            span.classList.remove('text-blue-600', 'dark:text-blue-400');
        }, 2000);
    }

    async function copyUrl() {
        try {
            if (navigator.clipboard?.writeText) {
                await navigator.clipboard.writeText(url);
            } else {
                const ta = document.createElement('textarea');
                ta.value = url;
                ta.style.position = 'fixed';
                ta.style.opacity = '0';
                document.body.appendChild(ta);
                ta.select();
                document.execCommand('copy');
                document.body.removeChild(ta);
            }
            feedbackCopied();
        } catch {
            if (span) span.textContent = labelCopyError;
        }
    }

    btn.addEventListener('click', async () => {
        if (navigator.share) {
            try {
                await navigator.share({ title, text: textShare, url });
                if (span) {
                    span.textContent = labelShared;
                    span.classList.add('text-blue-600', 'dark:text-blue-400');
                    setTimeout(() => {
                        span.textContent = original;
                        span.classList.remove('text-blue-600', 'dark:text-blue-400');
                    }, 2000);
                }
            } catch (err) {
                if (err?.name !== 'AbortError') await copyUrl();
            }
        } else {
            await copyUrl();
        }
    });
}

function boot() {
    const el = document.getElementById('consulta-publica-config');
    if (!el) return;
    let cfg;
    try {
        cfg = JSON.parse(el.textContent);
    } catch {
        return;
    }
    initMap(cfg);
    initDownload(cfg);
    initShare(cfg);
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', boot);
} else {
    boot();
}
