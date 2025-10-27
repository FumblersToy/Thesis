// File input enhancement
        const fileInput = document.getElementById('profile_image');
        const fileLabel = document.getElementById('file-label');
        const fileText = document.getElementById('file-text');

        fileInput.addEventListener('change', function(e) {
            if (e.target.files.length > 0) {
                const fileName = e.target.files[0].name;
                fileText.textContent = fileName;
                fileLabel.classList.add('has-file');
            } else {
                fileText.textContent = 'Choose your logo';
                fileLabel.classList.remove('has-file');
            }
        });

        // Phone number formatting - FIXED VERSION
        const phoneInput = document.getElementById('phone');
        phoneInput.addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, '');
            
            // Ensure it starts with 9 for mobile numbers
            if (value.length > 0 && value[0] !== '9') {
                value = '9' + value.slice(0, 9);
            }
            
            // Limit to 10 digits total (not 8)
            value = value.slice(0, 10);
            
            // Format: 917 123 4567
            if (value.length > 6) {
                value = value.slice(0, 3) + ' ' + value.slice(3, 6) + ' ' + value.slice(6);
            } else if (value.length > 3) {
                value = value.slice(0, 3) + ' ' + value.slice(3);
            }
            
            e.target.value = value;
        });

        // Form validation
        const form = document.querySelector('form');
        const inputs = form.querySelectorAll('input[required], select[required]');

        inputs.forEach(input => {
            input.addEventListener('blur', function() {
                const formGroup = this.closest('.form-group');
                
                if (this.type === 'email') {
                    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                    if (this.value.trim() === '' || !emailRegex.test(this.value)) {
                        formGroup.classList.add('invalid');
                        formGroup.classList.remove('valid');
                    } else {
                        formGroup.classList.add('valid');
                        formGroup.classList.remove('invalid');
                    }
                } else if (this.type === 'tel') {
                    // Check if phone has exactly 10 digits starting with 9
                    const phoneDigits = this.value.replace(/\D/g, '');
                    if (this.value.trim() === '' || phoneDigits.length !== 10 || !phoneDigits.startsWith('9')) {
                        formGroup.classList.add('invalid');
                        formGroup.classList.remove('valid');
                    } else {
                        formGroup.classList.add('valid');
                        formGroup.classList.remove('invalid');
                    }
                } else {
                    if (this.value.trim() === '') {
                        formGroup.classList.add('invalid');
                        formGroup.classList.remove('valid');
                    } else {
                        formGroup.classList.add('valid');
                        formGroup.classList.remove('invalid');
                    }
                }
            });

            input.addEventListener('input', function() {
                const formGroup = this.closest('.form-group');
                if (this.value.trim() !== '') {
                    formGroup.classList.remove('invalid');
                }
            });
        });

        // Add custom validation for form submission
        form.addEventListener('submit', function(e) {
            const phoneDigits = phoneInput.value.replace(/\D/g, '');
            
            // Check if phone number is valid before submitting
            if (phoneDigits.length !== 10 || !phoneDigits.startsWith('9')) {
                e.preventDefault();
                const phoneGroup = phoneInput.closest('.form-group');
                phoneGroup.classList.add('invalid');
                phoneInput.focus();
                return false;
            }
            
            const submitBtn = document.querySelector('.submit-btn');
            submitBtn.textContent = 'Creating Profile...';
            submitBtn.style.opacity = '0.7';
            submitBtn.disabled = true;
        });