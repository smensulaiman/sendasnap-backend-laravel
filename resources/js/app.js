import './bootstrap';

// Global JS enhancements
document.addEventListener('DOMContentLoaded', () => {
    // Avatar preview (fallback if not provided inline)
    const input = document.querySelector('[data-avatar-input]') || document.getElementById('avatarInput');
    const preview = document.querySelector('[data-avatar-preview]') || document.getElementById('avatarPreview');
    if (input && preview) {
        input.addEventListener('change', function(){
            const file = this.files && this.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = e => preview.src = e.target.result;
                reader.readAsDataURL(file);
            }
        });
    }
});

