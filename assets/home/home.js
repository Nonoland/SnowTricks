let throttleTimer;
const throttle = (callback, time) => {
    if (throttleTimer) return;
    throttleTimer = true;
    setTimeout(() => {
        callback();
        throttleTimer = false;
    }, time);
}

document.addEventListener('DOMContentLoaded', () => {
    const home = document.getElementById('home');
    if (!home) {
        return;
    }

    const loadMore = document.getElementById('load_more');
    if (!loadMore) {
        return;
    }

    loadMore.addEventListener('click', loadMoreHandle);

    checkIfButtonIsVisible();
});

window.addEventListener('scroll', () => {
    throttle(checkIfButtonIsVisible, 500);
});

function checkIfButtonIsVisible() {
    const button = document.getElementById('load_more');

    if (!button || button.disabled) {
        return;
    }

    const buttonRect = button.getBoundingClientRect();
    const windowHeight = window.innerHeight || document.documentElement.clientHeight;

    if (buttonRect.top >= 0 && buttonRect.bottom <= windowHeight) {
        if (!button.disabled)
            button.click();
    }
}

function loadMoreHandle(event) {
    const button = event.currentTarget;
    const tricks = document.querySelectorAll('.card[data-type=trick]');
    const lastTrick = tricks[tricks.length - 1];
    const lastId = lastTrick.dataset.id;
    const count = 10;

    button.disabled = true;

    fetch(`/ajaxGetTricks?lastId=${lastId}&count=${count}`, {
        method: 'GET',
        headers: {
            'Accept': 'application/json'
        }
    })
        .then(response => response.json())
        .then(handleFetchData(button))
        .catch(handleFetchError);
}

function handleFetchData(button) {
    return data => {
        if (!data.success) {
            console.error('Error during data recovery.');
            button.disabled = false;
            return;
        }

        const tricksHtml = data.data;
        const trickContainers = tricksHtml.map(createTrickContainer);
        const tricksList = document.getElementById('tricks_list');

        appendContainersToParent(trickContainers, tricksList);

        if (tricksHtml.length < data.targetCount) {
            button.hidden = true;
            button.disabled = true;
            return;
        }

        button.hidden = false;
        button.disabled = false;

    };
}

function createTrickContainer(trickHtml) {
    const div = document.createElement('div');
    div.className = 'col mb-4';
    div.innerHTML = trickHtml;
    return div;
}

function appendContainersToParent(containers, parent) {
    containers.forEach(container => {
        parent.appendChild(container);
    });
}

function handleFetchError(error) {
    console.error('AJAX request error :', error);
}
