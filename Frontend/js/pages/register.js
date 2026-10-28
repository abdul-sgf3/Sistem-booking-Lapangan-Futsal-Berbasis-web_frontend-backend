// js/pages/register.js
import { daftarAkun } from '../services/authService.js';

const emailInput = document.getElementById('email');
const passwordInput = document.getElementById('password');
const confirmInput = document.getElementById('confirm_password');
const btnSubmit = document.getElementById('btnSubmit');
const registerForm = document.getElementById('registerForm');
const alertBox = document.getElementById('alert-box');

const errEmailKosong = document.getElementById('err-email-kosong');
const errEmailFormat = document.getElementById('err-email-format');
const errEmailSpasi = document.getElementById('err-email-spasi');
const errEmailExists = document.getElementById('err-email-exists');
const errPwKosong = document.getElementById('err-pw-kosong');
const errPwMin = document.getElementById('err-pw-min');
const errPwKompleks = document.getElementById('err-pw-kompleks');
const errConfirmKosong = document.getElementById('err-confirm-kosong');
const errConfirmMatch = document.getElementById('err-confirm-match');

window.togglePassword = function(inputId, iconElement) {
    const input = document.getElementById(inputId);
    const iconClosed = iconElement.querySelector('.icon-eye-closed');
    const iconOpen = iconElement.querySelector('.icon-eye-open');
    if (input.type === 'password') { 
        input.type = 'text'; iconClosed.style.display = 'none'; iconOpen.style.display = 'block'; 
    } else { 
        input.type = 'password'; iconClosed.style.display = 'block'; iconOpen.style.display = 'none'; 
    }
}

function validateEmail() {
    emailInput.classList.remove('is-invalid');
    errEmailKosong.classList.remove('show');
    errEmailFormat.classList.remove('show');
    errEmailSpasi.classList.remove('show');
    errEmailExists.classList.remove('show');

    const val = emailInput.value;
    if (val === '') {
        emailInput.classList.add('is-invalid'); errEmailKosong.classList.add('show'); return false;
    }
    if (val.includes(' ')) {
        emailInput.classList.add('is-invalid'); errEmailSpasi.classList.add('show'); return false;
    }
    if (!val.includes('@')) {
        emailInput.classList.add('is-invalid'); errEmailFormat.classList.add('show'); return false;
    }
    return true;
}

function validatePassword() {
    passwordInput.classList.remove('is-invalid');
    errPwKosong.classList.remove('show');
    errPwMin.classList.remove('show');
    errPwKompleks.classList.remove('show');

    const val = passwordInput.value;
    if (val === '') {
        passwordInput.classList.add('is-invalid'); errPwKosong.classList.add('show'); return false;
    }
    if (val.length < 8) {
        passwordInput.classList.add('is-invalid'); errPwMin.classList.add('show'); return false;
    }
    
    const hasUppercase = /[A-Z]/.test(val);
    const hasLowercase = /[a-z]/.test(val);
    const hasNumber = /[0-9]/.test(val);
    if (!hasUppercase || !hasLowercase || !hasNumber) {
        passwordInput.classList.add('is-invalid'); errPwKompleks.classList.add('show'); return false;
    }
    return true;
}

function validateConfirm() {
    confirmInput.classList.remove('is-invalid');
    errConfirmKosong.classList.remove('show');
    errConfirmMatch.classList.remove('show');

    const val = confirmInput.value;
    if (val === '') {
        confirmInput.classList.add('is-invalid'); errConfirmKosong.classList.add('show'); return false;
    }
    if (val !== passwordInput.value) {
        confirmInput.classList.add('is-invalid'); errConfirmMatch.classList.add('show'); return false;
    }
    return true;
}

function cekTombolAktif() {
    const valEmail = emailInput.value;
    const valPw = passwordInput.value;
    const valConfirm = confirmInput.value;
    
    const isEmailValid = valEmail !== '' && !valEmail.includes(' ') && valEmail.includes('@');
    const hasUppercase = /[A-Z]/.test(valPw);
    const hasLowercase = /[a-z]/.test(valPw);
    const hasNumber = /[0-9]/.test(valPw);
    const isPassValid = valPw.length >= 8 && hasUppercase && hasLowercase && hasNumber;
    const isConfirmValid = valConfirm === valPw && valConfirm !== '';
    
    btnSubmit.disabled = !(isEmailValid && isPassValid && isConfirmValid);
}

emailInput.addEventListener('input', () => { validateEmail(); cekTombolAktif(); });
passwordInput.addEventListener('input', () => { validatePassword(); validateConfirm(); cekTombolAktif(); });
confirmInput.addEventListener('input', () => { validateConfirm(); cekTombolAktif(); });

registerForm.addEventListener('submit', async function(e) {
    e.preventDefault();
    if (btnSubmit.disabled) return;
    
    btnSubmit.disabled = true; 
    btnSubmit.textContent = 'Memproses...';
    alertBox.innerHTML = '';

    try {
        const responseText = await daftarAkun({ 
            email: emailInput.value, 
            password: passwordInput.value, 
            confirm_password: confirmInput.value 
        });

        let result;
        try {
            // Coba parse JSON dari backend
            result = JSON.parse(responseText);
            
            if (result.status === 'success') {
                // JIKA SUKSES: Tampilkan alert dan pindah ke verifikasi
                alertBox.innerHTML = `
                    <div class="vsq-alert vsq-alert-success">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#16A34A" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                            <polyline points="22 4 12 14.01 9 11.01"></polyline>
                        </svg>
                        Daftar Akun Berhasil! Lanjut verifikasi untuk mengaktifkan akun anda
                    </div>`;
                
                // Simpan email untuk halaman verifikasi
                localStorage.setItem('register_email', emailInput.value); 
                
                // Tunggu 1.5 detik lalu pindah ke verifikasi.html
                setTimeout(() => { window.location.href = 'verifikasi.html'; }, 1500);
            } else {
                // JIKA GAGAL DARI BACKEND (Misal: Email sudah ada)
                if (result.message && result.message.toLowerCase().includes('sudah terdaftar')) {
                    emailInput.classList.add('is-invalid');
                    errEmailExists.classList.add('show');
                } else {
                    alertBox.innerHTML = `<div class="vsq-alert vsq-alert-error">${result.message || 'Registrasi gagal'}</div>`;
                }
                btnSubmit.disabled = false; 
                btnSubmit.textContent = 'Daftar Akun';
            }
        } catch (parseError) {
            // JIKA RESPON BUKAN JSON (Server Error / Belum siap)
            // JANGAN BYPASS! Tampilkan error agar user tahu.
            console.error("Server error:", responseText);
            alertBox.innerHTML = `<div class="vsq-alert vsq-alert-error">Terjadi kesalahan pada server. Silakan coba lagi.</div>`;
            btnSubmit.disabled = false; 
            btnSubmit.textContent = 'Daftar Akun';
        }
    } catch (error) {
        // JIKA KONEKSI INTERNET PUTUS
        alertBox.innerHTML = `<div class="vsq-alert vsq-alert-error">Kesalahan jaringan internet Anda.</div>`;
        btnSubmit.disabled = false; 
        btnSubmit.textContent = 'Daftar Akun';
    }
});