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
        fetch(url, { method: 'POST' })
          .then(res => res.text())
          .then(() => window.location.href = '/')
          .catch(() => alert('Błąd sieci'));
        break;
      case 'settings':
        fetch('/templates/settings.php')
          .then(res => res.text())
          .then(html => document.getElementById('content').innerHTML = html)
          .catch(() => showMessageBox('Nie można załadować ustawień profilu.'));
        break;
      case 'addresses':
        fetch('/templates/addresses.php')
          .then(res => res.text())
          .then(html => document.getElementById('content').innerHTML = html)
          .catch(() => alert('Błąd sieci'));
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
        registerForm.addEventListener('submit', function(e) {
          e.preventDefault();
          const formData = new FormData(registerForm);
          fetch('PHP/register.php', { method: 'POST', body: formData })
            .then(res => res.text())
            .then(response => {
              if (response.trim() === 'OK') loadForm('dialogRegisterSuccesful');
              else alert(response);
            })
            .catch(() => alert("Rejestracja nie powiodła się"));
        });
      }

      if (type === 'formPassreset') {
        const form = document.getElementById('passResetForm');
        if (form) form.addEventListener('submit', function(e) {
          e.preventDefault();
          if (!form.checkValidity()) return form.reportValidity();
          const formData = new FormData(form);
          fetch('/PHP/passReset.php', { method: 'POST', body: formData })
            .then(res => {
              if (res.ok) loadForm('dialogPassresetSuccesful');
              else alert("Błąd resetowania hasła");
            })
            .catch(() => alert("Reset hasła nie powiódł się"));
        });
      }

      if (type === 'formLogin') {
        const form = document.getElementById('loginForm');
        if (form) form.addEventListener('submit', function(e) {
          e.preventDefault();
          const formData = new FormData(form);
          fetch('/PHP/login.php', { method: 'POST', body: formData })
            .then(res => res.text())
            .then(response => {
              if (response.trim() === 'OK') window.location.href = '/';
              else alert(response);
            })
            .catch(() => alert("Logowanie nie powiodło się"));
        });
      }

      if (type === 'formChangeSettings') {
        const changeSettingsForm = document.getElementById('changeSettingsForm');
        if (changeSettingsForm) {
          changeSettingsForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(changeSettingsForm);
            fetch('/PHP/updateProfile.php', { method: 'POST', body: formData })
              .then(res => res.text())
              .then(response => {
                if (response.trim() === 'OK') {
                  alert('Dane zaktualizowane pomyślnie!');
                  loadForm('settings');
                } else alert(response);
              })
              .catch(() => showMessageBox("Aktualizacja profilu nieudana."));
          });
          const cancelButton = changeSettingsForm.querySelector('.cancel-btn');
          if (cancelButton) cancelButton.addEventListener('click', () => loadForm('settings'));
        }
      }
      if (type === 'formChangePassword') {
        const changePasswordForm = document.getElementById('changePasswordForm');
        if (changePasswordForm) {
          const cancelBtn = changePasswordForm.querySelector('.cancel-btn');
          if (cancelBtn) {
            cancelBtn.addEventListener('click', () => loadForm('settings'));
          }

          changePasswordForm.addEventListener('submit', function(e) {
            e.preventDefault();

            const newPassword = changePasswordForm.newPassword.value;
            const confirmPassword = changePasswordForm.confirmPassword.value;

            if (newPassword !== confirmPassword) {
              alert('Nowe hasła nie są takie same!');
              return;
            }

            if (!changePasswordForm.checkValidity()) {
              changePasswordForm.reportValidity();
              return;
            }

            const formData = new FormData(changePasswordForm);
            fetch('/PHP/changePassword.php', { method: 'POST', body: formData })
              .then(res => res.text())
              .then(response => {
                if (response.trim() === 'OK') {
                  alert('Hasło zmienione pomyślnie!');
                  loadForm('settings');
                } else {
                  alert(response);
                }
              })
              .catch(() => alert('Błąd podczas zmiany hasła.'));
          });
        }
      }
    });
}

