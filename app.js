function loadHeader() {
    fetch('templates/header.html')
        .then(res => res.text())
        .then(html => {
            document.getElementById("header").innerHTML = html;
        });
}
function loadForm(type) {
    fetch(`templates/${type}.html`)
        .then(res => res.text())
        .then(html => {
            document.getElementById("content").innerHTML = html;
            if (type === 'form_register') {
                const registerForm = document.getElementById('registerForm');
                if (registerForm) {
                    registerForm.addEventListener('submit', function (e) {
                        e.preventDefault();
                        if (registerForm.checkValidity()) {
                            loadForm('login');
                        } else {
                            registerForm.reportValidity();
                        }
                    });
                }
            }
            if (type === 'form_passreset') {
                const resetForm = document.getElementById('resetForm');
                if (resetForm) {
                    resetForm.addEventListener('submit', function (e) {
                        e.preventDefault();
                        if (resetForm.checkValidity()) {
                            loadForm('dialog');
                        } else {
                            resetForm.reportValidity();
                        }
                    });
                }
            }
        });
}

document.addEventListener("DOMContentLoaded", () => {
    loadHeader();
    loadForm('form_login');
});