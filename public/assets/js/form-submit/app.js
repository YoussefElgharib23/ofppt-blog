$('.actions-submit').each((i, $btn) => {
    $($btn).on('click', () => {
        let parent = $($btn).parents('form')
        let formInputs = $(parent).find('input')

        let filled = false
        let allInputFilled = true
        formInputs.each((i, ele) => {
            filled = $(ele).filter(function () {
                return $.trim($(this).val()).length === 0
            }).length === 0
            if(!filled) return false
        })

        let textAreas = parent.find('textarea')
        textAreas.each((i, textArea) => {
            if ( $(textArea).val().trim() === '' ) {
                filled = false
                return false
            }
        })
        $(parent).submit()

        console.log('submit 1')
        if (!filled) return false
        console.log('submit 2')

        $($btn).attr('disabled', true)
        $($btn).html(`
            <div class="spinner-border spinner-border-sm" role="status">
                <span class="sr-only">Loading...</span>
            </div>
        `)
    })
})