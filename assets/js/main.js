// Archivo global ACADEMIX
window.APP = (function () {

    const APP_NAME = "AcademiX";

    // Log general
    function log(msg, data = null) {
        if (data) {
            console.log(`[${APP_NAME}] ${msg}`, data);
        } else {
            console.log(`[${APP_NAME}] ${msg}`);
        }
    }

    // Formato DD/MM/YYYY
    function formatFecha(fechaStr) {
        if (!fechaStr) return "";
        const d = new Date(fechaStr);
        if (isNaN(d.getTime())) return fechaStr;
        return `${String(d.getDate()).padStart(2, "0")}/${String(d.getMonth() + 1).padStart(2, "0")}/${d.getFullYear()}`;
    }

    // Crear contenedor de toasts
    function ensureToastContainer() {
        let container = document.getElementById("app-toast-container");
        if (!container) {
            container = document.createElement("div");
            container.id = "app-toast-container";
            document.body.appendChild(container);
        }
        return container;
    }

    // Mostrar toast
    function showToast(message, type = "info", timeout = 4000) {
        const container = ensureToastContainer();

        const toast = document.createElement("div");
        toast.classList.add("app-toast", `app-toast-${type}`);
        toast.innerHTML = `
            <span class="app-toast-message">${message}</span>
            <button class="app-toast-close">&times;</button>
        `;

        container.appendChild(toast);

        // Cerrar manual
        toast.querySelector(".app-toast-close").addEventListener("click", () => {
            hideToast(toast);
        });

        // Auto-cerrar
        setTimeout(() => hideToast(toast), timeout);
    }

    function hideToast(toast) {
        toast.classList.add("app-toast-hide");
        setTimeout(() => toast.remove(), 300);
    }

    // Confirmación
    function confirmAction(msg, cb) {
        if (window.confirm(msg) && typeof cb === "function") {
            cb();
        }
    }

    // Activar ícono en sidebar
    function marcarSidebarActiva() {
        const links = document.querySelectorAll(".sidebar-icon[data-href]");
        if (!links.length) return;

        const path = window.location.pathname;

        links.forEach(link => {
            const destino = link.getAttribute("data-href");
            if (path.includes(destino)) {
                link.classList.add("active");
            }
        });
    }

    // Inicializar
    function init() {
        log("Scripts globales listos");
        marcarSidebarActiva();
    }

    return {
        init,
        log,
        formatFecha,
        showToast,
        confirmAction
    };

})();

// Inicialización global
document.addEventListener("DOMContentLoaded", () => {
    if (window.APP && APP.init) APP.init();
});

/* ===============================
   Buscador en tiempo real
   =============================== */
function iniciarBuscadorEnTiempoReal(selectorInput, selectorTabla) {
    const input = document.querySelector(selectorInput);
    const filas = document.querySelectorAll(selectorTabla + " tbody tr");

    if (!input || !filas.length) return;

    input.addEventListener("keyup", function () {
        const texto = this.value.toLowerCase();

        filas.forEach(fila => {
            const contenido = fila.textContent.toLowerCase();
            fila.style.display = contenido.includes(texto) ? "" : "none";
        });
    });
}
