import axios from 'axios'

$(document).ready(() => {
    let $btn = $('#mark-all-as-read')
    $btn.click((e) => {
        e.preventDefault()

        // Make a post call to the ajax route !
        let url = '/admin/api/notification/read'
        axios.post(url, {})
        .then((response) => {
            if (response.data.message) {
                $('#notification-container').addClass('show')
                let notificationContainer = $('.nk-notification')
                if (notificationContainer) {
                    notificationContainer.html(`
                        <div class="nk-notification-item dropdown-inner">
                            <div class="nk-notification-content text-center w-100">
                                <div class="nk-notification-text text-danger">
                                    No new notifications
                                </div>
                            </div>
                        </div>
                    `)
                }
            }
        })
        .catch((e) => {
            console.log(e.message)
        })
    })
})