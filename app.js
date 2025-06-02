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
        if (type === 'form_register') {
            const registerForm = document.getElementById('registerForm');
            registerForm.addEventListener('submit', function (e) {
                e.preventDefault();
                const formData = new FormData(registerForm);
                fetch('PHP/register.php', {
                    method: 'POST',
                    body: formData
                })
                .then(res => {
                    if (res.ok) {
                        loadForm('dialog_register_succesful');
                    } else {
                        alert("Błąd rejestracji");
                    }
                })
                .catch(err => {
                    console.error("Błąd sieci:", err);
                });
            });
        }
        if (type === 'form_passreset') {
            const form = document.getElementById('passResetForm');
            if (form) {
                form.addEventListener('submit', function (e) {
                    e.preventDefault();
                    if (form.checkValidity()) {
                        const formData = new FormData(form);
                        fetch('/PHP/passreset.php', {
                            method: 'POST',
                            body: formData
                        })
                        .then(res => {
                            if (res.ok) {
                                loadForm('dialog_passreset_succesful');
                            } else {
                                alert("Błąd resetowania hasła");
                            }
                        })
                        .catch(() => alert("Błąd sieci"));
                    } else {
                        form.reportValidity();
                    }
                });
            }
        }
    });
}

function closeDialog() {
    document.getElementById("popup").style.display = "none";
    loadForm('form_login');
}

document.addEventListener("DOMContentLoaded", () => {
    loadHeader();
    // loadForm('form_login');
});
