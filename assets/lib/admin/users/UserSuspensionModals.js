$(document).ready(() => {

    $('a').each((i, ele) => {
        if ($(ele).data('toggle') === 'modal') {
            let $modal = $($(ele).data('target'))
            let $form = $modal.parents('form')
            $($form).removeClass('d-n')
        }
    })

})