function closeDialog() {
  document.getElementById("popup").style.display = "none";
  loadForm('formLogin');
}

document.addEventListener("DOMContentLoaded", () => loadHeader());

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
    fetch('PHP/saveAddress.php', { method: 'POST', body: formData })
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
    fetch('PHP/saveAddress.php', { method: 'POST', body: formData })
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
  if (e.target.classList.contains('btn-edit') && e.target.hasAttribute('data-adresid')) {
    loadAddressForm(e.target.getAttribute('data-adresid'));
  }
  if (e.target.classList.contains('btn-delete') && e.target.hasAttribute('data-adresid')) {
    const adresId = e.target.getAttribute('data-adresid');
    if (confirm('Czy na pewno chcesz usunąć ten adres?')) {
      const formData = new FormData();
      formData.append('adres_id', adresId);
      fetch('PHP/deleteAddress.php', { method: 'POST', body: formData })
        .then(res => res.text())
        .then(response => {
          if (response.trim() === 'OK') {
            alert('Adres został usunięty.');
            loadForm('addresses');
          } else alert('Błąd: ' + response);
        })
        .catch(() => alert('Nie udało się usunąć adresu.'));
    }
  }
  if (e.target.classList.contains('btn-delete') && e.target.hasAttribute('data-paczkomatId')) {
    const paczkomatId = e.target.getAttribute('data-paczkomatId');
    if (confirm('Czy na pewno chcesz zmienić widoczność tego paczkomatu?')) {
      const formData = new FormData();
      formData.append('paczkomat_id', paczkomatId);
      fetch('PHP/deleteLocker.php', { method: 'POST', body: formData })
        .then(res => res.text())
        .then(response => {
          if (response.trim() === 'OK') {
            alert('Widoczność paczkomat została zmieniona.');
            loadView('packageLockerManagement');
          } else alert(response);
        })
        .catch(() => alert('Nie udało się zmienić widoczności paczkomatu.'));
    }
  }
  if (e.target.classList.contains('btn-edit') && e.target.hasAttribute('data-paczkomatId')) {
    loadLockerForm(e.target.getAttribute('data-paczkomatId'));
  }
  if (e.target.classList.contains('btn-edit-status')) {
    loadPackageStatusForm(e.target.getAttribute('data-paczkaid'));
  }
  if (e.target.classList.contains('btn-change-role') && e.target.hasAttribute('data-userid')) {
    loadChangeRoleForm(e.target.getAttribute('data-userid'));
  }
});

function initNavLinks() {
  document.querySelectorAll('.nav a[data-view]').forEach(link =>
    link.addEventListener('click', e => {
      e.preventDefault();
      loadView(link.getAttribute('data-view'));
    })
  );
}

function loadView(view) {
  fetch(`/templates/${view}.php`)
    .then(res => res.text())
    .then(html => {
      document.getElementById('content').innerHTML = html;
      if (view === 'formPackage') {
        const packageForm = document.getElementById('packageForm');
        if (packageForm) {
          packageForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(packageForm);
            fetch('/PHP/sendPackage.php', { method: 'POST', body: formData })
              .then(res => res.text())
              .then(response => {
                if (response.trim() === 'OK') {
                  alert("Nadano paczkę.");
                  loadView('myPackages');
                } else alert(response);
              })
              .catch(() => alert("Nadanie paczki nie powiodło się"));
          });
        }
        const addressSelect = document.getElementById('address');
        const typPrzesylki = document.getElementById('typ_przesyłki');
        if (addressSelect && typPrzesylki) {
          addressSelect.addEventListener('change', function() {
            typPrzesylki.value = this.options[this.selectedIndex].dataset.type || '';
          });
          typPrzesylki.value = addressSelect.options[addressSelect.selectedIndex]?.dataset.type || '';
        }
      }
    })
    .catch(() => alert('Nie udało się załadować widoku.'));
}

