document.addEventListener('DOMContentLoaded', () => {
    const form = document.querySelector('form[name=trick]');
    if (!form) {
        return;
    }

    const inputTitle = form.querySelector('input[name="trick[title]"]');
    inputTitle.addEventListener('keyup', (event) => {
        fetch('/trick/verifyName/'+inputTitle.value, {
            method: 'GET'
        }).then((response) => {
            return response.json();
        }).then((response) => {
            if (!response.success) {
                inputTitle.classList.add('is-invalid');
                if (!inputTitle.nextElementSibling) {
                    const invalid = document.createElement('div');
                    invalid.classList.add('invalid-feedback');
                    invalid.textContent = "This name is already in use";
                    inputTitle.after(invalid);
                }
            } else {
                inputTitle.classList.remove('is-invalid');
                if (inputTitle.nextElementSibling) {
                    inputTitle.nextElementSibling.remove();
                }
            }
        });
    });

    let selectedFiles = [];
    const inputImage = form.querySelector('input#trick_trickImageMedia');
    const buttonAddMedia = form.querySelector('button.add_media');

    const templateImagePreview = form.querySelector('.media_row .media').cloneNode(true);
    templateImagePreview.hidden = false;
    form.querySelector('.media_row .media').remove();

    buttonAddMedia.addEventListener('click', (event) => {
        event.preventDefault();

        inputImage.click();
    });

    inputImage.addEventListener('change', (event) => {
        const files = event.target.files;
        const fileList = Array.from(files);

        fileList.forEach((file) => {
            const id = Date.now().toString() + Math.random().toString();
            selectedFiles[id] = file;
        });

        let medias = form.querySelectorAll('.media');
        medias.forEach((media) => {
            media.remove();
        });

        Object.keys(selectedFiles).forEach((id) => {
            const file = selectedFiles[id];

            const newImagePreview = templateImagePreview.cloneNode(true);
            newImagePreview.querySelector('img').src = URL.createObjectURL(file);
            newImagePreview.onload = () => {
                URL.revokeObjectURL(this.src);
            };

            newImagePreview.querySelector('.remove').addEventListener('click', (event) => {
                event.preventDefault();

                delete selectedFiles[id];
                newImagePreview.remove();
            })

            form.querySelector('.media_row').appendChild(newImagePreview);
        });
    });

    const buttonSubmit = form.querySelector('button[type="submit"]');
    buttonSubmit.addEventListener('click', (event) => {
        event.preventDefault();

        const formData = new FormData(form);

        selectedFiles.forEach((file, index) => {
            formData.append(`trick[trickImageMedia][${index}]`, file);
        });

        fetch(window.location, {
            method: 'POST',
            body: formData
        }).then((response) => {
            if (response.ok) {
                window.location = '/';
            } else {
                //Erreur
            }
        });
    });
});
