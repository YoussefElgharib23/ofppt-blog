import axios from "axios";
$('#dislike-post-js').click((e) => {
    e.preventDefault()
    let $this = $('#dislike-post-js')
    let postId = $this.data('target')
    let url = window.location.origin + '/post/dislike/' + postId;
    axios.post(url, { csrf: $(`meta[name="csrf-token"]`).attr('content') })
        .then((response) => {
            if (response.data.message) {
                $('span#dislikes-count').text(response.data.dislikeCount)
                if (response.data.dislikeType === '>1') {
                    $this.find('i').removeClass('fal')
                    $this.find('i').addClass('fas')
                }
                else {
                    $this.find('i').addClass('fal')
                    $this.find('i').removeClass('fas')
                }
                if (response.data.isLiked) {
                    let dislikeIcon = $('#like-post-js')
                    dislikeIcon.find('i').addClass('fal')
                    dislikeIcon.find('i').removeClass('fas')
                }
                $('span#likes-count').text(response.data.likeCount)
                NioApp.Toast('You disliked the post !', 'success')
            }
        })
        .catch((error) => {
            console.log(error);
            NioApp.Toast(error.message, error.message)
        })
})