function loadPackageStatusForm(paczkaId) {
  fetch(`/templates/formPackageStatus.php?paczka_id=${paczkaId}`)
    .then(res => res.text())
    .then(html => {
      document.getElementById('content').innerHTML = html;
      initEditPackageStatusForm(paczkaId);
    })
    .catch(() => alert('Nie udało się załadować formularza edycji paczki.'));
}

function initEditPackageStatusForm(paczkaId) {
  const form = document.getElementById('packageStatusForm');
  if (!form) return;
  form.addEventListener('submit', e => {
    e.preventDefault();
    const formData = new FormData(form);
    formData.append('paczka_id', paczkaId);
    fetch('/PHP/savePackageStatus.php', { method: 'POST', body: formData })
      .then(res => res.text())
      .then(response => {
        if (response.trim() === 'OK') {
          alert('Status paczki zaktualizowany!');
          loadView('packageManagement');
        } else alert(response);
      })
      .catch(() => alert('Błąd przy zapisie statusu.'));
  });
}

function loadLockerForm(paczkomatId = null) {
  const url = paczkomatId ? `templates/formChangeLocker.php?paczkomat_id=${paczkomatId}` : 'templates/formChangeLocker.php';
  fetch(url)
    .then(res => res.text())
    .then(html => {
      document.getElementById('content').innerHTML = html;
      if (paczkomatId) initEditLockerForm(paczkomatId);
      else initAddLockerForm();
    })
    .catch(() => alert('Nie udało się załadować formularza paczkomatu.'));
}

function initEditLockerForm(paczkomatId) {
  const form = document.getElementById('changeLockerForm');
  if (!form) return;
  form.addEventListener('submit', e => {
    e.preventDefault();
    const formData = new FormData(form);
    formData.append('paczkomat_id', paczkomatId);
    fetch('PHP/saveLocker.php', { method: 'POST', body: formData })
      .then(res => res.text())
      .then(response => {
        if (response.trim() === 'OK') {
          alert('Paczkomat zaktualizowany pomyślnie!');
          loadForm('packageLockerManagement');
        } else alert(response);
      })
      .catch(() => alert('Aktualizacja paczkomatu nie powiodła się.'));
  });
}

function initAddLockerForm() {
  const form = document.getElementById('changeLockerForm');
  if (!form) return;
  form.addEventListener('submit', e => {
    e.preventDefault();
    const formData = new FormData(form);
    fetch('PHP/saveLocker.php', { method: 'POST', body: formData })
      .then(res => res.text())
      .then(response => {
        if (response.trim() === 'OK') {
          alert('Paczkomat dodany pomyślnie!');
          loadForm('packageLockerManagement');
        } else alert(response);
      })
      .catch(() => alert('Dodanie paczkomatu nie powiodło się.'));
  });
}

function loadChangeRoleForm(userId) {
  fetch(`/templates/formChangeRole.php?user_id=${userId}`)
    .then(res => res.text())
    .then(html => {
      document.getElementById('content').innerHTML = html;
      initChangeRoleForm(userId);
    })
    .catch(() => alert('Nie udało się załadować formularza zmiany roli.'));
}

function initChangeRoleForm(userId) {
  const form = document.getElementById('changeRoleForm');
  if (!form) return;
  form.addEventListener('submit', e => {
    e.preventDefault();
    const formData = new FormData(form);
    formData.append('user_id', userId);
    fetch('/PHP/changeUserRole.php', { method: 'POST', body: formData })
      .then(res => res.text())
      .then(response => {
        if (response.trim() === 'OK') {
          alert('Rola użytkownika zmieniona pomyślnie!');
          loadView('userManagement');
        } else alert(response);
      })
      .catch(() => alert('Zmiana roli użytkownika nie powiodła się.'));
  });
  const cancelButton = form.querySelector('.cancel-btn');
  if (cancelButton) cancelButton.addEventListener('click', () => loadView('userManagement'));
}