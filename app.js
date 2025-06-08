function loadHeader() {
  fetch('templates/header.php')
    .then(res => res.text())
    .then(html => {
        document.getElementById("header").innerHTML = html;
        initUserActionListener();
    });
}
function initUserActionListener() {
    const userSelect = document.querySelector('select[name="user_action"]');
    if (!userSelect) return;

    userSelect.addEventListener('change', () => {
        const action = userSelect.value;
        let url = '';

        switch (action) {
            case 'logout':
                url = '/PHP/logout.php';
                options = { method: 'POST' };
                fetch(url, options)
                    .then(res => res.text())
                    .then(() => {
                        window.location.href = '/';
                    })
                    .catch(() => alert('Błąd sieci'));
                break;

            case 'settings':
                url = '/PHP/settings.php';
                fetch(url)
                    .then(res => res.text())
                    .then(html => {
                        document.getElementById('content').innerHTML = html;
                    })
                    .catch(() => alert('Błąd sieci'));
                break;

            case 'addresses':
                url = '/PHP/addresses.php';
                fetch(url)
                    .then(res => res.text())
                    .then(html => {
                        document.getElementById('content').innerHTML = html;
                    })
                    .catch(() => alert('Błąd sieci'));
                break;

            default:
                break;
        }
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
                .then(res => res.text())
                .then(response => {
                    if (response.trim() === 'OK') {
                        loadForm('dialog_register_succesful');
                    } else {
                        alert(response);
                    }
                })
                .catch(() => alert("Błąd sieci"));
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
        if (type === 'form_login') {
            const form = document.getElementById('loginForm');
            if (form) {
                form.addEventListener('submit', function (e) {
                    e.preventDefault();
                    const formData = new FormData(form);
                    fetch('/PHP/login.php', {
                        method: 'POST',
                        body: formData
                    })
                    .then(res => res.text())
                    .then(response => {
                        if (response.trim() === 'OK') {
                            window.location.href = 'home.php';
                        } else {
                            alert("Błędny email lub hasło");
                        }
                    })
                    .catch(() => alert("Błąd sieci"));
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
});
