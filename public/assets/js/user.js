$(document).ready(() => {
    // button => filter-js
    // status select => status-user-js
    // FILTER THE USERS BY THE ROLE AND STATUS
    $('button#filter-js').click(() => {
        let $selectStatus = $('#status-user-js')
        let $selectRole = $('#select-user-role-js')
        let status = $selectStatus.select2('val')
        let role = $selectRole.select2('val')
        let $elements = $('.user-list-js.searched-email').length ? $('.user-list-js.searched-email') : $('.user-list-js')

        $elements.each((i, ele) => {
            $(ele).css('display', '')

            if (status !== 'any' && $(ele).find('.user-info').data('status') !== status) {
                if (role !== 'any' && $(ele).find('.user-info').data('role') !== role) {
                    $(ele).css('display', 'none')
                }
                $(ele).css('display', 'none')
            }
            else if (role !== 'any' && $(ele).find('.user-info').data('role') !== role) {
                $(ele).css('display', 'none')
            }

            if ($(ele).css('display') !== 'none') {
                $(ele).addClass('searched')
            }
        })
    })

    // SEARCH FOR A USER BY EMAIL
    // input ===> search-user-email-js
    $('input#search-user-email-js').on('keyup', () => {
        let $this = $('input#search-user-email-js')
        let $elements = $('.user-list-js.searched').length ? $('.user-list-js.searched') : $('.user-list-js')
        $elements.each((i, ele) => {
            let email = $(ele).find('.user-email').data('value')
            $(ele).css('display', '')
            $(ele).removeClass('searched-email')
            if (email.indexOf($this.val().toLowerCase()) < 0) {
                $(ele).css('display', 'none')
            }
            else {
                $(ele).addClass('searched-email')
            }
        })
    })

    $('#reset-filter-js').click((e) => {
        e.preventDefault()
        $('.user-list-js').each((i, ele) => {
            $(ele).css('display', '')
            $(ele).removeClass(['searched', 'searched-email'])
        })
    })
})