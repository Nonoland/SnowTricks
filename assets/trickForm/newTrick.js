import {Modal} from "bootstrap";

let images = {};
let embeds = {};
document.addEventListener('DOMContentLoaded', () => {
    const form = document.querySelector('form[name=trick]');
    if (!form) {
        return;
    }

    const inputTitle = form.querySelector('input[name="trick[title]"]');
    const firstTitle = inputTitle.value;
    inputTitle.addEventListener('keyup', (event) => {
        if (inputTitle.value === firstTitle) {
            return;
        }

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

    //Medias
    const addMedia = document.getElementById('addMedia');
    const divAddMedia = document.getElementById("addTrickMedia");
    const modalAddMedia = new Modal(divAddMedia);
    const inputFile = divAddMedia.querySelector("#mediaImage");
    const inputEmbed = divAddMedia.querySelector("#mediaEmbed");
    const inputSaveModal = divAddMedia.querySelector("#mediaSave");

    addMedia.addEventListener('click', (event) => {
        event.preventDefault();

        divAddMedia.querySelector("#mediaSave").dataset.type = "add";

        modalAddMedia.show();
    });

    inputSaveModal.addEventListener('click', async (event) => {
        event.preventDefault();

        const files = Array.from(inputFile.files);

        //Nouveau media
        if (inputSaveModal.dataset.type === "add" && files.length === 1) {
            const newImage = cloneImageMedia();
            newImage.querySelector("img").src = URL.createObjectURL(files[0]);
            newImage.querySelector("img").onload = function () {
                URL.revokeObjectURL(this.src);
            }

            newImage.hidden = false;

            let index = Object.keys(images).length;
            images[index] = await convertBase64(files[0]);

            newImage.querySelector('button.remove').hidden = false;
            newImage.querySelector('button.remove').dataset.index = index;
            newImage.querySelector('button.remove').addEventListener('click', (event) => {
                event.preventDefault();

                delete images[event.currentTarget.dataset.index];
                newImage.remove();
            });

            document.querySelector(".list_media").appendChild(newImage);
        } else if (inputSaveModal.dataset.type === "add" && inputEmbed.value.length > 0) {
            const newEmbed = cloneEmbedMedia();
            const divEmbed = newEmbed.querySelector(".embed");
            divEmbed.innerHTML = inputEmbed.value;
            divEmbed.hidden = false;
            newEmbed.hidden = false;

            let index = Object.keys(images).length;
            embeds[index] = inputEmbed.value;

            newEmbed.querySelector('button.remove').hidden = false;
            newEmbed.querySelector('button.remove').dataset.index = index;
            newEmbed.querySelector('button.remove').addEventListener('click', (event) => {
                event.preventDefault();

                delete embeds[event.currentTarget.dataset.index];
                newEmbed.remove();
            });

            document.querySelector(".list_media").appendChild(newEmbed);
        }

        modalAddMedia.hide();
        cleanModalAddMedia();
    });

    document.getElementById("trick_save").addEventListener("click", (event) => {
        event.preventDefault();

        const formTrick = document.querySelector("form[name=trick]");

        const type = formTrick.querySelector("input[name=type]").value;

        if (type === "add") {

        }

        formTrick.querySelector('#trick_images').value = JSON.stringify(images);
        formTrick.querySelector('#trick_embeds').value = JSON.stringify(embeds);

        formTrick.submit();
    });
});

function cloneImageMedia() {
    return document.querySelector(".media.image[hidden]").cloneNode(true);
}

function cloneEmbedMedia() {
    return document.querySelector(".media.embed[hidden]").cloneNode(true);
}

function cleanModalAddMedia() {
    const divAddMedia = document.getElementById("addTrickMedia");
    divAddMedia.querySelectorAll("input").forEach((input) => {
        input.value = null;
    });
    divAddMedia.querySelectorAll("textarea").forEach((input) => {
        input.value = null;
    });
}

async function convertBase64(file) {
    return new Promise((resolve, reject) => {
        const fileReader = new FileReader();
        fileReader.readAsDataURL(file);
        fileReader.onload = () => {
            resolve(fileReader.result);
        };
        fileReader.onerror = (error) => {
            reject(error);
        };
    });
}
