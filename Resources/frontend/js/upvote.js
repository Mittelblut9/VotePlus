document.querySelectorAll('.entry--upvote_btn').forEach(div => {

    if(JSON.parse(div.dataset.voted)) {
        div.classList.add('entry--upvote_btn_active');
    }

    div.addEventListener('click', async function () {
        const hasUserAlreadyDownVoted = JSON.parse(document.getElementById(`entry--downvote_${this.dataset.voteid}`).dataset.voted);
        if(hasUserAlreadyDownVoted) {
            await sendToBackend({
                action: 'remove_down',
                voteId: this.dataset.voteid,
                url: this.parentElement.dataset.url
            }).then(res => {
                const oldCount = document.getElementById(`entry--voteCount_${this.dataset.voteid}`).innerHTML;
                document.getElementById(`entry--voteCount_${this.dataset.voteid}`).innerHTML = (Number(oldCount) + 1) + '';
                document.getElementById(`entry--downvote_${this.dataset.voteid}`).classList.toggle('entry--downvote_btn_active');
                document.getElementById(`entry--downvote_${this.dataset.voteid}`).dataset.voted = 'false';
            });
        }

        sendToBackend({
            action: this.dataset.voted === 'true' ? 'remove' : 'up',
            voteId: this.dataset.voteid,
            url: this.parentElement.dataset.url
        }).then(res => {
            const oldCount = document.getElementById(`entry--voteCount_${this.dataset.voteid}`).innerHTML;
            document.getElementById(`entry--voteCount_${this.dataset.voteid}`).innerHTML = this.dataset.voted === 'true' ? (Number(oldCount) - 1) : (Number(oldCount) + 1);
            this.classList.toggle('entry--upvote_btn_active');
            this.dataset.voted = !JSON.parse(this.dataset.voted);
        })
        .catch(() => {})
    });
})


document.querySelectorAll('.entry--downvote_btn').forEach(div => {

    if(JSON.parse(div.dataset.voted)) {
        div.classList.add('entry--downvote_btn_active');
    }

    div.addEventListener('click', async function () {
        const hasUserAlreadyUpVoted = JSON.parse(document.getElementById(`entry--upvote_${this.dataset.voteid}`).dataset.voted);
        if(hasUserAlreadyUpVoted) {
            await sendToBackend({
                action: 'remove',
                voteId: this.dataset.voteid,
                url: this.parentElement.dataset.url
            }).then(res => {
                const oldCount = document.getElementById(`entry--voteCount_${this.dataset.voteid}`).innerHTML;
                document.getElementById(`entry--voteCount_${this.dataset.voteid}`).innerHTML = (Number(oldCount) - 1) + '';
                document.getElementById(`entry--upvote_${this.dataset.voteid}`).classList.toggle('entry--upvote_btn_active');
                document.getElementById(`entry--upvote_${this.dataset.voteid}`).dataset.voted = 'false';
            });
        }

        sendToBackend({
           action: this.dataset.voted === 'true' ? 'remove_down' : 'down',
           voteId: this.dataset.voteid,
           url: this.parentElement.dataset.url
       }).then(res => {
           const oldCount = document.getElementById(`entry--voteCount_${this.dataset.voteid}`).innerHTML;
           document.getElementById(`entry--voteCount_${this.dataset.voteid}`).innerHTML = this.dataset.voted === 'true' ? (Number(oldCount) + 1) : (Number(oldCount) - 1);
           this.classList.toggle('entry--downvote_btn_active');
           this.dataset.voted = !JSON.parse(this.dataset.voted);
       })
       .catch(() => {})
    });
})


function sendToBackend({action, voteId, url}) {
     return new Promise((resolve, reject) => {
        const formData = new FormData()
        formData.set('action', action);
        formData.set('voteId', voteId);

        fetch(
            url, {
                headers: {
                    'X-CSRF-Token' :CSRF.getToken()
                },
                method: 'POST',
                body: formData
            }
        ).then(async res => {
            return await res;
        }).then(res => {
            switch (res.status) {
                case 403:
                    return reject('User is not logged-in');

                case 404:
                    return reject('Comment wasn\'t found');

                case 204:
                    return reject('User is Author of this Comment');

                case 200:
                    return resolve(res);
            }

        })
     })
}