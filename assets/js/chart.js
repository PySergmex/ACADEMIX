/* =======================================
   DASHBOARD ADMIN – JS CENTRALIZADO
======================================= */

// -------------------------
// Animación de contadores
// -------------------------
export function iniciarContadores() {
    const counters = document.querySelectorAll(".stat-number");

    counters.forEach(counter => {
        const target = parseInt(counter.getAttribute("data-target"), 10) || 0;
        let current = 0;
        const duration = 700;
        const steps = 40;
        const increment = target / steps;
        const interval = duration / steps;

        const timer = setInterval(() => {
            current += increment;
            if (current >= target) {
                current = target;
                clearInterval(timer);
            }
            counter.textContent = Math.floor(current);
        }, interval);
    });
}

// -------------------------
// Donut de Estatus
// -------------------------
export function cargarGraficaEstatus(canvasId, labels, data) {
    const ctx = document.getElementById(canvasId);

    if (!ctx) return;

    new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: labels,
            datasets: [{
                data: data,
                backgroundColor: [
                    '#5146D9',
                    '#6FA8FF',
                    '#FFB48A',
                    '#FF9BBF',
                    '#1A1C2D'
                ]
            }]
        },
        options: {
            responsive: true,
            animation: {
                duration: 900,
                easing: 'easeOutCubic'
            },
            plugins: {
                legend: { position: 'bottom' }
            }
        }
    });
}

// -------------------------
// Gráfica de Barras
// -------------------------
export function cargarGraficaPromedios(canvasId, labels, data) {
    const ctx = document.getElementById(canvasId);

    if (!ctx) return;

    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [{
                label: "Promedio general",
                data: data,
                backgroundColor: "#5146D9"
            }]
        },
        options: {
            responsive: true,
            animation: {
                duration: 900,
                easing: 'easeOutCubic'
            },
            scales: {
                y: {
                    beginAtZero: true,
                    suggestedMax: 10
                }
            }
        }
    });
}
