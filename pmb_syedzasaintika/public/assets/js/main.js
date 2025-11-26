// Main JavaScript for PMB Syedza Saintika

// Function to refresh captcha
function refreshCaptcha() {
    fetch(BASE_URL + 'auth/generateCaptcha')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                document.getElementById('captcha-image').src = 
                    BASE_URL + 'auth/captchaImage/' + data.session_id + '?t=' + Date.now();
                // Update hidden field with new session ID
                document.getElementById('captcha_session_id').value = data.session_id;
            }
        })
        .catch(error => console.error('Error:', error));
}

// Event listener for captcha refresh button
document.addEventListener('DOMContentLoaded', function() {
    const refreshBtn = document.getElementById('btn-refresh-captcha');
    if (refreshBtn) {
        refreshBtn.addEventListener('click', function(e) {
            e.preventDefault();
            refreshCaptcha();
        });
    }
    
    // Dynamic province-city dropdown
    const provinsiSelect = document.getElementById('provinsi_id');
    const kabupatenSelect = document.getElementById('kabupaten_id');
    
    if (provinsiSelect && kabupatenSelect) {
        provinsiSelect.addEventListener('change', function() {
            const provinsiId = this.value;
            
            if (provinsiId) {
                fetch(BASE_URL + 'admin/getKabupatenByProvinsi?provinsi_id=' + provinsiId)
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            // Clear existing options
                            kabupatenSelect.innerHTML = '<option value="">Pilih Kabupaten/Kota</option>';
                            
                            // Add new options
                            data.data.forEach(kabupaten => {
                                const option = document.createElement('option');
                                option.value = kabupaten.id;
                                option.textContent = kabupaten.nama;
                                kabupatenSelect.appendChild(option);
                            });
                        }
                    })
                    .catch(error => console.error('Error:', error));
            } else {
                kabupatenSelect.innerHTML = '<option value="">Pilih Kabupaten/Kota</option>';
            }
        });
    }
});

// Form validation
function validateForm(formId) {
    const form = document.getElementById(formId);
    const inputs = form.querySelectorAll('input[required], select[required], textarea[required]');
    let isValid = true;
    
    inputs.forEach(input => {
        if (!input.value.trim()) {
            input.classList.add('is-invalid');
            isValid = false;
        } else {
            input.classList.remove('is-invalid');
        }
    });
    
    return isValid;
}