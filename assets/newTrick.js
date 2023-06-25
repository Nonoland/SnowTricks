import {Modal} from "bootstrap";
import button from "bootstrap/js/src/button";

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

    //Edit First Image
    const inputFirstImage = document.getElementById("trick_firstImage");
    const buttonEditFirstImage = form.querySelector('.actions_first_image .edit');
    const buttonRemoveFirstImage = form.querySelector('.actions_first_image .remove');
    const elementFirstImage = document.querySelector('img.first_image');
    buttonRemoveFirstImage.dataset.placeholder = elementFirstImage.src;

    inputFirstImage.addEventListener('change', (event) => {
        const files = event.target.files;
        const newFiles = Array.from(files);

        elementFirstImage.src = URL.createObjectURL(newFiles[0]);
        elementFirstImage.onload = function () {
            URL.revokeObjectURL(this.src);
        }

        buttonRemoveFirstImage.hidden = false;
    })

    buttonEditFirstImage.addEventListener('click', (event) => {
        event.preventDefault();

        inputFirstImage.click();
    });

    buttonRemoveFirstImage.addEventListener('click', (event) => {
        event.preventDefault();

        elementFirstImage.src = buttonRemoveFirstImage.dataset.placeholder;

        buttonRemoveFirstImage.hidden = true;
    });

    //Input image
    for (let i = 1; i <= 3; i++) {
        const imageInput = form.querySelector(`input[name="trick[image${i}]"]`);
        const imageElement = form.querySelector(`.media.image${i} img`);
        const buttonEdit = form.querySelector(`.media.image${i} .edit`);
        const buttonRemove = form.querySelector(`.media.image${i} .remove`);

        imageInput.addEventListener('change', (event) => {
            const files = event.target.files;
            const newFiles = Array.from(files);

            imageElement.src = URL.createObjectURL(newFiles[0]);
            imageElement.onload = function () {
                URL.revokeObjectURL(this.src);
            }

            buttonRemove.hidden = false;
        })

        buttonEdit.addEventListener('click', (event) => {
           event.preventDefault();

           imageInput.click();
        });

        buttonRemove.addEventListener('click', (event) => {
            event.preventDefault();

            imageElement.src = buttonRemove.dataset.placeholder;
            imageInput.value = "";

            buttonRemove.hidden = true;
        });
    }

    //Input media
    for (let i = 1; i <= 3; i++) {
        const mediaInput = form.querySelector(`input[name="trick[media${i}]"]`);
        const mediaElement = form.querySelector(`.media.media${i} img`);
        const mediaPreviewEmbed = form.querySelector(`.media.media${i} .embed`);
        const buttonEdit = form.querySelector(`.media.media${i} .edit`);
        const buttonRemove = form.querySelector(`.media.media${i} .remove`);

        mediaInput.addEventListener('change', (event) => {
            buttonRemove.hidden = false;
        })

        buttonEdit.addEventListener('click', (event) => {
            event.preventDefault();

            const modalElement = document.getElementById("addTrickMedia");
            const modalForm = modalElement.querySelector('form');
            const mediaEmbed = document.getElementById("mediaEmbed");
            const modal = new Modal(modalElement, {});

            const formSubmit = (event) => {
                event.preventDefault();

                mediaInput.value = mediaEmbed.value;

                if (mediaInput.value.length > 0) {
                    buttonRemove.hidden = false;

                    console.log(mediaPreviewEmbed);

                    mediaElement.hidden = true;
                    mediaPreviewEmbed.hidden = false;

                    mediaPreviewEmbed.innerHTML = mediaInput.value;
                }

                modal.hide();
            };

            modalForm.addEventListener('submit', formSubmit);

            modalElement.addEventListener('show.bs.modal', (event) => {
                mediaEmbed.value = mediaInput.value;
            });

            modalElement.addEventListener('hidden.bs.modal', (event) => {
                modalForm.removeEventListener('submit', formSubmit);
                mediaEmbed.value = "";
            });

            modal.show();
        });

        buttonRemove.addEventListener('click', (event) => {
            event.preventDefault();

            mediaInput.value = "";
            mediaElement.hidden = false;

            buttonRemove.hidden = true;
            mediaPreviewEmbed.hidden = true;
        });
    }
});
