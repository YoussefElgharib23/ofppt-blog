import axios from "axios";

const checkCurrentMessageItem = () => {
    if (!$('.nk-msg-item .current').length) {

    }
}

$(document).ready(() => {

    const capitalize = str => str.substr(0, 1).toUpperCase() + str.substr(1, str.length);

    // Get the click event
    $('div.nk-msg-item').each(function () {
        let $this = $(this)
        $this.click(function () {
            let msgContext = $('.nk-msg-body'),
                msgItem = $('.msg-item')

            let idMsg = $this.data('msg-id')

            // Get data from the server with ajax and post method
            let url = window.location.origin + '/admin/api/contact-us-message'
            axios.post(url, {id: idMsg})
                .then(async (response) => {
                    let message = response.data.message,
                        deleteLink = response.data.delete

                    $('a#message-delete-link').attr('href', deleteLink)

                    // Get the description and the category
                    $('h4.title').html(message.description)
                    $('#category-msg').html(capitalize(message.category))
                    $('.user__name').each((i, ele) => {
                        if (i === 0) {
                            $(ele).html(message.twoLettersName)
                        }
                    })

                    $('.user-name').each((i, ele) => {
                        if (i === 0) {
                            $(ele).html(message.fullName)
                        }
                    })
                    $('.nk-reply-entry.entry').each((i, ele) => {
                        if (i === 0) {
                            $(ele).html(`<p>${message.details}</p>`)
                        }
                    })
                    $('.date-time').each((i, ele) => {
                        if (i === 0) {
                            $(ele).html(message.formattedCreatedAt)
                        }
                    })
                    $('.nb-attached-files').each((i, ele) => {
                        if (i === 0) {
                            $(ele).html('1');
                        }
                    })
                    $('.email').html(message.email)

                    $('.user-reply').remove()
                    $('.user-meta').remove()

                    if (message.replyContactUs) {
                        $('.nk-reply-form').empty()
                        $('.nk-reply-form').addClass('d-n');
                        console.log('right !!!');
                        $('.nk-reply-item').after(`
                            <div class="nk-reply-meta user-meta">
                                <div class="nk-reply-meta-info">
                                    <span class="who">${message.replyContactUs.user.__fullName}</span>
                                    replies at ${message.replyContactUs.formattedCreatedAt}
                                </div>
                            </div>
                            <!-- .nk-reply-meta -->
                            <div class="nk-reply-item user-reply">
                                <div class="nk-reply-header">
                                    <div class="user-card">
                                        <div class="user-avatar sm bg-blue">
                                            <span class="user__name">${message.replyContactUs.twoLatter}</span>
                                        </div>
                                        <div class="user-name">${message.replyContactUs.user.__fullName}</div>
                                    </div>
                                    <div class="date-time">${message.replyContactUs.minCreatedAt}</div>
                                </div>
                                <div class="nk-reply-body">
                                    <div class="nk-reply-entry entry">
                                        <p>
                                            ${message.replyContactUs.content}
                                        </p>
                                    </div>
                                </div>
                            </div><!-- .nk-reply-item -->
                        `)
                    }
                    else {
                        $('.nk-reply-form').removeClass('d-n');
                        console.log($(`meta[name='csrf-token']`).attr('content'));
                        $('.nk-reply-form').html(`
                            <div class="nk-reply-form-header">
                                <ul class="nav nav-tabs-s2 nav-tabs nav-tabs-sm">
                                    <li class="nav-item">
                                        <a class="nav-link active" data-toggle="tab"
                                           href="#reply-form">Reply</a>
                                    </li>
                                </ul>
                                <div class="nk-reply-form-title">
                                    <div class="title">Reply as:</div>
                                    <div class="user-avatar xs bg-purple">
                                        <span>${response.data.currentUser}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="tab-content">
                                <div class="tab-pane active" id="reply-form">
                                    <div class="nk-reply-form-editor">
                                        <form action="" class="form-validate" method="POST">
                                            <input type="hidden" name="_token"
                                                   value="${$('#reply-csrf').text()}">
                                            <input type="hidden" name="_msg-id"
                                                   value="${message.id}">
                                            <div class="nk-reply-form-field">
                                                <textarea
                                                        class="form-control form-control-simple no-resize"
                                                        name="reply"
                                                        placeholder="Hello"></textarea>
                                            </div>
                                            <div class="nk-reply-form-tools">
                                                <ul class="nk-reply-form-actions g-1">
                                                    <li class="mr-2">
                                                        <button class="btn btn-primary"
                                                                type="submit">Reply
                                                        </button>
                                                    </li>
                                                </ul>
                                            </div><!-- .nk-reply-form-tools -->
                                        </form>
                                    </div><!-- .nk-reply-form-editor -->
                                </div>
                            </div>
                        `)
                    }

                    $(`input[name=_msg-id]`).text(message.id)

                    let attachedFiles = $('.attach-files')
                    if (message.imageName === null) {
                        attachedFiles.addClass('d-n')
                        return
                    }

                    attachedFiles.removeClass('d-n')
                    $('.download').each((i, ele) => {
                        if (i === 0) {
                            $('a.attach-download:first').attr('href', message.imgTargetPath)
                            $(ele).attr('href', message.imgTargetPath)
                        }
                    })
                })
                .catch(async (error) => {
                    console.log(error.message)
                })
        })
    })

})