document.addEventListener('DOMContentLoaded', () => {
    const button = document.getElementById('medias_handle');
    if (!button) {
        return;
    }

    button.addEventListener('click', handleMediasHidden);
});

document.addEventListener('DOMContentLoaded', () => {
    const form = document.querySelector('form[name=trick]');
    if (!form) {
        return;
    }

    document.getElementById('medias_handle').addEventListener('click', handleMediasHidden);
});

function handleMediasHidden(event) {
    event.preventDefault();

    document.querySelector('.list_media').classList.remove('mobile-hide');
    event.currentTarget.hidden = true;
}
