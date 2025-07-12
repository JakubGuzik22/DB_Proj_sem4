function loadHeader() {
  fetch('templates/header.php')
    .then(res => res.text())
    .then(html => {
        document.getElementById("header").innerHTML = html;
        initUserActionListener();
        initNavLinks();
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
                    .catch(() => showMessageBox('Nie można załadować ustawień profilu.'));
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

        if (type === 'formRegister') {
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
                        loadForm('dialogRegisterSuccesful');
                    } else {
                        alert(response);
                    }
                })
                .catch(() => alert("Rejestracja nie powiodła się"));
            });
        }

        if (type === 'formPassreset') {
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
                                loadForm('dialogPassresetSuccesful');
                            } else {
                                alert("Błąd resetowania hasła");
                            }
                        })
                        .catch(() => alert("Reset hasła nie powiodł się"));
                    } else {
                        form.reportValidity();
                    }
                });
            }
        }

        if (type === 'formLogin') {
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
                    .catch(() => alert("Logowanie nie powiodło się"));
                });
            }
        }

        if (type === 'formChangeSettings') {
            const changeSettingsForm = document.getElementById('changeSettingsForm');
            if (changeSettingsForm) {
                changeSettingsForm.addEventListener('submit', function(e) {
                    e.preventDefault();
                    const formData = new FormData(changeSettingsForm);
                    fetch('/PHP/updateProfile.php', {
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
                    .catch(() => showMessageBox("Aktualizacja profilu nieudana."));
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
    loadForm('formLogin');
}

document.addEventListener("DOMContentLoaded", () => {
    loadHeader();
});

function loadAddressForm(adresId = null) {
    const url = adresId ? `templates/formChangeAddress.php?adres_id=${adresId}` : 'templates/formChangeAddress.php';
    fetch(url)
        .then(res => res.text())
        .then(html => {
            document.getElementById('content').innerHTML = html;
            if (adresId) initEditAddressForm(adresId);
            else initAddAddressForm();
        })
        .catch(() => alert('Nie udało się załadować formularza adresu.'));
}

function initEditAddressForm(adresId) {
    const form = document.getElementById('changeAddressForm');
    if (!form) return;
    form.addEventListener('submit', e => {
        e.preventDefault();
        const formData = new FormData(form);
        formData.append('adres_id', adresId);
        fetch('PHP/saveAddress.php', {
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
            .catch(() => alert('Aktualizacja adresu nie powiodła się.'));
    });
}

function initAddAddressForm() {
    const form = document.getElementById('changeAddressForm');
    if (!form) return;
    form.addEventListener('submit', e => {
        e.preventDefault();
        const formData = new FormData(form);
        fetch('PHP/saveAddress.php', {
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
            .catch(() => alert('Dodanie adresu nie powiodło się.'));
    });
}

document.addEventListener('click', e => {
    if (e.target.classList.contains('btn-edit')) {
        const adresId = e.target.getAttribute('data-adresid');
        loadAddressForm(adresId);
    }
    if (e.target.classList.contains('btn-delete')) {
        const adresId = e.target.getAttribute('data-adresid');
        if (confirm('Czy na pewno chcesz usunąć ten adres?')) {
            const formData = new FormData();
            formData.append('adres_id', adresId);

            fetch('PHP/deleteAddress.php', {
                method: 'POST',
                body: formData
            })
            .then(res => res.text())
            .then(response => {
                if (response.trim() === 'OK') {
                    alert('Adres został usunięty.');
                    loadForm('addresses');
                } else {
                    alert('Błąd: ' + response);
                }
            })
            .catch(() => alert('Nie udało się usunąć adresu.'));
        }
    }
});

function initNavLinks() {
    document.querySelectorAll('.nav a[data-view]').forEach(link => {
        link.addEventListener('click', e => {
            e.preventDefault();
            const view = link.getAttribute('data-view');
            loadView(view);
        });
    });
}

function loadView(view) {
    fetch(`templates/${view}.php`)
        .then(res => res.text())
        .then(html => {
            document.getElementById('content').innerHTML = html;
            if (view === 'formPackage') {
                const packageForm = document.getElementById('packageForm');
                packageForm.addEventListener('submit', function (e) {
                    e.preventDefault();
                    const formData = new FormData(packageForm);
                    fetch('/PHP/sendPackage.php', {
                        method: 'POST',
                        body: formData
                    })
                    .then(res => res.text())
                    .then(response => {
                        if (response.trim() === 'OK') {
                            alert("Nadano paczkę.");
                            loadView('myPackages');
                        } else {
                            alert(response);
                        }
                    })
                    .catch(() => alert("Nadanie paczki nie powiodło się"));
                });
            }
        })
        .catch(() => alert('Nie udało się załadować widoku.'));
}
