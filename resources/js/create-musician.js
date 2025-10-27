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
                fileText.textContent = 'Choose a photo';
                fileLabel.classList.remove('has-file');
            }
        });

        // Character counter for bio
        const bioTextarea = document.getElementById('bio');
        const charCount = document.getElementById('char-count');

        bioTextarea.addEventListener('input', function() {
            const length = this.value.length;
            charCount.textContent = length;
            
            if (length > 450) {
                charCount.style.color = '#ef4444';
            } else if (length > 400) {
                charCount.style.color = '#f59e0b';
            } else {
                charCount.style.color = '#9ca3af';
            }
        });

        // Form validation enhancements
        const form = document.querySelector('form');
        const inputs = form.querySelectorAll('input[required], select[required], textarea[required]');

        inputs.forEach(input => {
            input.addEventListener('blur', function() {
                if (this.value.trim() === '') {
                    this.style.borderColor = '#ef4444';
                } else {
                    this.style.borderColor = '#10b981';
                }
            });

            input.addEventListener('input', function() {
                if (this.value.trim() !== '') {
                    this.style.borderColor = '#10b981';
                }
            });
        });

        // Smooth form submission
        form.addEventListener('submit', function(e) {
            const submitBtn = document.querySelector('.submit-btn');
            submitBtn.textContent = 'Creating Profile...';
            submitBtn.style.opacity = '0.7';
            submitBtn.disabled = true;
        });