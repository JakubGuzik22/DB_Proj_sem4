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
                url = '/templates/settings.php';
                fetch(url)
                    .then(res => res.text())
                    .then(html => {
                        document.getElementById('content').innerHTML = html;
                    })
                    .catch(() => showMessageBox('Błąd sieci: Nie można załadować ustawień profilu.'));
                break;

            case 'addresses':
                url = '/templates/addresses.php';
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
                .catch(() => alert("Błąd sieci: Rejestracja nie powiodła się"));
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
                        .catch(() => alert("Błąd sieci: Reset hasła nie powiodł się"));
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
                            window.location.href = '/';
                        } else {
                            alert(response);
                        }
                    })
                    .catch(() => alert("Błąd sieci: Logowanie nie powiodło się"));
                });
            }
        }
        if (type === 'form_change_settings') {
            const changeSettingsForm = document.getElementById('changeSettingsForm');
            if (changeSettingsForm) {
                changeSettingsForm.addEventListener('submit', function(e) {
                    e.preventDefault();
                    const formData = new FormData(changeSettingsForm);
                    fetch('PHP/update_profile.php', {
                        method: 'POST',
                        body: formData
                    })
                    .then(res => res.text())
                    .then(response => {
                        if (response.trim() === 'OK') {
                            alert('Dane zaktualizowane pomyślnie!');
                            loadForm('settings')
                        } else {
                            alert(response);
                        }
                    })
                    .catch(() => showMessageBox("Błąd sieci: Aktualizacja profilu nieudana."));
                });
            }
            const cancelButton = changeSettingsForm.querySelector('.cancel-btn');
            if (cancelButton) {
                cancelButton.addEventListener('click', () => {
                    loadForm('settings');
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
function loadAddressForm(adresId = null) {
    const url = adresId ? `templates/form_change_address.php?adres_id=${adresId}` : 'templates/form_change_address.php';
    fetch(url)
        .then(res => res.text())
        .then(html => {
            document.getElementById('content').innerHTML = html;
            if (adresId) initEditAddressForm(adresId);
            else initAddAddressForm();
        })
        .catch(() => alert('Błąd sieci: Nie udało się załadować formularza adresu.'));
}
function initEditAddressForm(adresId) {
    const form = document.getElementById('changeAddressForm');
    if (!form) return;
    form.addEventListener('submit', e => {
        e.preventDefault();
        const formData = new FormData(form);
        formData.append('adres_id', adresId);
        fetch('PHP/save_address.php', {
            method: 'POST',
            body: formData
        })
            .then(res => res.text())
            .then(response => {
                if (response.trim() === 'OK') {
                    alert('Adres zaktualizowany pomyślnie!');
                    loadForm('addresses');
                } else alert(response);
            })
            .catch(() => alert('Błąd sieci: Aktualizacja adresu nie powiodła się.'));
    });
}
function initAddAddressForm() {
    const form = document.getElementById('changeAddressForm');
    if (!form) return;
    form.addEventListener('submit', e => {
        e.preventDefault();
        const formData = new FormData(form);
        fetch('PHP/save_address.php', {
            method: 'POST',
            body: formData
        })
            .then(res => res.text())
            .then(response => {
                if (response.trim() === 'OK') {
                    alert('Adres dodany pomyślnie!');
                    loadForm('addresses');
                } else alert(response);
            })
            .catch(() => alert('Błąd sieci: Dodanie adresu nie powiodło się.'));
    });
}

document.addEventListener('click', e => {
    if (e.target.classList.contains('btn-edit')) {
        const adresId = e.target.getAttribute('data-adresid');
        loadAddressForm(adresId);
    }
});