document.addEventListener('DOMContentLoaded', function() {
    const form = document.querySelector('.form-loader');
    const loaderCover = document.querySelector('.waiting-cover');
    const loader = document.querySelector('.waiting-loader');
    const submitButton = form.querySelector('.form-loader button[type="submit"]');

    form.addEventListener('submit', function(event) {
        loaderCover.style.visibility = 'visible';
        loader.style.display = 'block';
    });
});