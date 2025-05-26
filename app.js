function loadHeader() {
    fetch('templates/header.php')
        .then(res => res.text())
        .then(html => {
            document.getElementById("header").innerHTML = html;
        });
}

function loadForm(type) {
    fetch(`templates/${type}.php`)
        .then(res => res.text())
        .then(html => {
            document.getElementById("content").innerHTML = html;

            // if (type === 'form_register') {
            //     const registerForm = document.getElementById('registerForm');
            //     if (registerForm) {
            //         registerForm.addEventListener('submit', function (e) {
            //             e.preventDefault();

            //             if (!registerForm.checkValidity()) {
            //                 registerForm.reportValidity();
            //                 return;
            //             }

            //             const formData = new FormData(registerForm);

            //             fetch('templates/form_register.php', {
            //                 method: 'POST',
            //                 body: formData
            //             })
            //             .then(res => res.text())
            //             .then(response => {
            //                 console.log('Rejestracja:', response);
            //                 loadForm('form_login');
            //             })
            //             .catch(err => {
            //                 console.error('Błąd rejestracji:', err);
            //             });
            //         });
            //     }
            // }

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
