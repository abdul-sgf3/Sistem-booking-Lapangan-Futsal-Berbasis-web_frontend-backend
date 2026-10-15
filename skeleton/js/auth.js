document.addEventListener('DOMContentLoaded', () => {
    const loginForm = document.getElementById('login-form');
    if (loginForm) {
        loginForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            const email = loginForm.email.value;
            const password = loginForm.password.value;
            
            const result = await fetchAPI('/auth/login.php', 'POST', { email, password });
            
            if (result.status === 'success') {
                localStorage.setItem('user_id', result.data.user_id);
                localStorage.setItem('nama', result.data.nama);
                window.location.href = 'index.html';
            } else {
                showAlert('alert-box', 'error', result.message);
            }
        });
    }

    const registerForm = document.getElementById('register-form');
    if (registerForm) {
        registerForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            const data = {
                email: registerForm.email.value,
                password: registerForm.password.value
            };
            if (data.password !== registerForm.confirm_password.value) {
                showAlert('alert-box', 'error', 'Password tidak sama!');
                return;
            }
            const result = await fetchAPI('/auth/register.php', 'POST', data);
            if (result.status === 'success') {
                showAlert('alert-box', 'success', 'Registrasi berhasil!');
                setTimeout(() => window.location.href = 'login.html', 1500);
            } else {
                showAlert('alert-box', 'error', result.message);
            }
        });
    }
});