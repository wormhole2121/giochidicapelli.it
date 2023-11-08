document.addEventListener("DOMContentLoaded", function() {
    const toggleLinks = document.querySelectorAll('.toggle-link');

    toggleLinks.forEach(link => {
        link.addEventListener('click', function() {
            if (this.textContent === '+') {
                this.textContent = '-';
            } else {
                this.textContent = '+';
            }
        });
    });
});