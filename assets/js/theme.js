// Modo oscuro/claro
document.addEventListener("DOMContentLoaded", () => {

    const toggleBtn = document.getElementById("themeToggle");
    if (!toggleBtn) return; 

    const body = document.body;
    const icon = toggleBtn.querySelector("i");

    // Cargar preferencia guardada
    const savedTheme = localStorage.getItem("theme");

    if (savedTheme === "dark") {
        body.classList.add("dark-mode");
        icon.classList.replace("bi-moon-fill", "bi-sun-fill");
    }

    toggleBtn.addEventListener("click", () => {
        body.classList.toggle("dark-mode");

        const isDark = body.classList.contains("dark-mode");

        // Cambiar icono
        if (isDark) {
            icon.classList.replace("bi-moon-fill", "bi-sun-fill");
        } else {
            icon.classList.replace("bi-sun-fill", "bi-moon-fill");
        }

        // Guardar preferencia
        localStorage.setItem("theme", isDark ? "dark" : "light");
    });

});
