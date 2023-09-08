document.addEventListener('DOMContentLoaded', () => {
    const button = document.getElementById('medias_handle');
    if (!button) {
        return;
    }

    button.addEventListener('click', (event) => {
        event.preventDefault();

        document.querySelector('.list_media').classList.remove('mobile-hide');
        button.hidden = true;
    })
});
