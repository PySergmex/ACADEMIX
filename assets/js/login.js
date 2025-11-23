// Manejo del loader al enviar el formulario de login
document.addEventListener("DOMContentLoaded", function () {

    const loginForm = document.querySelector("form[action='includes/validar.php']");
    const loader = document.getElementById("loader-overlay");

    if (loginForm && loader) {
        loginForm.addEventListener("submit", function () {
            loader.classList.remove("d-none");
        });
    }

    // Animación del contenedor del formulario
    const formCard = document.querySelector(".form-card, .form-card1");
    if (formCard) {
        formCard.classList.add("fade-in");
    }

    // Tabs de Sign In / Sign Up con animación suave
    const tabs = document.querySelectorAll(".tab-btn");

    tabs.forEach(btn => {
        btn.addEventListener("click", function (event) {
            event.preventDefault();

            if (!formCard) return;

            formCard.style.opacity = "0";

            setTimeout(() => {
                if (btn.textContent.includes("Sign Up")) {
                    window.location.href = "sign_up.php";
                } else {
                    window.location.href = "index.php";
                }
            }, 150);
        });
    });

});
