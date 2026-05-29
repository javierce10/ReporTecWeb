// ===========================
// ReporTec - JS Global
// ===========================

// ---- TOAST ----
function showToast(msg, type = 'success') {
    let t = document.getElementById('toast');
    if (!t) {
        t = document.createElement('div');
        t.id = 'toast';
        document.body.appendChild(t);
    }
    t.className = type;
    t.innerHTML = `<i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-circle'}"></i> ${msg}`;
    t.classList.add('show');
    setTimeout(() => t.classList.remove('show'), 3200);
}

// ---- SIDEBAR MÓVIL ----
function toggleSidebar() {
    const sb  = document.querySelector('.sidebar');
    const ov  = document.querySelector('.sidebar-overlay');
    sb.classList.toggle('open');
    if (ov) ov.classList.toggle('open');
}

// ---- MODAL ----
function openModal(id) { document.getElementById(id).classList.add('open'); }
function closeModal(id) { document.getElementById(id).classList.remove('open'); }

// Cerrar modal al hacer click fuera
document.addEventListener('click', e => {
    if (e.target.classList.contains('modal-overlay')) {
        e.target.classList.remove('open');
    }
});

// ---- CONFIRMAR ----
function confirmar(msg, callback) {
    if (confirm(msg)) callback();
}

// ---- FETCH HELPER ----
async function apiPost(url, data) {
    const fd = new FormData();
    for (const k in data) fd.append(k, data[k]);
    const res = await fetch(url, { method: 'POST', body: fd });
    return res.json();
}

async function apiGet(url) {
    const res = await fetch(url);
    return res.json();
}

// ---- BUSQUEDA EN TABLA ----
function initSearch(inputId, tableId) {
    const input = document.getElementById(inputId);
    if (!input) return;
    input.addEventListener('input', () => {
        const q = input.value.toLowerCase();
        const rows = document.querySelectorAll(`#${tableId} tbody tr`);
        rows.forEach(row => {
            row.style.display = row.textContent.toLowerCase().includes(q) ? '' : 'none';
        });
    });
}

// ---- BADGE DE ESTADO ----
function badgeEstado(estado) {
    const map = {
        'pendiente':  ['badge-pending',  'fa-hourglass-half', 'Pendiente'],
        'en proceso': ['badge-process',  'fa-sync-alt',       'En proceso'],
        'resuelto':   ['badge-resolved', 'fa-check-circle',   'Resuelto'],
        'rechazado':  ['badge-rejected', 'fa-times-circle',   'Rechazado'],
    };
    const [cls, ico, label] = map[estado?.toLowerCase()] || ['badge-pending','fa-question','Desconocido'];
    return `<span class="badge ${cls}"><i class="fas ${ico}"></i> ${label}</span>`;
}

// ---- FECHA LEGIBLE ----
function fechaLegible(str) {
    if (!str) return '-';
    const d = new Date(str);
    return d.toLocaleDateString('es-MX', { day:'2-digit', month:'short', year:'numeric', hour:'2-digit', minute:'2-digit' });
}
