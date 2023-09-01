document.addEventListener('DOMContentLoaded', (event) => {
    const form = document.querySelector('form[name=trick]');
    if (!form) {
        return;
    }

    const inputType = form.querySelector('input[name=type]');
    if (inputType.value !== "edit") {
        return;
    }

    //Chargement des images et embeds du trick
    const firstImageInput = document.getElementById("trick_firstImage");
    const imagesInput = document.getElementById("trick_images");
    const embedsInput = document.getElementById("trick_embeds");

    const imagesRemoveInput = document.getElementById("trick_removeImages");
    const embedsRemoveInput = document.getElementById("trick_removeEmbeds");
    const imagesRemove = [];
    const embedsRemove = [];

    const listMedias = document.querySelector('.list_media');
    const protoImage = document.querySelector(".media.image");
    const protoEmbed = document.querySelector(".media.embed");

    if (!imagesInput || !embedsInput) {
        return;
    }

    //Chargement de la première image
    document.querySelector('.first_image').src = firstImageInput.dataset.edit;

    //Chargement des médias
    const images = JSON.parse(imagesInput.dataset.images);
    const embeds = JSON.parse(embedsInput.dataset.embeds);

    images.forEach((image) => {
        const newImage = protoImage.cloneNode(true);
        newImage.querySelector('img').src = `/uploads/${image}`;
        newImage.hidden = false;
        newImage.dataset.image = image;

        newImage.querySelector("button.edit").addEventListener('click', (event) => {
            event.preventDefault();
            firstImageInput.click();
        });

        newImage.querySelector("button.remove").hidden = false;
        newImage.querySelector("button.remove").addEventListener('click', (event) => {
            imagesRemove.push(newImage.dataset.image);
            listMedias.removeChild(newImage);

            imagesRemoveInput.value = imagesRemove;
        });

        listMedias.appendChild(newImage);
    });

    embeds.forEach((embed) => {
        const newEmbed = protoEmbed.cloneNode(true);
        newEmbed.querySelector('.embed').innerHTML = embed;
        newEmbed.querySelector('.embed').hidden = false;
        newEmbed.hidden = false;
        newEmbed.dataset.embed = embed;

        newEmbed.querySelector("button.remove").hidden = false;
        newEmbed.querySelector("button.remove").addEventListener('click', (event) => {
            embedsRemove.push(newEmbed.dataset.embed);
            listMedias.removeChild(newEmbed);

            embedsRemoveInput.value = embedsRemove;
        });

        listMedias.appendChild(newEmbed);
    });

    //Gestion de la suppression des images et embeds
});