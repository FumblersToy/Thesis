 // File input enhancement
        const fileInput = document.getElementById('profile_picture');
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

        // Genre and Instrument selection logic - prevent duplicates
        const genreSelects = ['genre', 'genre2', 'genre3'];
        const instrumentSelects = ['instrument', 'instrument2', 'instrument3'];
        
        function updateSelectOptions(selectIds) {
            const selectedValues = selectIds.map(id => document.getElementById(id).value).filter(v => v);
            
            selectIds.forEach(id => {
                const select = document.getElementById(id);
                const currentValue = select.value;
                const options = select.querySelectorAll('option');
                
                options.forEach(option => {
                    if (option.value === '' || option.value === currentValue) {
                        option.disabled = false;
                        option.style.display = '';
                    } else if (selectedValues.includes(option.value)) {
                        option.disabled = true;
                        option.style.display = 'none';
                    } else {
                        option.disabled = false;
                        option.style.display = '';
                    }
                });
            });
        }
        
        genreSelects.forEach(id => {
            document.getElementById(id).addEventListener('change', () => updateSelectOptions(genreSelects));
        });
        
        instrumentSelects.forEach(id => {
            document.getElementById(id).addEventListener('change', () => updateSelectOptions(instrumentSelects));
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