import axios from "axios";

$('#like-post-js').click((e) => {
    let errorAlert = $('#error-panel-js');
    errorAlert.css('display', 'none')
    setTimeout(() => {
        errorAlert.removeClass('show')
    })
    let $this = $('#like-post-js')
    e.preventDefault()
    let postId = $this.data('target')
    let token = $(`meta[name="csrf-token"]`).attr('content')

    if (postId) {
        let url = window.location.origin + '/post/like/' + postId
        axios.post(url, { csrf:  token })
            .then((response) => {
                if (response.data.message) {
                    $('span#likes-count').text(response.data.likeCount)
                    if (response.data.likeType === '>1') {
                        $this.find('i').removeClass('fal')
                        $this.find('i').addClass('fas')
                    } else {
                        $this.find('i').addClass('fal')
                        $this.find('i').removeClass('fas')
                    }

                    if (response.data.isDisliked) {
                        $this.find('i.fa-thumbs-down').removeClass('fas')
                        $this.find('i.fa-thumbs-down').addClass('fal')
                        $('#dislike-post-js').find('i').addClass('fal')
                        $('#dislike-post-js').find('i').removeClass('fas')
                    }
                    $('span#dislikes-count').text(response.data.dislikeCount)
                    if(response.data.likeType === '>1') {
                        NioApp.Toast('You liked the post !', 'success')
                    }
                }
            })
            .catch((error) => {
                console.log(error)
                errorAlert.text('An error has occurred ! Please refresh your page !')
                errorAlert.css('display', '')
                setTimeout(() => {
                    errorAlert.addClass('show')
                }, 200)
                NioApp.Toast(error.message, 'error')
            })
    }
})