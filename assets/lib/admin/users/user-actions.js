import axios from "axios";

$(document).ready(() => {
    $('.apply-btn-js').each((i, ele) => {
        $(ele).click((e) => {
            e.preventDefault()
            let selectedAction = $('span#select-value-js').text()
            // GET THE VALUE
            if (selectedAction !== '') {
                enableOrDisableButton(ele, 'disable')
                let $users = $('.user-list-js'), $usersFilters = []

                $users.each((i, ele) => {
                    if ($(ele).find('.select-box-js').is(':checked')) {
                        $usersFilters.push($(ele).data('target'))
                    }
                })
                console.log($usersFilters)

                if ($usersFilters.length === 0) {
                    NioApp.Toast(
                        'You must select at least 1 user !',
                        'error'
                    )
                    enableOrDisableButton(ele)
                    return
                }

                console.log('foot !')

                let url = window.location.origin + '/api/admin/users/bulk'

                axios.post(url, {
                    users: $usersFilters,
                    action: selectedAction
                })
                .then((response) => {
                    Swal.fire(
                        "Good job !",
                        `The selected user status's set to ${response.data.status} status ! `,
                        "success"
                    )
                    .then((result) => {
                        if (result.isConfirmed) {
                            window.location.reload()
                        }
                    })
                })
                .catch((error) => {
                    console.log(error.message);
                })
                .then(() => {
                    enableOrDisableButton(ele)
                });
            }
            else {
                NioApp.Toast(
                    'You must select a valid action !',
                    'error'
                )
            }
        })
    })
})

function enableOrDisableButton(ele, type = 'enable') {
    let $btn = $(ele);
    if (type === 'disable') {
        $btn.attr('disabled', 'disabled')
        $btn.html('')
        $btn.html(
            `<div class="spinner-border spinner-border-sm" role="status">
                <span class="sr-only">Loading...</span>
            </div>`
        )
        return
    }
    if (!$btn.hasClass('btn-icon')) {
        $btn.text('Apply')
    }
    else {
        $btn.html(`
            <em class="icon ni ni-arrow-right"></em>
        `)
    }
    $btn.attr('disabled', false)
}