import {Modal} from "bootstrap";
import {SnowAlert} from "./alert";

document.addEventListener('DOMContentLoaded', () => {
    const modalElement = document.getElementById('addTrickGroup');
    if (!modalElement) {
        return;
    }

    const modal = new Modal(modalElement, {});
    const formModal = modalElement.querySelector('form[name="trick_group"]');
    const urlTarget = formModal.getAttribute('action');

    formModal.onsubmit = (event) => {
        event.preventDefault();

        let formData = new FormData(formModal);
        let groupName = formData.get('trick_group[title]');

        fetch(urlTarget, {
            method: 'POST',
            body: formData
        }).then((response) => {
            return response.json();
        }).then((response) => {
            if (response.success) {
                const selectTrickGroup = document.getElementById('trick_trickGroup');
                const newOption = document.createElement('option');
                newOption.setAttribute('value', response.id);
                newOption.textContent = groupName;
                selectTrickGroup.appendChild(newOption);

                new SnowAlert("The new \""+groupName+"\" group has been added", "alert-success").show();
            } else {

                new SnowAlert("Error when adding new group", "alert-alert").show();
            }

            modal.toggle();
        });
    };
});
