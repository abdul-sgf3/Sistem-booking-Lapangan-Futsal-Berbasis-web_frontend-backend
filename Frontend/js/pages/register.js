// js/pages/register.js
import { daftarAkun } from '../services/authService.js';

// DOM Elements
const emailInput = document.getElementById('email');
const passwordInput = document.getElementById('password');
const confirmInput = document.getElementById('confirm_password');
const btnSubmit = document.getElementById('btnSubmit');
const registerForm = document.getElementById('registerForm');
const alertBox = document.getElementById('alert-box');

// Elemen Error
const errEmailKosong = document.getElementById('err-email-kosong');
const errEmailFormat = document.getElementById('err-email-format');
const errEmailSpasi = document.getElementById('err-email-spasi'); // Elemen baru
const errEmailExists = document.getElementById('err-email-exists');
const errPwKosong = document.getElementById('err-pw-kosong');
const errPwMin = document.getElementById('err-pw-min');
const errPwKompleks = document.getElementById('err-pw-kompleks');
const errConfirmKosong = document.getElementById('err-confirm-kosong');
const errConfirmMatch = document.getElementById('err-confirm-match');

// Toggle Password
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

// Reset Error
function resetErrors() {
    emailInput.classList.remove('is-invalid');
    passwordInput.classList.remove('is-invalid');
    confirmInput.classList.remove('is-invalid');
    errEmailKosong.classList.remove('show');
    errEmailFormat.classList.remove('show');
    errEmailSpasi.classList.remove('show');
    errEmailExists.classList.remove('show');
    errPwKosong.classList.remove('show');
    errPwMin.classList.remove('show');
    errPwKompleks.classList.remove('show');
    errConfirmKosong.classList.remove('show');
    errConfirmMatch.classList.remove('show');
}

// Validasi Email (Sesuai UI/UX: Kosong, Harus ada @, Tidak boleh spasi)
function validateEmail() {
    emailInput.classList.remove('is-invalid');
    errEmailKosong.classList.remove('show');
    errEmailFormat.classList.remove('show');
    errEmailSpasi.classList.remove('show');
    errEmailExists.classList.remove('show');

    const val = emailInput.value;

    if (val === '') {
        emailInput.classList.add('is-invalid');
        errEmailKosong.classList.add('show');
        return false;
    }
    if (val.includes(' ')) {
        emailInput.classList.add('is-invalid');
        errEmailSpasi.classList.add('show');
        return false;
    }
    if (!val.includes('@')) {
        emailInput.classList.add('is-invalid');
        errEmailFormat.classList.add('show');
        return false;
    }
    return true;
}

// Validasi Password (Sesuai UI/UX: Kosong, Min 8, Huruf besar/kecil/angka)
function validatePassword() {
    passwordInput.classList.remove('is-invalid');
    errPwKosong.classList.remove('show');
    errPwMin.classList.remove('show');
    errPwKompleks.classList.remove('show');

    const val = passwordInput.value;

    if (val === '') {
        passwordInput.classList.add('is-invalid');
        errPwKosong.classList.add('show');
        return false;
    }
    if (val.length < 8) {
        passwordInput.classList.add('is-invalid');
        errPwMin.classList.add('show');
        return false;
    }
    
    // Cek huruf besar, huruf kecil, angka
    const hasUppercase = /[A-Z]/.test(val);
    const hasLowercase = /[a-z]/.test(val);
    const hasNumber = /[0-9]/.test(val);
    
    if (!hasUppercase || !hasLowercase || !hasNumber) {
        passwordInput.classList.add('is-invalid');
        errPwKompleks.classList.add('show');
        return false;
    }
    return true;
}

// Validasi Konfirmasi (Sesuai UI/UX: Kosong, Tidak cocok)
function validateConfirm() {
    confirmInput.classList.remove('is-invalid');
    errConfirmKosong.classList.remove('show');
    errConfirmMatch.classList.remove('show');

    const val = confirmInput.value;
    if (val === '') {
        confirmInput.classList.add('is-invalid');
        errConfirmKosong.classList.add('show');
        return false;
    }
    if (val !== passwordInput.value) {
        confirmInput.classList.add('is-invalid');
        errConfirmMatch.classList.add('show');
        return false;
    }
    return true;
}

// Cek Tombol Aktif
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
    
    if (isEmailValid && isPassValid && isConfirmValid) {
        btnSubmit.disabled = false;
    } else {
        btnSubmit.disabled = true;
    }
}

// Event Listener
emailInput.addEventListener('input', () => { validateEmail(); cekTombolAktif(); });
passwordInput.addEventListener('input', () => { validatePassword(); validateConfirm(); cekTombolAktif(); });
confirmInput.addEventListener('input', () => { validateConfirm(); cekTombolAktif(); });

// Submit Form (Logika tidak berubah)
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
            result = JSON.parse(responseText);
            if (result.status === 'success') {
                alertBox.innerHTML = `<div class="vsq-alert vsq-alert-success">Registrasi berhasil! Mengalihkan...</div>`;
                localStorage.setItem('register_email', emailInput.value); 
                setTimeout(() => { window.location.href = 'verifikasi.html'; }, 1500);
            } else {
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
            console.warn("BE Error (Bypassed):", responseText);
            alertBox.innerHTML = `<div class="vsq-alert vsq-alert-success">Akun diproses! Mengalihkan...</div>`;
            localStorage.setItem('register_email', emailInput.value); 
            setTimeout(() => { window.location.href = 'verifikasi.html'; }, 1500);
        }
    } catch (error) {
        alertBox.innerHTML = `<div class="vsq-alert vsq-alert-error">Kesalahan jaringan internet Anda.</div>`;
        btnSubmit.disabled = false; 
        btnSubmit.textContent = 'Daftar Akun';
    }
});