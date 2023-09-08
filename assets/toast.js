import {Toast} from "bootstrap";

document.addEventListener('DOMContentLoaded', () => {
    const toast = getCookie("toast");
    if (!toast) {
        return;
    }

    const toastElement = document.getElementById("toast");
    if (!toastElement) {
        return;
    }

    toastElement.querySelector(".toast-body").textContent = toast;
    const toastBootstrap = new Toast(toastElement, {'delay' : 10000});
    toastBootstrap.show();

    document.cookie = "toast= ; expires = Thu, 01 Jan 1970 00:00:00 GMT"
});

function getCookie(name) {
    const cookies = document.cookie.split(';');
    for(let i = 0; i < cookies.length; i++) {
        let cookieData = cookies[i].split('=');
        if (name === cookieData[0].trim()) {
            return decodeURIComponent(cookieData[1])
        }
    }

    return null;
}

function removeCookie(name) {
    document.cookie = `${name}= ; expires = Thu, 01 Jan 1970 00:00:00 GMT`
}
