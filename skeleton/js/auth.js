// auth.js - Logic Login & Register

document.addEventListener('DOMContentLoaded', function() {
    
    // ===== FORM LOGIN =====
    const loginForm = document.getElementById('login-form');
    if (loginForm) {
        loginForm.addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const email = loginForm.email.value.trim();
            const password = loginForm.password.value;
            
            if (!email || !password) {
                showAlert('alert-box', 'error', 'Email dan password wajib diisi!');
                return;
            }
            
            const btn = loginForm.querySelector('button[type="submit"]');
            btn.disabled = true;
            btn.textContent = 'Memproses...';
            
            try {
                const result = await fetchAPI('/auth/login.php', 'POST', { email, password });
                
                if (result.status === 'success') {
                    localStorage.setItem('user_id', result.data.user_id);
                    localStorage.setItem('nama', result.data.nama || result.data.username);
                    localStorage.setItem('email', result.data.email);
                    
                    showAlert('alert-box', 'success', 'Login berhasil! Mengalihkan...');
                    setTimeout(function() {
                        window.location.href = 'index.html';
                    }, 1000);
                } else {
                    showAlert('alert-box', 'error', result.message || 'Login gagal');
                    btn.disabled = false;
                    btn.textContent = 'Masuk';
                }
            } catch (err) {
                showAlert('alert-box', 'error', 'Terjadi kesalahan. Coba lagi.');
                btn.disabled = false;
                btn.textContent = 'Masuk';
            }
        });
    }
    
    // ===== FORM REGISTER =====
    const registerForm = document.getElementById('register-form');
    if (registerForm) {
        registerForm.addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const email = registerForm.email.value.trim();
            const password = registerForm.password.value;
            const confirm = registerForm.confirm_password.value;
            
            if (!email || !password || !confirm) {
                showAlert('alert-box', 'error', 'Semua field wajib diisi!');
                return;
            }
            
            if (password !== confirm) {
                showAlert('alert-box', 'error', 'Password dan konfirmasi tidak sama!');
                return;
            }
            
            if (password.length < 6) {
                showAlert('alert-box', 'error', 'Password minimal 6 karakter!');
                return;
            }
            
            const btn = registerForm.querySelector('button[type="submit"]');
            btn.disabled = true;
            btn.textContent = 'Memproses...';
            
            try {
                const result = await fetchAPI('/auth/register.php', 'POST', {
                    email: email,
                    password: password
                });
                
                if (result.status === 'success') {
                    showAlert('alert-box', 'success', 'Registrasi berhasil! Silakan login.');
                    setTimeout(function() {
                        window.location.href = 'login.html';
                    }, 1500);
                } else {
                    showAlert('alert-box', 'error', result.message || 'Registrasi gagal');
                    btn.disabled = false;
                    btn.textContent = 'Daftar Akun';
                }
            } catch (err) {
                showAlert('alert-box', 'error', 'Terjadi kesalahan. Coba lagi.');
                btn.disabled = false;
                btn.textContent = 'Daftar Akun';
            }
        });
    }
    
});