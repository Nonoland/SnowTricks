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

    //Input image
    for (let i = 1; i <= 3; i++) {
        const imageInput = form.querySelector(`#trick_image${i}`);
        const imageElement = form.querySelector(`.media.image${i} img`);
        const buttonEdit = form.querySelector(`.media.image${i} .edit`);
        const buttonRemove = form.querySelector(`.media.image${i} .remove`);
        buttonRemove.hidden = true;

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

            imageElement.src = "";
            imageInput.value = "";

            buttonRemove.hidden = true;
        });
    }
});
