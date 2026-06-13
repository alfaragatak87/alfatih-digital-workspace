</div>
    </div>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            
            // ===================================================
            // SIDEBAR TOGGLE (dengan state tersimpan)
            // ===================================================
            const sidebarToggle = document.getElementById('sidebartoggle');
            const adminContainer = document.querySelector('.admin-container');
            
            if (sidebarToggle && adminContainer) {
                // Cek state sidebar yang tersimpan di browser
                const isSidebarCollapsed = localStorage.getItem('sidebarCollapsed') === 'true';
                if (isSidebarCollapsed) {
                    adminContainer.classList.add('sidebar-collapsed');
                }

                sidebarToggle.addEventListener('click', function() {
                    adminContainer.classList.toggle('sidebar-collapsed');
                    // Simpan state setiap kali di-klik
                    localStorage.setItem('sidebarCollapsed', adminContainer.classList.contains('sidebar-collapsed'));
                });
            }

            // ===================================================
            // FILE INPUT PREVIEW
            // ===================================================
            const fileInputs = document.querySelectorAll('.custom-file-input');
            fileInputs.forEach(input => {
                input.addEventListener('change', function() {
                    const fileName = this.files[0]?.name || 'Choose file';
                    const fileLabel = this.nextElementSibling;
                    
                    if (fileLabel) {
                        fileLabel.textContent = fileName;
                    }
                    
                    const previewContainer = this.closest('.form-group')?.querySelector('.image-preview');
                    if (previewContainer && this.files[0] && this.files[0].type.startsWith('image/')) {
                        const reader = new FileReader();
                        
                        reader.onload = function(e) {
                            previewContainer.innerHTML = `<img src="${e.target.result}" alt="Preview">`;
                        }
                        
                        reader.readAsDataURL(this.files[0]);
                    }
                });
            });

            // ===================================================
            // DROPDOWN MENU SIDEBAR
            // ===================================================
            const dropdownToggles = document.querySelectorAll('.sidebar-dropdown .dropdown-toggle');
            dropdownToggles.forEach(toggle => {
                // Check if dropdown should be open (if its link is active)
                const hasActiveLink = toggle.classList.contains('active') || 
                                     toggle.nextElementSibling.querySelector('.active');
                
                if (hasActiveLink) {
                    toggle.nextElementSibling.style.display = 'block';
                    toggle.querySelector('.dropdown-icon')?.classList.add('fa-rotate-180');
                }
                
                toggle.addEventListener('click', function(e) {
                    e.preventDefault();
                    const submenu = this.nextElementSibling;
                    const icon = this.querySelector('.dropdown-icon');

                    if (submenu.style.display === 'block') {
                        submenu.style.display = 'none';
                        icon?.classList.remove('fa-rotate-180');
                    } else {
                        // Tutup dropdown lain sebelum membuka yang ini
                        document.querySelectorAll('.sidebar-submenu').forEach(otherSubmenu => {
                            otherSubmenu.style.display = 'none';
                            otherSubmenu.previousElementSibling.querySelector('.dropdown-icon')?.classList.remove('fa-rotate-180');
                        });
                        submenu.style.display = 'block';
                        icon?.classList.add('fa-rotate-180');
                    }
                });
            });

            // ===================================================
            // DROPDOWN AVATAR ADMIN
            // ===================================================
            const adminAvatarContainer = document.querySelector('.admin-avatar-container');
            if (adminAvatarContainer) {
                adminAvatarContainer.addEventListener('click', function(e) {
                    // Mencegah event bubbling ke document
                    e.stopPropagation();
                    this.classList.toggle('active');
                });

                // Tutup dropdown jika klik di luar
                document.addEventListener('click', function(e) {
                    if (!adminAvatarContainer.contains(e.target)) {
                        adminAvatarContainer.classList.remove('active');
                    }
                });
            }
            
            // ===================================================
            // TEMA WARNA CUSTOM
            // ===================================================
            const themeColorPicker = document.getElementById('themeColorPicker');
            if (themeColorPicker) {
                themeColorPicker.addEventListener('change', function() {
                    const newColor = this.value;
                    // Perbarui warna tema di root CSS
                    document.documentElement.style.setProperty('--primary-color', newColor);
                    
                    // Hitung warna gelap
                    const rgbColor = hexToRgb(newColor);
                    if (rgbColor) {
                        const darkerColor = `rgb(${Math.max(0, rgbColor.r - 20)}, ${Math.max(0, rgbColor.g - 20)}, ${Math.max(0, rgbColor.b - 20)})`;
                        document.documentElement.style.setProperty('--primary-dark', darkerColor);
                    }
                    
                    // Simpan ke database melalui AJAX
                    saveThemeColor(newColor);
                });
            }
            
            // Fungsi untuk mengkonversi hex ke rgb
            function hexToRgb(hex) {
                const result = /^#?([a-f\d]{2})([a-f\d]{2})([a-f\d]{2})$/i.exec(hex);
                return result ? {
                    r: parseInt(result[1], 16),
                    g: parseInt(result[2], 16),
                    b: parseInt(result[3], 16)
                } : null;
            }
            
            // Fungsi untuk menyimpan warna tema ke database
            function saveThemeColor(color) {
                const xhr = new XMLHttpRequest();
                xhr.open('POST', '<?= BASE_URL ?>/admin/save_theme.php', true);
                xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
                xhr.onreadystatechange = function() {
                    if (xhr.readyState === 4 && xhr.status === 200) {
                        // Berhasil disimpan
                        console.log('Theme color saved successfully');
                    }
                };
                xhr.send('color=' + encodeURIComponent(color));
            }
            
            // ===================================================
            // LOADING OVERLAY
            // ===================================================
            // Tambahkan overlay loading saat submit form
            const forms = document.querySelectorAll('form:not(.no-loading)');
            forms.forEach(form => {
                form.addEventListener('submit', function() {
                    // Hanya tampilkan loading jika form valid
                    if (this.checkValidity()) {
                        showLoading();
                    }
                });
            });
            
            // Fungsi untuk menampilkan loading overlay
            window.showLoading = function(text = 'Processing...') {
                // Buat loading overlay jika belum ada
                let overlay = document.getElementById('loadingOverlay');
                if (!overlay) {
                    overlay = document.createElement('div');
                    overlay.id = 'loadingOverlay';
                    overlay.className = 'loading-overlay';
                    
                    const spinner = document.createElement('div');
                    spinner.className = 'spinner';
                    
                    const loadingText = document.createElement('div');
                    loadingText.className = 'loading-text';
                    loadingText.textContent = text;
                    
                    overlay.appendChild(spinner);
                    overlay.appendChild(loadingText);
                    document.body.appendChild(overlay);
                } else {
                    // Update text jika overlay sudah ada
                    overlay.querySelector('.loading-text').textContent = text;
                    overlay.style.display = 'flex';
                }
            };
            
            // Fungsi untuk menyembunyikan loading overlay
            window.hideLoading = function() {
                const overlay = document.getElementById('loadingOverlay');
                if (overlay) {
                    overlay.style.display = 'none';
                }
            };
            
            // ===================================================
            // KONFIRMASI HAPUS
            // ===================================================
            const deleteButtons = document.querySelectorAll('.delete-btn, [data-confirm]');
            deleteButtons.forEach(button => {
                button.addEventListener('click', function(e) {
                    const message = this.getAttribute('data-confirm') || 'Apakah Anda yakin ingin menghapus item ini?';
                    if (!confirm(message)) {
                        e.preventDefault();
                        return false;
                    }
                });
            });
            
            // ===================================================
            // CHART.JS SETUP (jika tersedia)
            // ===================================================
            if (window.Chart) {
                // Set tema gelap untuk Chart.js
                Chart.defaults.color = '#b3b3b3';
                Chart.defaults.scale.grid.color = 'rgba(255, 255, 255, 0.05)';
            }
            
        });
    </script>
</body>
</html>