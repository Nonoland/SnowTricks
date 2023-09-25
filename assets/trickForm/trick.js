document.addEventListener('DOMContentLoaded', () => {
    loadMediasHandle();
    loadComments();
});

function loadMediasHandle() {
    const button = document.getElementById('medias_handle');
    if (!button) {
        return;
    }

    button.addEventListener('click', handleMediasHidden);
}

function handleMediasHidden(event) {
    event.preventDefault();

    document.querySelector('.list_media').classList.remove('mobile-hide');
    event.currentTarget.hidden = true;
}

function loadComments() {
    const buttonLoadMore = document.getElementById('load_more_comments');
    if (!buttonLoadMore) {
        return;
    }

    buttonLoadMore.addEventListener('click', (event) => {
        event.preventDefault();

        const comments = document.querySelectorAll('.comment');
        const lastComment = comments[comments.length - 1];
        const lastId = lastComment.dataset.commentId;
        console.log(lastId);
        const count = 5;

        buttonLoadMore.disabled = true;
        buttonLoadMore.querySelector('.spinner').hidden = false;

        fetch(`/ajaxGetTrickComments?lastId=${lastId}&count=${count}`, {
            method: 'GET',
            headers: {
                'Accept': 'application/json'
            }
        })
            .then(response => response.json())
            .then((data) => {
                if (!data.success) {
                    buttonLoadMore.disabled = false;
                    console.error('Error during data recovery.');
                    return;
                }

                const commentList = document.querySelector('.comments_list');

                const commentsHtml = data.data;
                commentsHtml.forEach((commentData) => {
                    let cache = document.createElement('div');
                    cache.innerHTML = commentData;
                    const commentHTML = cache.firstElementChild;

                    commentList.appendChild(commentHTML);
                });

                buttonLoadMore.disabled = false;
                buttonLoadMore.querySelector('.spinner').hidden = true;

                if (!data.isMoreResults) {
                    buttonLoadMore.hidden = true;
                }
            });
    })
}
